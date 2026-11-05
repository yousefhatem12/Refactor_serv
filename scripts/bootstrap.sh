#!/usr/bin/env bash
# ==============================================================================
# Serv5 Bootstrap Script - Optimized for Virtual Servers (VPS / Cloud VMs)
# Next.js 16 (Port 80) + Laravel 12 / Filament (Port 8000) + MySQL + Redis
# ==============================================================================

set -euo pipefail

# ------------------------------------------------------------------------------
# 1. Configuration (Edit if needed, or leave defaults for auto-detection)
# ------------------------------------------------------------------------------
APP_NAME="serv5"
APP_DIR="/var/www/${APP_NAME}"
GIT_REPO_URL="https://github.com/yousefhatem12/Refactor_serv.git"
GIT_BRANCH="main"

# Auto-detect Server IP (works locally or on public VPS)
SERVER_IP=$(curl -s -m 3 ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')

# Ports
FRONTEND_PORT="80"
BACKEND_PORT="8000"

# Runtimes
NODE_VERSION="22"       # Node.js 22 LTS
PHP_VERSION="8.3"        # PHP 8.3

# Database & Admin credentials
DB_NAME="serv5"
DB_USER="serv5_user"
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
   log_error "This script must run with root privileges (sudo -i)."
   exit 1
fi

export DEBIAN_FRONTEND=noninteractive

# ------------------------------------------------------------------------------
# 2. Virtual Server Swap Allocation (CRITICAL for Next.js builds on low-RAM VMs)
# ------------------------------------------------------------------------------
log_info "Checking virtual server memory and swap..."
TOTAL_RAM_MB=$(free -m | awk '/^Mem:/{print $2}')
SWAP_EXISTS=$(free -m | awk '/^Swap:/{print $2}')

if [ "$SWAP_EXISTS" -lt 1024 ]; then
    log_warn "Virtual server has low swap. Creating 2GB swapfile to prevent build OOM crashes..."
    fallocate -l 2G /swapfile || dd if=/dev/zero of=/swapfile bs=1M count=2048
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    if ! grep -q '/swapfile' /etc/fstab; then
        echo '/swapfile none swap sw 0 0' >> /etc/fstab
    fi
    sysctl vm.swappiness=10
    echo 'vm.swappiness=10' >> /etc/sysctl.d/99-vps-swap.conf
    log_success "2GB Swap active."
else
    log_info "Sufficient swap already configured."
fi

# ------------------------------------------------------------------------------
# 3. Base Packages
# ------------------------------------------------------------------------------
log_info "Updating package lists..."
apt update && apt upgrade -y
apt install -y curl wget git unzip zip software-properties-common ufw build-essential

# ------------------------------------------------------------------------------
# 4. Node.js 22 LTS
# ------------------------------------------------------------------------------
log_info "Installing Node.js ${NODE_VERSION}.x..."
mkdir -p /etc/apt/keyrings
curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg --yes
echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_VERSION}.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
apt update && apt install -y nodejs
log_success "Node: $(node -v) | NPM: $(npm -v)"

# ------------------------------------------------------------------------------
# 5. PHP ${PHP_VERSION} & Required Extensions
# ------------------------------------------------------------------------------
log_info "Installing PHP ${PHP_VERSION} & extensions..."
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-redis \
    php${PHP_VERSION}-opcache

# Adjust memory limits for virtual server
sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 32M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 32M/' /etc/php/${PHP_VERSION}/fpm/php.ini
systemctl restart php${PHP_VERSION}-fpm

# Install Composer
log_info "Installing Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ------------------------------------------------------------------------------
# 6. MySQL & Redis (Optimized for VPS)
# ------------------------------------------------------------------------------
log_info "Installing MySQL and Redis..."
apt install -y mysql-server redis-server

# Tune MySQL InnoDB buffer pool for virtual server
cat <<EOF > /etc/mysql/mysql.conf.d/vps-tuning.cnf
[mysqld]
innodb_buffer_pool_size = 128M
max_connections = 60
EOF

systemctl restart mysql
systemctl enable --now redis-server mysql

# Provision DB
log_info "Provisioning MySQL database..."
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';"
mysql -e "FLUSH PRIVILEGES;"

# ------------------------------------------------------------------------------
# 7. Pull Code
# ------------------------------------------------------------------------------
log_info "Setting up code at ${APP_DIR}..."
if [ -d "${APP_DIR}/.git" ]; then
    cd "${APP_DIR}" && git pull origin "${GIT_BRANCH}"
else
    mkdir -p "${APP_DIR}"
    git clone -b "${GIT_BRANCH}" "${GIT_REPO_URL}" "${APP_DIR}"
fi

# ------------------------------------------------------------------------------
# 8. Setup Backend (Laravel)
# ------------------------------------------------------------------------------
log_info "Configuring Laravel Backend..."
cd "${APP_DIR}/backend"

if [ ! -f .env ]; then
    cp .env.example .env
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

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ------------------------------------------------------------------------------
# 9. Setup Frontend (Next.js)
# ------------------------------------------------------------------------------
log_info "Configuring Next.js Frontend..."
cd "${APP_DIR}"

cat <<EOF > .env.production
NEXT_PUBLIC_API_URL=http://${SERVER_IP}:${BACKEND_PORT}/api/v1
NEXT_PUBLIC_SITE_URL=http://${SERVER_IP}
NODE_ENV=production
PORT=3000
EOF

npm install

# Cap node memory during webpack build on virtual server to prevent memory spike
NODE_OPTIONS="--max-old-space-size=1536" npm run build

chown -R www-data:www-data "${APP_DIR}"

# ------------------------------------------------------------------------------
# 10. Systemd Services
# ------------------------------------------------------------------------------
log_info "Setting up systemd services..."

# Next.js Frontend Service
cat <<EOF > /etc/systemd/system/${APP_NAME}-frontend.service
[Unit]
Description=Serv5 Next.js Service
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=${APP_DIR}
Environment=NODE_ENV=production
Environment=PORT=3000
ExecStart=/usr/bin/npm run start
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# Queue Worker Service
cat <<EOF > /etc/systemd/system/${APP_NAME}-worker.service
[Unit]
Description=Serv5 Queue Worker
After=network.target mysql.service redis-server.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=${APP_DIR}/backend
ExecStart=/usr/bin/php ${APP_DIR}/backend/artisan queue:work redis --sleep=3 --tries=3 --timeout=300
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# Scheduler Cron
cat <<EOF > /etc/cron.d/${APP_NAME}-scheduler
* * * * * www-data /usr/bin/php ${APP_DIR}/backend/artisan schedule:run >> /dev/null 2>&1
EOF
chmod 0644 /etc/cron.d/${APP_NAME}-scheduler

systemctl daemon-reload
systemctl enable --now ${APP_NAME}-frontend.service
systemctl enable --now ${APP_NAME}-worker.service

# ------------------------------------------------------------------------------
# 11. Nginx Reverse Proxy (Frontend :80 / Backend :8000)
# ------------------------------------------------------------------------------
log_info "Configuring Nginx..."
apt install -y nginx

cat <<EOF > /etc/nginx/sites-available/${APP_NAME}.conf
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
        fastcgi_pass unix:/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/${APP_NAME}.conf /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# ------------------------------------------------------------------------------
# 12. Firewall
# ------------------------------------------------------------------------------
if command -v ufw >/dev/null 2>&1; then
    log_info "Opening ports in firewall..."
    ufw allow 22/tcp || true
    ufw allow 80/tcp || true
    ufw allow 8000/tcp || true
    ufw --force enable || true
fi

# ------------------------------------------------------------------------------
# 13. Summary
# ------------------------------------------------------------------------------
log_success "=========================================================="
log_success " Virtual Server Bootstrap Finished!                      "
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
echo -e "Check processes:"
echo -e "  systemctl status ${APP_NAME}-frontend"
echo -e "  systemctl status ${APP_NAME}-worker"
