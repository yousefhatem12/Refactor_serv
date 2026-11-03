# Serv5 Full-Stack Requirements (Frontend & Backend)

**Repository:** `https://github.com/yousefhatem12/Refactor_serv.git`  
**Stack:** Next.js 16 (React 19) Frontend + Laravel 12 (Filament 5 / Livewire 4) Backend  

---

## 1. System & Runtime Requirements

| Component | Minimum Version | Recommended Production Version | Purpose |
| --- | --- | --- | --- |
| **Node.js** | `>= 20.9.0` | `22.x LTS` | Next.js 16 frontend & Vite asset bundler |
| **NPM** | `>= 10.x` | `10.x+` | Package management (Frontend & Backend assets) |
| **PHP** | `>= 8.2` | `8.3` or `8.4` (with OPcache + JIT) | Laravel 12 + Filament 5 runtime |
| **Composer** | `>= 2.7` | `2.8+` | PHP dependency management |
| **Database** | MySQL `8.0+` / MariaDB `10.6+` | MySQL `8.0+` | Primary relational database (`serv5`) |
| **Cache / Queue / Session** | Database driver (built-in) | **Redis `7.x+`** (`phpredis` extension) | Sub-millisecond API cache, sessions, and queues |
| **Web Server** | Apache `2.4+` (`mod_rewrite`, `mod_headers`, `mod_http2`) | **Nginx `1.25+` (`http3` QUIC + `http2`)** or **Caddy `2.8+`** | HTTP/3 (`h3` over UDP:443) + HTTP/2 (`h2`) termination |

### Required PHP Extensions (Backend)
- `bcmath`
- `ctype`
- `curl` (built with `libcurl` HTTP/2 & HTTP/3 support recommended)
- `dom` / `libxml` (used by `PostContentFormatter` & `GeneratedHtmlSanitizer`)
- `fileinfo` (MIME validation on file uploads)
- `gd` (WebP conversion & image scaling in `ImageService`)
- `intl` (Filament formatting & localization)
- `json`
- `mbstring`
- `openssl`
- `pdo_mysql` (production/local MySQL) & `pdo_sqlite` (in-memory PHPUnit test suite)
- `tokenizer`
- `xml`
- `zip`
- `redis` (recommended for production `CACHE_STORE=redis`)

---

## 2. Frontend Requirements (`/` — Next.js 16 + React 19)

### 2.1 Production Dependencies (`package.json`)
| Package | Version | Purpose |
| --- | --- | --- |
| `next` | `^16.2.9` | React framework (App Router, SSR/ISR) |
| `react` | `19.2.4` | Core UI library |
| `react-dom` | `19.2.4` | DOM renderer |
| `swr` | `^2.4.1` | Client-side API data fetching & caching |
| `framer-motion` | `^12.38.0` | UI animations & transitions |
| `react-icons` | `^5.6.0` | Icon library |
| `parse5` | `^8.0.1` | HTML parsing |
| `tailwindcss` | `^4` | Utility-first CSS framework |
| `@tailwindcss/postcss` | `^4` | PostCSS integration for Tailwind v4 |
| `postcss` | `^8.5.15` | CSS transformation |

### 2.2 Development & Testing Dependencies (`package.json`)
| Package | Version | Purpose |
| --- | --- | --- |
| `vitest` | `^4.1.9` | Unit & component test runner |
| `@vitejs/plugin-react` | `^6.0.3` | React plugin for Vitest |
| `@testing-library/react` | `^16.3.2` | React component testing utilities |
| `@testing-library/dom` | `^10.4.1` | DOM testing utilities |
| `jsdom` | `^29.1.1` | DOM environment for Vitest |
| `@playwright/test` | `^1.62.1` | End-to-End (E2E) browser testing |
| `eslint` | `^9` | JavaScript/JSX linter |
| `eslint-config-next` | `16.2.4` | Next.js ESLint rules |

### 2.3 Frontend Environment Variables (`.env.local`)
```dotenv
NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api/v1
NEXT_PUBLIC_SITE_URL=http://localhost:3000
NEXT_PUBLIC_TURNSTILE_SITE_KEY=
```

### 2.4 Frontend Installation & Commands
```bash
# Install dependencies
npm install

# Run local development server (http://localhost:3000)
npm run dev

# Build for production
npm run build

# Start production server
npm run start

# Run unit tests & E2E tests
npm run test
npm run test:e2e
```

---

## 3. Backend Requirements (`backend/` — Laravel 12 + Filament 5)

### 3.1 PHP Composer Dependencies (`backend/composer.json`)
| Package | Version Constraint | Locked / Verified Version | Purpose |
| --- | --- | --- | --- |
| `php` | `^8.2` | `8.2+ / 8.4` | Language runtime |
| `laravel/framework` | `^12.0` | `v12.70.2` | Core MVC framework |
| `filament/filament` | `^5.6` | `v5.11.0` | Admin (`/admin`) & Customer (`/user`) panels |
| `livewire/livewire` | (via Filament) | `v4.4.8` | Reactive server-driven UI components |
| `bezhansalleh/filament-shield` | `^4.2` | `4.2.x` | Role & policy management for Filament |
| `bezhansalleh/filament-language-switch` | `^4.3` | `4.3.x` | Arabic / English panel switcher |
| `spatie/laravel-permission` | `^6.25` | `6.25.x` | RBAC roles (`super_admin`, `read_only`) & permissions |
| `laravel/sanctum` | `^4.0` | `v4.x` | API token & HttpOnly cookie authentication |
| `laravel/tinker` | `^2.10.1` | `v2.10.x` | REPL console |

### 3.2 PHP Dev Dependencies (`backend/composer.json`)
| Package | Version Constraint | Purpose |
| --- | --- | --- |
| `phpunit/phpunit` | `^11.5.50` | Automated feature & unit testing (`133 tests`) |
| `fakerphp/faker` | `^1.23` | Test data factories |
| `mockery/mockery` | `^1.6` | Test mocking |
| `nunomaduro/collision` | `^8.6` | CLI error reporting |
| `laravel/pint` | `^1.24` | PHP code style fixer |
| `laravel/pail` | `^1.2.2` | Real-time log tailing |
| `laravel/sail` | `^1.41` | Docker development environment |

### 3.3 Backend Asset Dependencies (`backend/package.json`)
| Package | Version | Purpose |
| --- | --- | --- |
| `vite` | `^7.0.7` | Asset bundler |
| `laravel-vite-plugin` | `^2.0.0` | Laravel Vite integration |
| `tailwindcss` | `^4.0.0` | CSS framework |
| `@tailwindcss/vite` | `^4.0.0` | Tailwind v4 Vite plugin |
| `axios` | `^1.11.0` | HTTP client |
| `concurrently` | `^9.0.1` | Parallel dev server runner (`composer run dev`) |

### 3.4 Backend Key Environment Variables (`backend/.env`)
Copy `backend/.env.example` to `backend/.env` and configure:
```dotenv
APP_NAME=Serv5
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.serv5.com.eg

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=serv5
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=database          # Recommended in production: redis
SESSION_DRIVER=database       # Recommended in production: redis
QUEUE_CONNECTION=database     # Recommended in production: redis

CORS_ALLOWED_ORIGINS="https://serv5.com.eg,https://www.serv5.com.eg,http://localhost:3000,http://127.0.0.1:3000"
TRUSTED_PROXIES="*"

HTTP_ALT_SVC_ENABLED=true
HTTP_OUTBOUND_VERSION=http3
HTTP_TCP_KEEPALIVE=true

AI_CONTENT_ENABLED=true
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=
WHATS_API_URL=
WHATS_API_TOKEN=
```

### 3.5 Backend Installation, Migration & Verification Commands
```bash
cd backend

# 1. Install PHP & Node dependencies
composer install
npm install && npm run build

# 2. Configure environment & run migrations
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --force

# 3. Run queue worker (required for AI article/page generation jobs)
php artisan queue:work --tries=2 --timeout=300

# 4. Run full automated test suite & security audits
php artisan test
composer audit
npm audit
```
