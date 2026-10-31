# Serv5 Backend

Laravel + Filament backend for the Serv5 website. It provides:

- A bilingual (Arabic / English) content-management admin panel for services, portfolio projects, blog posts, products, careers, FAQs, site pages, navigation, testimonials, team members, pricing, bank accounts, and global settings.
- A public JSON API under `/api/v1` for the website frontend.
- Lead capture for contact messages, quotations, project consultations, and career applications.
- A customer-facing Filament panel at `/user` with WhatsApp OTP registration.
- Optional AI content tools (article generation and AI page builder) for Gemini, OpenAI, and DeepSeek.

---

## 1. Tech stack

Versions below are the versions declared in `composer.json` and locked in `composer.lock`.

| Package / tool | Version |
| --- | --- |
| PHP | `^8.2` |
| Laravel Framework | `^12.0` — locked at `v12.61.1` |
| Filament | `^5.6` — locked at `v5.6.6` |
| Filament Shield | `^4.2` — locked at `4.2.0` |
| Filament Language Switch | `*` — locked at `4.3.0` |
| Spatie Laravel Permission | `^6.25` — locked at `6.25.0` |
| Laravel Sanctum | `^4.0` — locked at `4.3.2` |
| Laravel Tinker | `^2.10.1` |
| PHPUnit | `^11.5.50` |
| Vite | `^7.0.7` |
| Tailwind CSS | `^4.0.0` |
| Laravel Vite Plugin | `^2.0.0` |

The backend also uses Laravel queues and the database cache/session/queue drivers by default.

---

## 2. Requirements

- PHP **8.2+**
- Composer **2.x**
- Node.js **20.19+ or 22.12+** (required by Vite 7) and npm
- MySQL **8.x** (the example environment uses MySQL)
- Required PHP extensions:
  - `pdo`, `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `ctype`, `filter`, `hash`, `session`, `tokenizer`
  - `fileinfo`
  - `intl` (required by Filament)
  - `gd` (used by `App\Services\ImageService` for WebP conversion)
  - `xml`, `curl`, `json`

A queue worker is needed for AI jobs and database notifications:

```bash
php artisan queue:work
```

---

## 3. Installation

Run the commands from the `backend/` directory.

```bash
# 1. Install PHP dependencies
composer install

# 2. Create the environment file
#    Windows (PowerShell):
Copy-Item .env.example .env
#    macOS / Linux:
#    cp .env.example .env

# 3. Generate the application key
php artisan key:generate

# 4. Configure the database in .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=serv5
#    DB_USERNAME=...
#    DB_PASSWORD=...

# 5. Install and build frontend assets
npm install
npm run build

# 6. Create the database schema
php artisan migrate

# 7. Seed initial content, roles and the super-admin account
php artisan db:seed

# 8. Create the public storage symlink for uploaded media
php artisan storage:link

# 9. Generate Shield permissions and policies for all resources/pages/widgets
php artisan shield:generate --all

# 10. Assign the super-admin role to a user
php artisan shield:super-admin

# 11. Create an additional Filament panel user (interactive)
php artisan make:filament-user

# 12. Start the development server
php artisan serve
```

The default application URL in `.env.example` is `http://localhost:8000`.

Notes:

- Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` **before** running `php artisan db:seed`. The seeder creates a `super_admin` user with those values and assigns the `super_admin` role.
- `php artisan shield:generate --all` reads the Filament resources, pages, and widgets. Run it after migrations and after the application boots successfully.
- `php artisan shield:super-admin` is interactive; it can create a user or assign the role to an existing one.
- For AI features, enable `AI_CONTENT_ENABLED=true` and add provider tokens through **Admin → AI Content Tools → AI API Credentials**. Tokens are stored encrypted in the database, not in `.env`.
- Turnstile protects public POST endpoints. Set `TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET_KEY`, and optionally `TURNSTILE_HOSTNAME` before using contact/quote/career forms. When the secret is empty outside the `testing` environment, those endpoints return HTTP 503.

---

## 4. Folder structure

```text
backend/
├── app/
│   ├── Filament/
│   │   ├── Pages/                 # Custom admin pages (home/settings/AI/translations)
│   │   ├── Resources/             # Filament CRUD resources for all managed models
│   │   └── Widgets/               # Dashboard stats and recent-leads widgets
│   ├── Http/
│   │   ├── Controllers/           # Web/base controllers
│   │   │   ├── Admin/             # Placeholder admin controller
│   │   │   └── Api/               # Public JSON API controllers
│   │   ├── Middleware/            # Sanctum cookie auth, Turnstile, security headers
│   │   ├── Requests/              # Form-request validation for public submissions
│   │   └── Resources/             # API resource transformations
│   ├── Jobs/                      # Queued AI generation/repair/topic-cluster jobs
│   ├── Models/                    # Eloquent models
│   ├── Notifications/             # Filament/database notification classes
│   ├── Policies/                  # Shield-generated authorization policies
│   ├── Providers/
│   │   ├── AppServiceProvider.php # Rate limiters, Gate::before, language switch
│   │   └── Filament/              # Admin and user panel providers
│   ├── Services/                  # Image handling, post formatting, AI provider services
│   ├── Support/                   # Small support helpers
│   └── Traits/                    # Shared model traits (API cache clearing)
├── bootstrap/                     # Framework bootstrap and provider registration
├── config/                        # Application, auth, database, AI, Shield, etc.
├── database/
│   ├── factories/                 # Model factories (currently UserFactory)
│   ├── migrations/                # Main schema; ai/ holds AI-table migrations
│   └── seeders/                   # Initial content and demo-data seeders
├── docs/                          # API integration documentation
├── lang/                          # en/ar JSON translation files and Laravel lang files
├── postman/                       # Postman collection for the API
├── public/                        # Web root, built assets and uploaded public files
├── resources/
│   ├── css/                       # Tailwind entry point
│   ├── js/                        # Vite JavaScript entry points
│   └── views/                     # Welcome page and Filament custom page views
├── routes/
│   ├── api.php                    # /api/* routes
│   ├── console.php                # Artisan console routes/commands
│   └── web.php                    # Welcome pages and protected quotation downloads
├── storage/                       # Logs, framework cache, private and public uploads
├── tests/
│   ├── Feature/                   # API, auth, AI and authorization feature tests
│   └── Unit/                      # Service/unit tests
├── .env.example                   # Safe environment template
├── artisan                        # Laravel CLI entry point
├── composer.json                  # PHP dependencies and scripts
├── package.json                   # Node/Vite dependencies
├── phpunit.xml                    # PHPUnit configuration (SQLite in-memory for tests)
└── vite.config.js                 # Vite/Tailwind configuration
```

Uploaded media uses the `public` disk and is exposed through `storage:link`. Quotation attachments are stored on the private `local` disk and downloaded through the authenticated route `GET /admin/quotation-files/{quotation}/{filename}`.

---

## 5. Models and relationships

| Model | Represents | Main relationships |
| --- | --- | --- |
| `AiCredential` | Encrypted AI provider credential (Gemini/OpenAI/DeepSeek) with default model and limits | `belongsTo` `User` as creator and updater |
| `AiGenerationRequest` | One AI article/page generation batch | `belongsTo` creator (`User`), `BlogCategory`, author (`User`), `Page`; `hasMany` `AiGenerationItem` |
| `AiGenerationItem` | One keyword/article inside an AI generation batch | `belongsTo` `AiGenerationRequest`; `belongsTo` `Post` |
| `BankAccount` | Bilingual bank-account details for the website | No Eloquent relationships |
| `BlogCategory` | Bilingual blog category | `hasMany` `Post` |
| `Career` | Bilingual job opening | `hasMany` `CareerApplication` |
| `CareerApplication` | Candidate application for a job | `belongsTo` `Career` |
| `ContactMessage` | Contact-form lead/message | No Eloquent relationship; optional `user_id` column |
| `Faq` | Bilingual frequently asked question | No Eloquent relationships |
| `Feature` | Bilingual feature card used on the home page | No Eloquent relationships |
| `FooterLink` | Bilingual footer link grouped by column | No Eloquent relationships |
| `NavLink` | Bilingual header/footer navigation item | `belongsTo` parent `NavLink`; `hasMany` children `NavLink` |
| `Page` | Bilingual CMS page with JSON sections; also used by the page builder and AI page generation | Referenced by `AiGenerationRequest.page_id` |
| `Post` | Bilingual blog post, including SEO and AI metadata | `belongsTo` author (`User`) and `BlogCategory`; referenced by `AiGenerationItem.post_id` |
| `PricingPlan` | Bilingual pricing plan, optionally attached to a service | `belongsTo` `Service` |
| `Product` | Bilingual product with features, specs, samples and images | No Eloquent relationships |
| `Project` | Bilingual portfolio project | No Eloquent relationship; `category` stores a project-category slug |
| `ProjectCategory` | Bilingual portfolio category | No Eloquent relationship; projects reference its slug |
| `ProjectConsultation` | Project consultation lead | `belongsTo` `User` |
| `Quotation` | Detailed quotation request with uploaded files | `belongsTo` `User` |
| `Service` | Bilingual service with page sections and optional parent | `hasMany` `PricingPlan`; `parent_id` column exists but no parent relationship is defined |
| `ServiceTemplate` | Reusable service-page template with block presets | No Eloquent relationships |
| `Setting` | Key/value site settings; values are encrypted at rest | No Eloquent relationships |
| `SocialLink` | Footer social-media link | No Eloquent relationships |
| `Statistic` | Home-page statistic label/count | No Eloquent relationships |
| `TeamMember` | Bilingual team member profile | No Eloquent relationships |
| `Testimonial` | Bilingual client testimonial with optional media | No Eloquent relationships |
| `User` | Filament/Sanctum user with Spatie roles and API tokens | `hasMany` `Post` as author; uses `HasRoles` and `HasApiTokens` |

Most translatable fields (`name`, `title`, `description`, `content`, etc.) are cast to JSON and stored as `{"en": "...", "ar": "..."}`.

---

## 6. Filament panels, resources and pages

### Panels

| Panel | Path | Access |
| --- | --- | --- |
| Admin panel (`admin`) | `/admin` (`/admin/login`) | Users with the `super_admin` role |
| User panel (`user`) | `/user` (`/user/login`, `/user/register`) | Authenticated users who do **not** have `super_admin` |

Both panels discover the resources and pages in `app/Filament`; therefore most resources are also reachable under `/user/...`. Access is still restricted by the panel's `canAccessPanel()` check and by policies.

### Resources

| Resource | Model | Admin URL | Navigation group |
| --- | --- | --- | --- |
| `AiCredentialResource` | `AiCredential` | `/admin/ai-credentials` | AI Content Tools |
| `BankAccountResource` | `BankAccount` | `/admin/bank-accounts` | Settings |
| `BlogCategoryResource` | `BlogCategory` | `/admin/blog-categories` | Content |
| `CareerApplicationResource` | `CareerApplication` | `/admin/career-applications` | Leads |
| `CareerResource` | `Career` | `/admin/careers` | Careers |
| `ContactMessageResource` | `ContactMessage` | `/admin/contact-messages` | Leads |
| `FaqResource` | `Faq` | `/admin/faqs` | Content |
| `FeatureResource` | `Feature` | `/admin/features` | Home Page |
| `FooterLinkResource` | `FooterLink` | `/admin/footer-links` | Footer |
| `NavLinkResource` | `NavLink` | `/admin/nav-links` | Header |
| `PageBuilderResource` | `Page` | `/admin/page-builders` | System Settings |
| `PageResource` | `Page` | `/admin/pages` | — |
| `PostResource` | `Post` | `/admin/posts` | Content |
| `PricingPlanResource` | `PricingPlan` | `/admin/pricing-plans` | Content |
| `ProductResource` | `Product` | `/admin/products` | Content |
| `ProjectCategoryResource` | `ProjectCategory` | `/admin/project-categories` | Content |
| `ProjectConsultationResource` | `ProjectConsultation` | `/admin/project-consultations` | Leads |
| `ProjectResource` | `Project` | `/admin/projects` | Content |
| `QuotationResource` | `Quotation` | `/admin/quotations` | Leads |
| `ServiceResource` | `Service` | `/admin/services` | Content |
| `ServiceTemplateResource` | `ServiceTemplate` | `/admin/service-templates` | Content |
| `SettingResource` | `Setting` | `/admin/settings` | SEO & Settings |
| `SocialLinkResource` | `SocialLink` | `/admin/social-links` | Footer |
| `TeamMemberResource` | `TeamMember` | `/admin/team-members` | Home Page |
| `TestimonialResource` | `Testimonial` | `/admin/testimonials` | Home Page |
| `UserResource` | `User` | `/admin/users` | System Settings |
| Shield `RoleResource` | Spatie `Role` | `/admin/shield/roles` | System Settings |

Some resources have `shouldRegisterNavigation = false` but remain routable at the URLs above (for example `PostResource`, `ProjectResource`, `ProductResource`, `PageResource`, `SettingResource`, `CareerResource`, and `CareerApplicationResource`).

### Custom pages

| Page | Admin URL | Purpose |
| --- | --- | --- |
| `Dashboard` | `/admin` | Filament dashboard with stats and recent leads |
| `ManageHomePage` | `/admin/manage-home-page` | Home-page section settings |
| `ManageHomeHero` | `/admin/manage-home-hero` | Hero section |
| `ManageHomeFeaturesSettings` | `/admin/manage-home-features-settings` | Home features settings |
| `ManageHomeCTA` | `/admin/manage-home-c-t-a` | Home call-to-action |
| `ManageHeader` | `/admin/manage-header` | Header settings |
| `ManageFooter` | `/admin/manage-footer` | Footer settings |
| `ManageAboutPage` | `/admin/manage-about-page` | About page content |
| `ManageContactPage` | `/admin/manage-contact-page` | Contact page content |
| `ManageFaqPage` | `/admin/manage-faq-page` | FAQ page content |
| `SitePages` | `/admin/site-pages` | Content/page overview |
| `ManageSeoSettings` | `/admin/manage-seo-settings` | SEO settings |
| `ManageMediaSettings` | `/admin/manage-media-settings` | Images and media settings |
| `ManageTranslations` | `/admin/manage-translations` | UI translation overrides |
| `AiGenerationDashboard` | `/admin/ai-generation-dashboard` | AI article generator (shown only when `AI_CONTENT_ENABLED=true`) |
| `AiPageBuilder` | `/admin/ai-page-builder` | AI page builder (shown only when `AI_CONTENT_ENABLED=true`) |
| `AiGenerationHistory` | `/admin/ai-generation-history` | AI generation history |

---

## 7. API

The API is versioned under `/api/v1`. The complete route list can be regenerated with:

```bash
php artisan route:list --path=api
```

There are currently 35 API routes.

### Authentication

Authentication uses **Laravel Sanctum personal access tokens**, delivered to browser clients as an **HttpOnly cookie**:

1. `POST /api/v1/register` or `POST /api/v1/login` creates a Sanctum token.
2. The response sets the token in the `auth_token` HttpOnly cookie (`AUTH_COOKIE_MINUTES`, default 30 days; `SameSite`, `Secure`, and domain controlled by `AUTH_COOKIE_*` variables).
3. The `token.cookie` middleware (`AuthenticateTokenFromCookie`) copies that cookie into an `Authorization: Bearer <token>` header before Sanctum authenticates the request.
4. `POST /api/v1/logout` deletes the current access token and clears the cookie.
5. Bearer tokens are only returned in JSON when the app is **not** in `production` and `AUTH_EXPOSE_TOKENS=true`.

Protected routes use `auth:sanctum`. Public read routes are limited to 120 requests/minute per IP. Public write routes use stricter throttles and Cloudflare Turnstile verification.

### Routes

| Method | URI | Controller / action | Auth |
| --- | --- | --- | --- |
| GET | `/api/user` | Closure (returns authenticated user) | Sanctum |
| GET | `/api/login` | Closure (returns 401 `Unauthenticated.`) | Public |
| POST | `/api/v1/register` | `Api\AuthController@register` | Public · 5/min · Turnstile |
| POST | `/api/v1/login` | `Api\AuthController@login` | Public · 5/min · Turnstile |
| POST | `/api/v1/logout` | `Api\AuthController@logout` | Sanctum |
| GET | `/api/v1/me` | `Api\AuthController@me` | Sanctum |
| POST | `/api/v1/change-password` | `Api\AuthController@changePassword` | Sanctum |
| GET | `/api/v1/services` | `Api\ServiceController@index` | Public · 120/min |
| GET | `/api/v1/services/{slug}` | `Api\ServiceController@show` | Public · 120/min |
| GET | `/api/v1/portfolio` | `Api\PortfolioController@index` | Public · 120/min |
| GET | `/api/v1/portfolio/categories` | `Api\PortfolioController@categories` | Public · 120/min |
| GET | `/api/v1/portfolio/{slug}` | `Api\PortfolioController@show` | Public · 120/min |
| GET | `/api/v1/blog` | `Api\BlogController@index` | Public · 120/min |
| GET | `/api/v1/blog/categories` | `Api\BlogController@categories` | Public · 120/min |
| GET | `/api/v1/blog/trending` | `Api\BlogController@trending` | Public · 120/min |
| GET | `/api/v1/blog/{slug}` | `Api\BlogController@show` | Public · 120/min |
| GET | `/api/v1/faqs` | `Api\FaqController@index` | Public · 120/min |
| GET | `/api/v1/products` | `Api\ProductController@index` | Public · 120/min |
| GET | `/api/v1/products/categories` | `Api\ProductController@categories` | Public · 120/min |
| GET | `/api/v1/products/{slug}` | `Api\ProductController@show` | Public · 120/min |
| GET | `/api/v1/careers` | `Api\CareerController@index` | Public · 120/min |
| GET | `/api/v1/settings` | `Api\SettingController@settings` | Public · 120/min |
| GET | `/api/v1/testimonials` | `Api\SettingController@testimonials` | Public · 120/min |
| GET | `/api/v1/team` | `Api\SettingController@team` | Public · 120/min |
| GET | `/api/v1/features` | `Api\SettingController@features` | Public · 120/min |
| GET | `/api/v1/footer-data` | `Api\SettingController@footer` | Public · 120/min |
| GET | `/api/v1/nav-links` | `Api\SettingController@navLinks` | Public · 120/min |
| GET | `/api/v1/pricing-plans` | `Api\SettingController@pricingPlans` | Public · 120/min |
| GET | `/api/v1/bank-accounts` | `Api\SettingController@bankAccounts` | Public · 120/min |
| GET | `/api/v1/pages` | `Api\PageController@index` | Public · 120/min |
| GET | `/api/v1/pages/{slug}` | `Api\PageController@show` | Public · 120/min |
| POST | `/api/v1/careers/apply` | `Api\CareerController@apply` | Public · 10/min · Turnstile |
| POST | `/api/v1/contact` | `Api\ContactController@submitMessage` | Public · 10/min · Turnstile |
| POST | `/api/v1/quotations` | `Api\ContactController@submitQuotation` | Public · 3/min · Turnstile |
| POST | `/api/v1/consultations` | `Api\ContactController@submitConsultation` | Public · 10/min · Turnstile |

Localized read endpoints accept `?lang=ar` or `?lang=en`; otherwise they read `Accept-Language` and fall back to English.

Additional web routes:

| Method | URI | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/`, `/ar`, `/en` | Welcome view | Public |
| GET | `/admin/quotation-files/{quotation}/{filename}` | Download a private quotation attachment | Filament session auth (`auth`) |

The API integration guide for blog content is in `docs/blog-content-api.md`, and a Postman collection is in `postman/`.

---

## 8. Roles and permissions

The project uses **Spatie Laravel Permission** for roles/permissions and **Filament Shield** to generate and manage them.

- `User` uses `Spatie\Permission\Traits\HasRoles`.
- The migration `create_permission_tables` creates the standard Spatie tables (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`).
- Shield is registered in both Filament panels. Its role manager is available at `/admin/shield/roles`.
- `config/filament-shield.php` uses `:` as the permission separator and PascalCase names, for example `ViewAny:Service`, `Update:Post`.
- Shield-generated policies live in `app/Policies` and check permissions such as `ViewAny:Service`.
- `AppServiceProvider` registers a `Gate::before` hook that grants every ability to users with the `super_admin` role.
- `DatabaseSeeder` creates these roles:
  - `super_admin` — unrestricted via the gate hook.
  - `read_only` — assigned automatically to every newly created user by `User::booted()`.
- The Shield config also defines a `panel_user` role; it is not created by `DatabaseSeeder`.
- `User::canAccessPanel()` allows only `super_admin` users into the `admin` panel and only non-`super_admin` users into the `user` panel.

Generate permissions/policies after changing Filament resources or pages:

```bash
php artisan shield:generate --all
```

Assign the super-admin role:

```bash
php artisan shield:super-admin
```

Seed credentials are read from `ADMIN_EMAIL` and `ADMIN_PASSWORD`. Never commit real values.

---

## 9. Languages and localization

- Supported locales: **Arabic (`ar`)** and **English (`en`)**.
- Default locale: `APP_LOCALE=en`; fallback: `APP_FALLBACK_LOCALE=en`.
- `bezhansalleh/filament-language-switch` is configured in `AppServiceProvider` with `->locales(['ar', 'en'])`.
- `lang/en.json` and `lang/ar.json` contain the UI translation keys used by Filament and custom pages.
- Standard Laravel language files are under `lang/en/` (`auth.php`, `pagination.php`, `passwords.php`, `validation.php`).
- Translatable model fields are stored as JSON (`{"en": "...", "ar": "..."}`) and edited as separate EN/AR fields in Filament.
- The API selects the language from `?lang=ar|en` or `Accept-Language`, defaulting to English.
- **Admin → System Settings → Translations** (`/admin/manage-translations`) stores editable translation overrides in the `settings` table.

---

## 10. Useful commands

```bash
# Install PHP dependencies
composer install

# Start the full local development stack:
# PHP server + queue listener + Pail logs + Vite dev server
composer run dev

# Clear all Laravel caches (config, route, view, cache)
php artisan optimize:clear

# Rebuild the database and seed it (DESTRUCTIVE)
php artisan migrate:fresh --seed

# Run the test suite (clears config cache first)
composer test

# Run tests directly
php artisan test

# Run one test file or filter
php artisan test tests/Feature/AuthTest.php
php artisan test --filter=ContactApiTest

# Regenerate Shield permissions/policies
php artisan shield:generate --all

# Process queued jobs (AI generation, notifications)
php artisan queue:work

# List routes
php artisan route:list
php artisan route:list --path=api
```

`phpunit.xml` configures tests to use SQLite `:memory:`, the `array` cache/session drivers, and the `sync` queue, so tests do not need the local MySQL database.

---

## 11. Common errors and fixes

| Error | Cause | Fix |
| --- | --- | --- |
| `No application encryption key has been specified` | `.env` is missing or `APP_KEY` is empty | Copy `.env.example` to `.env` and run `php artisan key:generate` |
| `could not find driver` | `pdo_mysql` is not enabled, or `DB_CONNECTION` is not `mysql` | Enable the PHP `pdo_mysql` extension and set `DB_CONNECTION=mysql` |
| `SQLSTATE[HY000] [1045] Access denied for user ...` | Wrong database username/password or insufficient privileges | Correct `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` and grant the user access |
| `SQLSTATE[42S02] Base table or view not found` | Migrations have not run | Run `php artisan migrate` |
| `Class "..." not found` | Stale Composer autoload files or missing dependencies | Run `composer install` then `composer dump-autoload`, followed by `php artisan optimize:clear` |
| Filament page shows old code/assets | Cached config/views or stale assets | Run `php artisan optimize:clear`, then `npm run build` (or `npm run dev`) |
| Uploaded images return 404 | Public storage link is missing | Run `php artisan storage:link` |
| `CAPTCHA is not configured on the server` (HTTP 503) | `TURNSTILE_SECRET_KEY` is empty | Set the Turnstile keys, or run API tests in the `testing` environment |
| AI jobs stay `queued` | No queue worker is running | Start `php artisan queue:work` |
| `Shield` permissions are missing | Permissions/policies were not generated | Run `php artisan shield:generate --all` |
| User cannot open `/admin` | The user does not have the `super_admin` role | Run `php artisan shield:super-admin` or assign the role in `/admin/shield/roles` |
| `419 Page Expired` on form submissions | CSRF/session issue | Clear caches, verify `SESSION_DRIVER=database`, and ensure cookies/domain settings are correct |

---

## 12. Environment files

- `.env` is **not** committed and must be created locally from `.env.example`.
- `.env.example` contains placeholders only; it contains no real credentials.
- The repository root `.gitignore` currently ignores `.env*` (including this backend `.env.example`). To track the example file, either add a negation rule for `backend/.env.example` in the root `.gitignore`, or force-add it with `git add -f backend/.env.example`.
