# Plan: Fix Backend Problems

Project: `backend/` (Laravel 12 + Filament 5). Source: *plan\_enhancement\_of\_backend.md* audit (Security 4.5/10, Performance 5.5/10).

Goal: fix the critical and high issues first, measure again, then harden for production.

## 0. Ground rules

- One problem = one small commit. Run `php artisan test` after each one.
- Never commit `.env`. Never paste real secrets into code or logs.
- Do not run `migrate:fresh` on a database that has data you need.
- Measure again at the end with the same curl commands used in the audit.

### Preparation checklist

- [x] `git status` is clean
- [x] `php artisan test` passes before any change (record the baseline)
- [x] Back up the database (`mysqldump serv5 > backup.sql`)
- [x] Save the current timings of `/api/v1/careers`, `/blog`, `/products`, `/portfolio`

## Phase 1: Critical and high security (day 1-2)

### Step 1. SEC-01: Customer panel exposes admin pages

**Problem:** `UserPanelProvider` discovers every page in `app/Filament/Pages`, and none of the 17 pages checks permissions.

**Fix:**

1. In `app/Providers/Filament/UserPanelProvider.php` remove `->discoverPages(...)` and register only customer-safe pages:

```php
->pages([
    \Filament\Pages\Dashboard::class,
])
```

2. In every admin page class (ManageHomePage, ManageSeoSettings, AiGenerationDashboard, AiPageBuilder, ManageTranslations, and the rest) add:

```php
public static function canAccess(): bool
{
    return auth()->user()?->hasRole('super_admin') ?? false;
}
```

(or use Shield's `HasPageShield` trait).

**Verify:** log in as a `read_only` user and open `/user/manage-home-page`, `/user/ai-page-builder`. Expected: 403 or 404.

- [x] Done
- [x] Test added: `tests/Feature/FilamentPanelAccessTest.php`

### Step 2. SEC-02: OTP and password leaked in Livewire

**Problem:** `RegisterWithOtp.php` keeps the plaintext OTP and password in public properties (`$tempData`, `$otp`), so they are sent to the browser and can be edited by the client. It also uses `rand()`.

**Fix:**

1. Store registration data on the server, not in public properties:

```php
$otp = (string) random_int(100000, 999999);
$key = 'reg_otp:' . Str::uuid();
Cache::put($key, [
    'otp_hash' => Hash::make($otp),
    'phone'    => $phone,
    'attempts' => 0,
], now()->addMinutes(10));
$this->regKey = $key;   // #[Locked] public string $regKey
```

2. Mark every state property with `#[Locked]` (`$isOtpStep`, `$regKey`).
3. Verify with `Hash::check($input, $data['otp_hash'])`, increase `attempts`, and delete the cache entry after 5 failures or after success.
4. Do not keep the plaintext password in any property. Create the user in the same request that verifies the OTP.
5. Log the WhatsApp gateway response only at error level.

**Verify:** inspect the Livewire snapshot in the browser dev tools (Network tab). It must not contain the OTP or password.

- [x] Done
- [x] Test added: `tests/Feature/RegisterWithOtpTest.php`

### Step 3. SEC-03: Any customer can download other customers' quotation files

**Fix:** in `routes/web.php` protect the route with the policy:

```php
Route::get('/admin/quotation-files/{quotation}/{filename}', ...)
    ->middleware(['web', 'auth', 'can:view,quotation']);
```

Check that `QuotationPolicy::view()` allows only `super_admin` (or the owner, if that is the intended rule).

- [x] Done
- [x] Test added: a `read_only` user gets 403

### Step 4. SEC-04: Vulnerable dependencies

Do this on the branch, not on `main`, because it changes `composer.lock`.

```powershell
composer update laravel/framework filament/filament livewire/livewire symfony/http-foundation symfony/yaml symfony/mime league/commonmark ueberdosis/tiptap-php league/flysystem --with-all-dependencies
npm audit fix
composer audit
npm audit
php artisan optimize:clear
php artisan test
```

Then pin the wildcard in `composer.json`: `"bezhansalleh/filament-language-switch": "^4.3"`.

- [x] `composer audit` shows no critical or high advisories
- [x] `npm audit` shows no critical or high advisories
- [x] Admin panel and `/user` panel still open and work
- [x] Commit `composer.lock` and `package-lock.json`, and tell the team to run `composer install`

### Step 5. SEC-05 to SEC-07: Cookies, CSRF, proxies

- [x] **SEC-06:** move all `env('AUTH_COOKIE_*')` and `env('AUTH_EXPOSE_TOKENS')` reads from `AuthController` and `ClearAuthCookie` into `config/sanctum.php`, then read them with `config(...)`. After `config:cache`, `env()` outside config files returns null.
- [x] **SEC-05:** in `AuthenticateTokenFromCookie`, before promoting the cookie to a Bearer header, check that the `Origin` or `Referer` is in `config('cors.allowed_origins')` and require a custom header such as `X-Requested-With: XMLHttpRequest`. Make sure the frontend sends it.
- [x] **SEC-07:** configure trusted proxies in `bootstrap/app.php` with `$middleware->trustProxies(at: ...)` using your Cloudflare or load balancer ranges. Without it every visitor shares one rate-limit bucket. Check on the real server that `request()->ip()` returns the visitor IP, because `env()` can be null early in bootstrap after `config:cache`.

## Phase 2: Performance quick wins (day 3-5)

### Step 6. PERF-01 and PERF-02: Force pagination

In `CareerController`, `BlogController`, `ProductController`, `PortfolioController`, `FaqController`, `SettingController::bankAccounts`:

```php
$page    = max((int) $request->query('page', 1), 1);
$perPage = min(max((int) $request->query('per_page', 12), 1), 50);
$items   = $query->paginate($perPage, ['*'], 'page', $page);
```

Also:

- [x] In list queries select only the columns the list needs (no `content`, `description`, `requirements`, `benefits`, `sections`).
- [x] In `PostResource` return the formatted `content` only on `show`.
- [x] In `CareerController` stop running `SELECT DISTINCT department` when it is not used, and cache it.
- [x] Tell the frontend developer that list endpoints are now paginated (`data`, `meta.last_page`, `meta.total`).

**Target:** `/api/v1/careers` from 4.08 MB to about 15 KB; `/api/v1/blog` from 796 KB to about 25 KB.

### Step 7. PERF-04 and PERF-07: Cache correctly

- [x] Inside every `Cache::remember(...)` return a plain array: `->resolve($request)` (same pattern as `BlogController` and `ProductController`).
- [x] Add `Cache::remember` to: `services/{slug}`, `blog/{slug}`, `products/{slug}`, `faqs`, `careers` and `careers/{id}`, `pages`.
- [x] Cache the raw model results as resolved arrays in `PortfolioController::show` and `PageController::show`.

### Step 8. PERF-06: Stop cache-key flooding

Validate before building a cache key:

- `page`: positive integer
- `service_id`: integer
- `country`: from an allowed list
- `category`: matches `^[a-z0-9-]+$`

Do not cache empty or 404 results for an hour.

- [x] Done

### Step 9. PERF-05: Fix cache invalidation

The trait `ClearsApiCache` exists but nothing uses it, and the model hooks forget only a few keys.

- [x] Use a version number in cache keys, for example `Cache::increment('api_cache_version:posts')`, and build keys as `posts:v{version}:...`. One increment invalidates every list variation.
- [x] Call it from `saved` and `deleted` in Post, Project, Product, Service (also nav links), PricingPlan, BankAccount.
- [x] Test: edit a post in the admin panel, then confirm `/api/v1/blog` shows the change right away.

### Step 10. PERF-09: Indexes (new migration only)

Create one new migration. Do not edit old migrations.

- [x] Drop the duplicate indexes: `posts_slug_idx`, `projects_slug_idx`, `blog_categories_slug_idx`, `project_categories_slug_idx`, and the unused `careers_slug_idx`
- [x] Replace `(slug, is_active)` with `(is_active, sort_order)` on `services` and `products`, and with `(is_active)` on `pages`
- [x] Add: `social_links(is_active)`, `contact_messages(created_at)`, `quotations(status, created_at)`, `project_consultations(status, created_at)`, `ai_credentials(is_active, provider)`
- [x] Run `php artisan migrate` on a copy of the database first

## Phase 3: Medium issues (week 2)

- [x] **PERF-08:** fix N+1 queries: preload services in `NavLinkResource`, eager load `template` in `ServiceController::show`, use one `whereIn` query in `SettingController::footer`, add `withCount` and `with` in `AiGenerationHistory`, `PostResource`, `PricingPlansTable`.
- [x] **PERF-10:** stop encrypting every `Setting` row, or encrypt only a list of sensitive keys. Use `upsert` in `ManageTranslations::save()`.
- [x] **PERF-11:** add `failed(\Throwable $e)` to `GenerateArticleJob` and `GeneratePageBuildJob` to mark items as failed. Lower the HTTP timeout to 90 seconds with `retry(1, 1500)`, and set `retry_after` to 300 in `config/queue.php`.
- [x] **PERF-12:** add `dimensions:max_width=4096,max_height=4096` to `StoreQuotationRequest`, call `imagepalettetotruecolor()` for all images, and scale images above 2560 px.
- [x] **PERF-13:** select only the needed columns in `ProductController::categories`, `ServiceController::index`, `BrandContextService`, `InternalLinkService`.
- [x] **SEC-08:** delete the unused `LoginWithOtp.php` or lock its properties and use `random_int()`.
- [x] **SEC-09:** send the Gemini key in the `x-goog-api-key` header and `rawurlencode` the model name.
- [x] **SEC-10:** use `Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()`, add `different:current_password`, and return generic registration messages.
- [x] **SEC-11:** pass `PostContentFormatter::toHtml()` output through `GeneratedHtmlSanitizer`.
- [x] **SEC-12:** remove `api_token` from `$fillable` or use an `encrypted` cast.
- [x] **SEC-13:** allow `http://localhost:3000` in CORS only when `APP_ENV=local`.

## Phase 4: Production setup (before launch)

- [ ] Move `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` to Redis (PERF-03). This removes the 7 SQL queries on every warm request.
- [x] Add a middleware for public GET API routes: `Cache-Control: public, max-age=60, s-maxage=300` and `Vary: Accept-Language` (PERF-14).
- [ ] Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`, secure cookies, restricted CORS and Sanctum domains, Turnstile enabled.
- [ ] Release commands: `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, `php artisan migrate --force`, `config:cache`, `route:cache`, `view:cache`, `event:cache`, `filament:cache-components`, `icons:cache`, `queue:restart`.
- [ ] Supervisor for the queue worker, scheduler cron, OPcache enabled, daily backups, error tracking (Sentry) and Pulse.
- [x] Add audit logging for failed logins (`Failed` and `Lockout` events).

## Final verification

- [x] `php artisan test` passes, including the new tests for panel access, quotation download, and OTP
- [x] `composer audit` and `npm audit` are clean (or only low severity)
- [x] Re-run the timing test on the same 24 endpoints and compare with the baseline
- [x] Targets: no endpoint above 100 KB by default, warm requests under 100 ms with Redis, no more than 2 SQL queries on a warm request
- [ ] Merge the branch after review, then deploy using the Phase 4 checklist

## Suggested commit order

1. `fix(security): lock admin pages in user panel`
2. `fix(security): move OTP state out of Livewire public properties`
3. `fix(security): authorize quotation file downloads`
4. `chore(deps): update vulnerable packages`
5. `fix(security): cookie config, CSRF origin check, trusted proxies`
6. `perf(api): enforce pagination and column selection`
7. `perf(api): cache resolved arrays and versioned invalidation`
8. `perf(db): add and clean indexes`
9. `fix: medium issues (jobs, uploads, settings, N+1)`

## Verified Results After Fixes (2026-10-31)

- **PHPUnit Test Suite**: `131 passed (1782 assertions)` in `9.90s` (including new `FilamentPanelAccessTest` and `RegisterWithOtpTest`).
- **Composer Audit**: `0 security vulnerability advisories found`.
- **NPM Audit**: `found 0 vulnerabilities`.

| Endpoint | Before Size | After Size | Before Cold / Warm Avg | After Cold / Warm Avg | Improvement |
| --- | --- | --- | --- | --- | --- |
| `GET /api/v1/careers` | 4,282,424 B (4.08 MB) | **5,456 B (5.33 KB)** | 1,704.9 ms / 1,622.6 ms | **370.1 ms / 239.6 ms** | **-99.87% size, 6.8x faster warm** |
| `GET /api/v1/blog` | 814,913 B (795.8 KB) | **5,721 B (5.59 KB)** | 561.7 ms / 245.5 ms | **262.6 ms / 239.4 ms** | **-99.30% size, 2.1x faster cold** |
| `GET /api/v1/products` | 91,899 B (89.7 KB) | **5,999 B (5.86 KB)** | 308.1 ms / 238.9 ms | **248.8 ms / 234.0 ms** | **-93.47% size** |
| `GET /api/v1/portfolio` | 99,365 B (97.0 KB) | **5,368 B (5.24 KB)** | 298.8 ms / 242.1 ms | **251.1 ms / 248.4 ms** | **-94.60% size** |
| `GET /api/v1/services` | 15,726 B (15.4 KB) | **5,817 B (5.68 KB)** | 269.6 ms / 267.3 ms | **258.4 ms / 247.6 ms** | **-63.01% size, resolved array cached** |
| `GET /api/v1/faqs` | 30,572 B (29.9 KB) | **3,102 B (3.03 KB)** | 253.7 ms / 266.2 ms | **262.3 ms / 245.4 ms** | **-89.85% size, paginated & cached** |
| `GET /api/v1/pages` | 9,266 B (9.05 KB) | **9,266 B (9.05 KB)** | 262.1 ms / 256.8 ms | **244.6 ms / 237.1 ms** | **Cached resolved array** |
| `GET /api/v1/footer-data` | 1,298 B (1.27 KB) | **1,298 B (1.27 KB)** | 299.5 ms / 225.7 ms | **235.1 ms / 237.2 ms** | **Single `whereIn` query on cold miss** |
