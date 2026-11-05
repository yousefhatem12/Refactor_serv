#!/usr/bin/env bash
# ==============================================================================
# Serv5 Bootstrap Script - Optimized for Fedora / RHEL & Debian / Ubuntu VMs
# Stack: Next.js 16 (Port 80) + Laravel 12 / Filament (Port 8000) + MariaDB/MySQL + Redis
# ==============================================================================

set -euo pipefail

# ------------------------------------------------------------------------------
# 1. Path Auto-Detection & Configuration
# ------------------------------------------------------------------------------
APP_NAME="serv5"
GIT_REPO_URL="https://github.com/yousefhatem12/Refactor_serv.git"
GIT_BRANCH="main"

# Auto-detect if executed from within cloned repository
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_CANDIDATE="$(cd "${SCRIPT_DIR}/.." && pwd)"

if [ -f "${REPO_CANDIDATE}/package.json" ] && [ -f "${REPO_CANDIDATE}/backend/artisan" ]; then
    APP_DIR="${REPO_CANDIDATE}"
else
    APP_DIR="/var/www/${APP_NAME}"
fi

# Detect actual non-root invoking user (for home directory permissions)
RUNNING_USER="${SUDO_USER:-$(id -un)}"
USER_HOME=$(eval echo "~${RUNNING_USER}")

# Auto-detect Server IP (works on local network or public VPS)
SERVER_IP=$(curl -s -m 3 ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')
[ -z "$SERVER_IP" ] && SERVER_IP="127.0.0.1"

# Ports
FRONTEND_PORT="80"
BACKEND_PORT="8000"

# Runtimes
NODE_VERSION="22"

# Database & Admin credentials
DB_NAME="serv5"
DB_USER="serv5_user"

# Reuse existing DB password from backend/.env if it already exists
if [ -f "${APP_DIR}/backend/.env" ]; then
    EXISTING_DB_PASS=$(grep -E '^DB_PASSWORD=' "${APP_DIR}/backend/.env" | head -n1 | cut -d'=' -f2- | tr -d '"' | tr -d "'" || true)
    if [ -n "${EXISTING_DB_PASS}" ]; then
        DB_PASSWORD="${EXISTING_DB_PASS}"
    fi
fi
DB_PASSWORD="${DB_PASSWORD:-$(openssl rand -hex 12)}"
ADMIN_EMAIL="admin@${SERVER_IP}.local"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-$(openssl rand -base64 12)}"

# Color helpers
C_RESET="\033[0m"
C_GREEN="\033[1;32m"
C_BLUE="\033[1;34m"
C_YELLOW="\033[1;33m"
C_RED="\033[1;31m"

log_info()    { echo -e "${C_BLUE}[INFO]${C_RESET} $*"; }
log_success() { echo -e "${C_GREEN}[SUCCESS]${C_RESET} $*"; }
log_warn()    { echo -e "${C_YELLOW}[WARN]${C_RESET} $*"; }
log_error()   { echo -e "${C_RED}[ERROR]${C_RESET} $*"; }

if [[ $EUID -ne 0 ]]; then
   log_error "This script must be run with root privileges (sudo ./bootstrap.sh)."
   exit 1
fi

log_info "Target Application Directory: ${APP_DIR}"
log_info "Detected Server IP: ${SERVER_IP}"

# ------------------------------------------------------------------------------
# 2. OS & Package Manager Detection
# ------------------------------------------------------------------------------
if command -v dnf >/dev/null 2>&1; then
    PKG_MGR="dnf"
    WEB_USER="nginx"
    WEB_GROUP="nginx"
    PHP_FPM_SOCK="/run/php-fpm/www.sock"
    NGINX_CONF_DIR="/etc/nginx/conf.d"
elif command -v apt-get >/dev/null 2>&1; then
    PKG_MGR="apt"
    WEB_USER="www-data"
    WEB_GROUP="www-data"
    PHP_FPM_SOCK="/run/php/php8.3-fpm.sock"
    NGINX_CONF_DIR="/etc/nginx/sites-available"
else
    log_error "Unsupported package manager. Requires dnf (Fedora/RHEL) or apt (Ubuntu/Debian)."
    exit 1
fi
log_info "Detected Package Manager: ${PKG_MGR} (Web User: ${WEB_USER})"

# ------------------------------------------------------------------------------
# 3. Virtual Server Swap Allocation (CRITICAL for Next.js builds on low-RAM VMs)
# ------------------------------------------------------------------------------
log_info "Checking virtual server memory and swap..."
TOTAL_RAM_MB=$(free -m | awk '/^Mem:/{print $2}')
SWAP_EXISTS=$(free -m | awk '/^Swap:/{print $2}')

if [ "$SWAP_EXISTS" -lt 1024 ]; then
    log_warn "Virtual server has low swap. Creating 2GB swapfile..."
    fallocate -l 2G /swapfile || dd if=/dev/zero of=/swapfile bs=1M count=2048
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    if ! grep -q '/swapfile' /etc/fstab; then
        echo '/swapfile none swap sw 0 0' >> /etc/fstab
    fi
    sysctl vm.swappiness=10
    echo 'vm.swappiness=10' > /etc/sysctl.d/99-vps-swap.conf
    log_success "2GB Swap activated."
else
    log_info "Sufficient swap already configured (${SWAP_EXISTS}MB)."
fi

# ------------------------------------------------------------------------------
# 4. Install Base Packages, Node.js, PHP, Nginx, MariaDB & Redis
# ------------------------------------------------------------------------------
log_info "Installing packages using ${PKG_MGR}..."

if [ "$PKG_MGR" = "dnf" ]; then
    # Fedora / RHEL
    dnf install -y curl wget git unzip zip tar ca-certificates policycoreutils-python-utils \
        nginx redis mariadb-server mariadb gcc-c++ make

    # Node.js 22 LTS via NodeSource RPM or native
    if ! command -v node >/dev/null 2>&1 || [[ $(node -v | cut -d'.' -f1 | tr -d 'v') -lt 20 ]]; then
        log_info "Setting up Node.js 22.x..."
        curl -fsSL https://rpm.nodesource.com/setup_${NODE_VERSION}.x | bash -
        dnf install -y nodejs
    fi

    # PHP and extensions (Fedora has PHP 8.3/8.4 in native repos)
    log_info "Installing PHP and extensions..."
    dnf install -y php-cli php-fpm php-mysqlnd php-mbstring php-xml php-curl php-zip \
        php-gd php-intl php-bcmath php-sqlite3 php-pecl-redis5 php-opcache php-json composer || \
    dnf install -y php php-fpm php-mysqlnd php-mbstring php-xml php-curl php-zip \
        php-gd php-intl php-bcmath php-sqlite3 php-opcache composer

    # Configure PHP-FPM to listen on socket with proper permissions
    sed -i "s|^user = .*|user = ${WEB_USER}|" /etc/php-fpm.d/www.conf
    sed -i "s|^group = .*|group = ${WEB_GROUP}|" /etc/php-fpm.d/www.conf
    sed -i "s|^listen = .*|listen = /run/php-fpm/www.sock|" /etc/php-fpm.d/www.conf
    sed -i "s|^;listen.owner = .*|listen.owner = ${WEB_USER}|" /etc/php-fpm.d/www.conf
    sed -i "s|^;listen.group = .*|listen.group = ${WEB_GROUP}|" /etc/php-fpm.d/www.conf
    sed -i "s|^;listen.mode = .*|listen.mode = 0660|" /etc/php-fpm.d/www.conf
    sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php.ini
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 32M/' /etc/php.ini
    sed -i 's/post_max_size = .*/post_max_size = 32M/' /etc/php.ini

    DB_SERVICE="mariadb"
    PHP_SERVICE="php-fpm"
else
    # Debian / Ubuntu
    export DEBIAN_FRONTEND=noninteractive
    apt-get update && apt-get upgrade -y
    apt-get install -y curl wget git unzip zip software-properties-common build-essential ca-certificates

    mkdir -p /etc/apt/keyrings
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg --yes
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_VERSION}.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
    add-apt-repository -y ppa:ondrej/php
    apt-get update
    apt-get install -y nodejs nginx redis-server mysql-server \
        php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip \
        php8.3-gd php8.3-intl php8.3-bcmath php8.3-sqlite3 php8.3-redis php8.3-opcache

    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

    DB_SERVICE="mysql"
    PHP_SERVICE="php8.3-fpm"
    PHP_FPM_SOCK="/run/php/php8.3-fpm.sock"
fi

log_success "Node: $(node -v) | NPM: $(npm -v) | PHP: $(php -v | head -n1)"

# ------------------------------------------------------------------------------
# 5. Services Startup & Database Initialization
# ------------------------------------------------------------------------------
log_info "Starting database and Redis services..."
systemctl enable --now redis
systemctl enable --now "${DB_SERVICE}"
systemctl enable --now "${PHP_SERVICE}"

log_info "Configuring database: ${DB_NAME}..."
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ensure user exists, password is synchronized, and permissions are granted for BOTH localhost and 127.0.0.1
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"

mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';"

mysql -e "FLUSH PRIVILEGES;"

# ------------------------------------------------------------------------------
# 6. Repository Setup
# ------------------------------------------------------------------------------
if [ ! -d "${APP_DIR}/.git" ]; then
    log_info "Cloning repository into ${APP_DIR}..."
    mkdir -p "${APP_DIR}"
    git clone -b "${GIT_BRANCH}" "${GIT_REPO_URL}" "${APP_DIR}"
fi

# Ensure home directory traversal if located inside /home/*
if [[ "${APP_DIR}" == /home/* ]]; then
    chmod 755 "${USER_HOME}" || true
fi

# ------------------------------------------------------------------------------
# 7. Configure Backend (Laravel 12 + Filament 5)
# ------------------------------------------------------------------------------
log_info "Setting up Laravel Backend in ${APP_DIR}/backend..."
cd "${APP_DIR}/backend"

if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        log_info "Creating backend/.env from built-in template..."
        cat <<EOF > .env
APP_NAME=Serv5
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://${SERVER_IP}:${BACKEND_PORT}

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD="${DB_PASSWORD}"

BROADCAST_CONNECTION=log
CACHE_STORE=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

CORS_ALLOWED_ORIGINS="http://${SERVER_IP},http://${SERVER_IP}:${FRONTEND_PORT},http://${SERVER_IP}:${BACKEND_PORT},http://localhost:3000,http://127.0.0.1:3000"
TRUSTED_PROXIES="*"

HTTP_ALT_SVC_ENABLED=true
HTTP_OUTBOUND_VERSION=http3
HTTP_TCP_KEEPALIVE=true

AI_CONTENT_ENABLED=true
ADMIN_EMAIL=${ADMIN_EMAIL}
ADMIN_PASSWORD="${ADMIN_PASSWORD}"
EOF
    fi
fi

sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|^APP_URL=.*|APP_URL=http://${SERVER_IP}:${BACKEND_PORT}|" .env
sed -i "s|^DB_HOST=.*|DB_HOST=127.0.0.1|" .env
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=\"${DB_PASSWORD}\"|" .env
sed -i "s|^CACHE_STORE=.*|CACHE_STORE=redis|" .env
sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=redis|" .env
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|" .env

if grep -q "ADMIN_EMAIL=" .env; then
    sed -i "s|^ADMIN_EMAIL=.*|ADMIN_EMAIL=${ADMIN_EMAIL}|" .env
    sed -i "s|^ADMIN_PASSWORD=.*|ADMIN_PASSWORD=\"${ADMIN_PASSWORD}\"|" .env
else
    echo "ADMIN_EMAIL=${ADMIN_EMAIL}" >> .env
    echo "ADMIN_PASSWORD=\"${ADMIN_PASSWORD}\"" >> .env
fi

composer install --no-dev --optimize-autoloader --no-interaction
npm install
npm run build

php artisan key:generate --force
php artisan storage:link --force
php artisan migrate --force
php artisan db:seed --force || true
php artisan shield:generate --all || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions for both owner and web server
chown -R "${RUNNING_USER}:${WEB_GROUP}" "${APP_DIR}/backend/storage" "${APP_DIR}/backend/bootstrap/cache"
chmod -R 775 "${APP_DIR}/backend/storage" "${APP_DIR}/backend/bootstrap/cache"

# ------------------------------------------------------------------------------
# 8. Configure Frontend (Next.js 16)
# ------------------------------------------------------------------------------
log_info "Setting up Next.js Frontend in ${APP_DIR}..."
cd "${APP_DIR}"

cat <<EOF > .env.production
NEXT_PUBLIC_API_URL=http://${SERVER_IP}:${BACKEND_PORT}/api/v1
NEXT_PUBLIC_SITE_URL=http://${SERVER_IP}
NODE_ENV=production
PORT=3000
EOF

npm install

# Cap memory limit during webpack build to prevent VM crashes
NODE_OPTIONS="--max-old-space-size=1536" npm run build

chown -R "${RUNNING_USER}:${WEB_GROUP}" "${APP_DIR}"

# ------------------------------------------------------------------------------
# 9. SELinux Configuration (Essential on Fedora / RHEL)
# ------------------------------------------------------------------------------
if command -v getenforce >/dev/null 2>&1; then
    SE_STATUS=$(getenforce)
    log_info "Configuring SELinux policy (Status: ${SE_STATUS})..."
    if [ "$SE_STATUS" != "Disabled" ]; then
        setsebool -P httpd_can_network_connect 1 || true
        setsebool -P httpd_can_network_connect_db 1 || true
        setsebool -P httpd_unified 1 || true
        setsebool -P httpd_read_user_content 1 || true
        chcon -R -t httpd_sys_rw_content_t "${APP_DIR}/backend/storage" "${APP_DIR}/backend/bootstrap/cache" 2>/dev/null || true
    fi
fi

# ------------------------------------------------------------------------------
# 10. Native Systemd Services (Next.js & Queue Worker & Cron)
# ------------------------------------------------------------------------------
log_info "Installing systemd services..."

NODE_BIN=$(command -v node)
NPM_BIN=$(command -v npm)
PHP_BIN=$(command -v php)

# Next.js Service
cat <<EOF > /etc/systemd/system/${APP_NAME}-frontend.service
[Unit]
Description=Serv5 Next.js Service
After=network.target

[Service]
Type=simple
User=${RUNNING_USER}
Group=${WEB_GROUP}
WorkingDirectory=${APP_DIR}
Environment=NODE_ENV=production
Environment=PORT=3000
ExecStart=${NPM_BIN} run start
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# Laravel Queue Worker Service
cat <<EOF > /etc/systemd/system/${APP_NAME}-worker.service
[Unit]
Description=Serv5 Laravel Queue Worker
After=network.target ${DB_SERVICE}.service redis.service

[Service]
Type=simple
User=${RUNNING_USER}
Group=${WEB_GROUP}
WorkingDirectory=${APP_DIR}/backend
ExecStart=${PHP_BIN} ${APP_DIR}/backend/artisan queue:work redis --sleep=3 --tries=3 --timeout=300
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# Laravel Scheduler Cron
cat <<EOF > /etc/cron.d/${APP_NAME}-scheduler
* * * * * ${RUNNING_USER} ${PHP_BIN} ${APP_DIR}/backend/artisan schedule:run >> /dev/null 2>&1
EOF
chmod 0644 /etc/cron.d/${APP_NAME}-scheduler

systemctl daemon-reload
systemctl enable --now ${APP_NAME}-frontend.service
systemctl enable --now ${APP_NAME}-worker.service
systemctl restart "${PHP_SERVICE}"

# ------------------------------------------------------------------------------
# 11. Configure Nginx (Reverse Proxy & FastCGI)
# ------------------------------------------------------------------------------
log_info "Configuring Nginx..."

mkdir -p "${NGINX_CONF_DIR}"

cat <<EOF > "${NGINX_CONF_DIR}/${APP_NAME}.conf"
# Frontend (Next.js on Port 80)
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;

    client_max_body_size 32M;

    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
    }
}

# Backend (Laravel / Filament / API on Port 8000)
server {
    listen 8000;
    listen [::]:8000;
    server_name _;
    root ${APP_DIR}/backend/public;

    index index.php index.html;
    charset utf-8;
    client_max_body_size 32M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

if [ "$PKG_MGR" = "apt" ]; then
    ln -sf /etc/nginx/sites-available/${APP_NAME}.conf /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
fi

nginx -t && systemctl enable --now nginx && systemctl reload nginx

# ------------------------------------------------------------------------------
# 12. Firewall Configuration (firewalld on Fedora / ufw on Ubuntu)
# ------------------------------------------------------------------------------
if command -v firewall-cmd >/dev/null 2>&1 && systemctl is-active --quiet firewalld; then
    log_info "Opening ports in firewalld..."
    firewall-cmd --permanent --add-service=ssh || true
    firewall-cmd --permanent --add-port=80/tcp || true
    firewall-cmd --permanent --add-port=8000/tcp || true
    firewall-cmd --reload || true
elif command -v ufw >/dev/null 2>&1; then
    log_info "Opening ports in ufw..."
    ufw allow 22/tcp || true
    ufw allow 80/tcp || true
    ufw allow 8000/tcp || true
    ufw --force enable || true
fi

# ------------------------------------------------------------------------------
# 13. Summary
# ------------------------------------------------------------------------------
log_success "=========================================================="
log_success " Serv5 Virtual Server Bootstrap Complete!                "
log_success "=========================================================="
echo -e "Frontend:           http://${SERVER_IP}"
echo -e "Backend / Admin:    http://${SERVER_IP}:${BACKEND_PORT}/admin"
echo -e "Backend API:        http://${SERVER_IP}:${BACKEND_PORT}/api/v1"
echo -e "Database Name:      ${DB_NAME}"
echo -e "Database User:      ${DB_USER}"
echo -e "Database Password:  ${DB_PASSWORD}"
echo -e "Admin Email:        ${ADMIN_EMAIL}"
echo -e "Admin Password:     ${ADMIN_PASSWORD}"
echo -e ""
echo -e "Check service statuses:"
echo -e "  systemctl status ${APP_NAME}-frontend"
echo -e "  systemctl status ${APP_NAME}-worker"
echo -e "  systemctl status ${PHP_SERVICE}"
