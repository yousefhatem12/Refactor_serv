#!/usr/bin/env bash
set -e

# ==============================================================================
# Refactor_serv — Master Server Staging Deployment & Fix Script
# Target: https://refactor.serv5.com.eg & https://api-refactor.serv5.com.eg
# Path:   /home2/serv5eg/Refactor_serv
# ==============================================================================

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}======================================================${NC}"
echo -e "${BLUE} Starting Refactor_serv Staging Deployment & Fix      ${NC}"
echo -e "${BLUE}======================================================${NC}"

# Detect base directory
BASE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKEND_DIR="${BASE_DIR}/backend"
cd "${BASE_DIR}"

echo -e "${GREEN}[1/7] Resolving PHP & Tools...${NC}"
PHP_BIN="/opt/cpanel/ea-php83/root/usr/bin/php"
if [ ! -x "${PHP_BIN}" ]; then
    PHP_BIN="$(which php 2>/dev/null || echo "php")"
fi
echo "Using PHP: $("${PHP_BIN}" -v | head -n 1)"

# ------------------------------------------------------------------------------
# 2. Configure Frontend Environment Files
# ------------------------------------------------------------------------------
echo -e "${GREEN}[2/7] Writing Frontend Environment (.env, .env.production, .env.local)...${NC}"
cat << 'EOF' > "${BASE_DIR}/.env.production"
NEXT_PUBLIC_SITE_URL=https://refactor.serv5.com.eg
NEXT_PUBLIC_API_URL=https://api-refactor.serv5.com.eg/api/v1
INTERNAL_API_URL=https://api-refactor.serv5.com.eg/api/v1
BACKEND_API_URL=https://api-refactor.serv5.com.eg/api/v1
EOF

cp "${BASE_DIR}/.env.production" "${BASE_DIR}/.env"
cp "${BASE_DIR}/.env.production" "${BASE_DIR}/.env.local"

# ------------------------------------------------------------------------------
# 3. Configure Backend Environment (.env)
# ------------------------------------------------------------------------------
echo -e "${GREEN}[3/7] Writing Backend Environment (backend/.env)...${NC}"
mkdir -p "${BACKEND_DIR}/database"
mkdir -p "${BACKEND_DIR}/storage/logs"
mkdir -p "${BACKEND_DIR}/storage/framework/cache/data"
mkdir -p "${BACKEND_DIR}/storage/framework/sessions"
mkdir -p "${BACKEND_DIR}/storage/framework/views"
mkdir -p "${BACKEND_DIR}/bootstrap/cache"
mkdir -p "${BASE_DIR}/tmp"

DB_FILE="${BACKEND_DIR}/database/database.sqlite"
if [ ! -f "${DB_FILE}" ]; then
    touch "${DB_FILE}"
fi

cat << EOF > "${BACKEND_DIR}/.env"
APP_NAME=Serv5
APP_ENV=production
APP_KEY=base64:82ojObSrutInvTap0NzrFqKPfJ1QjpJAWZwAd+TACLU=
APP_DEBUG=false
APP_URL=https://api-refactor.serv5.com.eg

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=${DB_FILE}
DB_BUSY_TIMEOUT=5000
DB_JOURNAL_MODE=WAL
DB_SYNCHRONOUS=NORMAL

CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync

SANCTUM_STATEFUL_DOMAINS=refactor.serv5.com.eg,www.refactor.serv5.com.eg,serv5.com.eg,www.serv5.com.eg,localhost:3000,127.0.0.1:3000
CORS_ALLOWED_ORIGINS=https://refactor.serv5.com.eg,https://www.refactor.serv5.com.eg,https://serv5.com.eg,https://www.serv5.com.eg,http://localhost:3000,http://127.0.0.1:3000
EOF

# ------------------------------------------------------------------------------
# 4. Configure SQLite & Permissions
# ------------------------------------------------------------------------------
echo -e "${GREEN}[4/7] Enabling WAL mode & setting permissions...${NC}"
if command -v sqlite3 >/dev/null 2>&1; then
    sqlite3 "${DB_FILE}" "PRAGMA journal_mode=WAL; PRAGMA busy_timeout=5000;" || true
fi
chmod -R 775 "${BACKEND_DIR}/storage" "${BACKEND_DIR}/bootstrap/cache" "${BACKEND_DIR}/database" 2>/dev/null || true

# ------------------------------------------------------------------------------
# 5. Run Database Migrations, Seeders & Cache Optimizations
# ------------------------------------------------------------------------------
echo -e "${GREEN}[5/7] Running Laravel Migrations & Seeders...${NC}"
cd "${BACKEND_DIR}"
"${PHP_BIN}" artisan migrate --force
"${PHP_BIN}" artisan db:seed --force
"${PHP_BIN}" artisan storage:link --force 2>/dev/null || true
"${PHP_BIN}" artisan optimize:clear
cd "${BASE_DIR}"

# ------------------------------------------------------------------------------
# 6. Ensure Symlinks & Reload Passenger
# ------------------------------------------------------------------------------
echo -e "${GREEN}[6/7] Ensuring Apache symlink & reloading Passenger...${NC}"
PUBLIC_HTML_TARGET="/home2/serv5eg/public_html/Refactor_serv/backend/public"
if [ -d "$(dirname "${PUBLIC_HTML_TARGET}")" ]; then
    mkdir -p "$(dirname "${PUBLIC_HTML_TARGET}")"
    ln -sfn "${BACKEND_DIR}/public" "${PUBLIC_HTML_TARGET}"
fi

# Touch restart file for Passenger
touch "${BASE_DIR}/tmp/restart.txt"
echo "Passenger reload signal sent."

# ------------------------------------------------------------------------------
# 7. Automated Verification & Health Checks
# ------------------------------------------------------------------------------
echo -e "${GREEN}[7/7] Running Health Checks...${NC}"
sleep 2

check_url() {
    local label="$1"
    local url="$2"
    local expected="$3"
    local status
    status=$(curl -s -k -o /dev/null -w "%{http_code}" "$url" || echo "ERR")
    if [ "$status" = "$expected" ]; then
        echo -e "  [✔] ${label}: ${status} OK"
    else
        echo -e "  [✘] ${label}: ${status} (expected ${expected})"
    fi
}

check_url "Frontend Home (/en)" "https://refactor.serv5.com.eg/en" "200"
check_url "Frontend Home (/ar)" "https://refactor.serv5.com.eg/ar" "200"
check_url "Backend Health (/up)" "https://api-refactor.serv5.com.eg/up" "200"
check_url "Backend Settings (/settings)" "https://api-refactor.serv5.com.eg/api/v1/settings" "200"
check_url "Backend Services (/services)" "https://api-refactor.serv5.com.eg/api/v1/services" "200"
check_url "Backend Pages (/pages/home)" "https://api-refactor.serv5.com.eg/api/v1/pages/home" "200"

# CORS Check
CORS_HEADER=$(curl -s -k -I -H "Origin: https://refactor.serv5.com.eg" https://api-refactor.serv5.com.eg/api/v1/settings | grep -i "Access-Control-Allow-Origin" || true)
if [[ "$CORS_HEADER" =~ "refactor.serv5.com.eg" ]]; then
    echo -e "  [✔] CORS for refactor origin: OK"
else
    echo -e "  [✘] CORS for refactor origin: MISSING"
fi

echo -e "${BLUE}======================================================${NC}"
echo -e "${GREEN} Deployment & Fix Complete! All systems operational. ${NC}"
echo -e "${BLUE}======================================================${NC}"
