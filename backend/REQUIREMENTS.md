# Serv5 Backend Requirements (`backend/`)

See root [`REQUIREMENTS.md`](../REQUIREMENTS.md) for full Frontend + Backend requirements.

## PHP & Server Requirements
- **PHP:** `>= 8.2` (`8.3` / `8.4` recommended with OPcache enabled)
- **Extensions:** `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd` (with WebP support), `intl`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `tokenizer`, `xml`, `zip`, `redis` (recommended)
- **Database:** MySQL `8.0+` / MariaDB `10.6+`
- **Web Server:** Nginx `1.25+` (`http3` QUIC + `http2`), Caddy `2.8+`, or Apache `2.4+` (`mod_http2`, `mod_headers`, `mod_rewrite`)

## Composer Packages (`composer.json`)
- `laravel/framework`: `^12.0` (`v12.70.2`)
- `filament/filament`: `^5.6` (`v5.11.0`)
- `bezhansalleh/filament-shield`: `^4.2`
- `bezhansalleh/filament-language-switch`: `^4.3`
- `spatie/laravel-permission`: `^6.25`
- `laravel/sanctum`: `^4.0`
- `laravel/tinker`: `^2.10.1`

## Composer Dev Packages
- `phpunit/phpunit`: `^11.5.50`
- `fakerphp/faker`: `^1.23`
- `mockery/mockery`: `^1.6`
- `nunomaduro/collision`: `^8.6`
- `laravel/pint`: `^1.24`
- `laravel/pail`: `^1.2.2`
- `laravel/sail`: `^1.41`

## Node / Vite Asset Packages (`package.json`)
- `vite`: `^7.0.7`
- `laravel-vite-plugin`: `^2.0.0`
- `tailwindcss`: `^4.0.0`
- `@tailwindcss/vite`: `^4.0.0`
- `axios`: `^1.11.0`
- `concurrently`: `^9.0.1`
