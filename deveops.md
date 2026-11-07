# Serv5 — Full cPanel DevOps & Deployment Guide (`deveops.md`)

> **Repository**: `https://github.com/yousefhatem12/Refactor_serv.git`  
> **Target Branch**: `refactor`  
> **Stack**: Next.js 16 (React 19) Frontend + Laravel 12 (PHP 8.3 + Filament v5 Admin) Backend + MySQL/MariaDB (`utf8mb4`)  
> **Included Data**: Full Bilingual (`EN` / `AR`) `serv5.com` WordPress XML Content (`00201288863631.WordPress.2026-11-05.xml`) pre-parsed into Laravel JSON Seeders (`backend/database/seeders/data/`).

---

## Table of Contents
1. [Server & cPanel Requirements](#1-server--cpanel-requirements)
2. [Recommended Domain & Directory Structure on cPanel](#2-recommended-domain--directory-structure-on-cpanel)
3. [Step 1 — Clone the `refactor` Branch from GitHub](#step-1--clone-the-refactor-branch-from-github)
4. [Step 2 — Create MySQL Database & User in cPanel](#step 2--create-mysql-database--user-in-cpanel)
5. [Step 3 — Deploy Laravel 12 Backend & Seed Full XML Content](#step-3--deploy-laravel-12-backend--seed-full-xml-content)
6. [Step 4 — Deploy Next.js 16 Frontend on cPanel](#step-4--deploy-nextjs-16-frontend-on-cpanel)
7. [Step 5 — Admin Dashboard & Live Frontend Sync Verification](#step-5--admin-dashboard--live-frontend-sync-verification)
8. [Step 6 — Running Locally (For Development / Testing)](#step-6--running-locally-for-development--testing)
9. [دليل الرفع والتشغيل الكامل على cPanel (باللغة العربية)](#9-دليل-الرفع-والتشغيل-الكامل-على-cpanel-باللغة-العربية)

---

## 1. Server & cPanel Requirements

| Component | Required Version | Notes |
| :--- | :--- | :--- |
| **PHP** | **PHP 8.3+** (`^8.3`) | Enable in cPanel **MultiPHP Manager** |
| **PHP Extensions** | `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`, `intl`, `zip`, `bcmath`, `xml` | Enable in cPanel **Select PHP Version** / **PHP Extensions** |
| **Composer** | **Composer 2.x** (`>= 2.7`) | Available in cPanel Terminal |
| **MySQL / MariaDB** | MySQL `8.0+` or MariaDB `10.4+` | Charset `utf8mb4`, Collation `utf8mb4_unicode_ci` |
| **Node.js** | **Node.js 20 LTS** (or `22 LTS`) | Configure via cPanel **Setup Node.js App** |

---

## 2. Recommended Domain & Directory Structure on cPanel

The project includes a ready-to-use [`.cpanel.yml`](.cpanel.yml) file that supports both **Automatic cPanel Git™ Version Control Deployment** and **Manual Terminal Deployment**:

* **Automatic `.cpanel.yml` Target Paths**:
  * **Frontend (Next.js)**: `/home2/serv5eg/public_html/serv5-frontend2026` (Startup file: `server.js`)
  * **Backend (Laravel 12)**: `/home2/serv5eg/public_html/serv5-backend2026` (Document Root: `/home2/serv5eg/public_html/serv5-backend2026/public`)
* **Domains**:
  * **Main Website Domain** (`https://serv5.com.eg` or `https://serv5.com`) $\rightarrow$ points to **Next.js Frontend** (`serv5-frontend2026`, `server.js`).
  * **API & Admin Subdomain** (`https://api.serv5.com.eg`) $\rightarrow$ points to **Laravel Backend** (`serv5-backend2026/public`).

---

## Method A — Automatic Deployment via cPanel Git™ Version Control (`.cpanel.yml`)

1. Open **cPanel $\rightarrow$ Git™ Version Control $\rightarrow$ Create**.
2. Set:
   - **Clone URL**: `https://github.com/yousefhatem12/Refactor_serv.git`
   - **Repository Path**: `/home2/serv5eg/repositories/Refactor_serv`
   - **Repository Name**: `Refactor_serv`
3. After cloning, switch the active branch in cPanel Git™ Version Control (or via Terminal inside `/home2/serv5eg/repositories/Refactor_serv`) to **`refactor`**:
   ```bash
   cd /home2/serv5eg/repositories/Refactor_serv
   git checkout refactor
   git pull origin refactor
   ```
4. Click **Manage $\rightarrow$ Pull or Deploy $\rightarrow$ Deploy HEAD Commit**.
5. [`.cpanel.yml`](.cpanel.yml) will automatically:
   - Copy the Next.js frontend to `/home2/serv5eg/public_html/serv5-frontend2026`
   - Copy the Laravel 12 backend to `/home2/serv5eg/public_html/serv5-backend2026`
   - Run `php artisan migrate --force`
   - Run `php artisan db:seed --force` (importing the entire `serv5.com` XML content!)
   - Run `php artisan optimize:clear`, `storage:link`, `config:cache`, `route:cache`, `view:cache`
   - Touch `tmp/restart.txt` to reload the Next.js Passenger app!

> **Note for First-Time Setup on `.cpanel.yml` paths**: Make sure `/home2/serv5eg/public_html/serv5-backend2026/.env` is configured with your MySQL credentials, and run `composer install --no-dev --optimize-autoloader` inside `serv5-backend2026` and `npm install && npm run build` inside `serv5-frontend2026` once.

---

## Method B — Step-by-Step Terminal Deployment on cPanel

## Step 1 — Clone the `refactor` Branch from GitHub

Open **cPanel $\rightarrow$ Terminal** (or SSH) and run:

```bash
cd ~
git clone -b refactor https://github.com/yousefhatem12/Refactor_serv.git serv5
cd ~/serv5
```

*(If the folder already exists and you want to pull the latest updates from `refactor`)*:
```bash
cd ~/serv5
git fetch origin refactor
git checkout refactor
git pull origin refactor
```

---

## Step 2 — Create MySQL Database & User in cPanel

1. Open **cPanel $\rightarrow$ MySQL® Databases**.
2. Create a new database (e.g., `cpaneluser_serv5`).
3. Create a MySQL user (e.g., `cpaneluser_serv5_usr`) with a strong password.
4. Add the user to the database with **ALL PRIVILEGES**.

---

## Step 3 — Deploy Laravel 12 Backend & Seed Full XML Content

### 3.1 Configure `backend/.env`
In cPanel Terminal:

```bash
cd ~/serv5/backend
cp .env.example .env
nano .env
```

Update the following keys in `backend/.env`:

```env
APP_NAME="Serv5"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.serv5.com.eg

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpaneluser_serv5
DB_USERNAME=cpaneluser_serv5_usr
DB_PASSWORD="YOUR_STRONG_DB_PASSWORD"

# Super Admin Credentials seeded by DatabaseSeeder
ADMIN_EMAIL=admin@serv5.com
ADMIN_PASSWORD="YOUR_STRONG_ADMIN_PASSWORD"

# Sanctum & CORS (Include your frontend domain)
SANCTUM_STATEFUL_DOMAINS=serv5.com.eg,www.serv5.com.eg,serv5.com,www.serv5.com,localhost:3000
CORS_ALLOWED_ORIGINS="https://serv5.com.eg,https://www.serv5.com.eg,https://serv5.com,https://www.serv5.com,http://localhost:3000"
```

### 3.2 Install Composer Dependencies & Generate App Key

```bash
cd ~/serv5/backend
composer install --no-dev --optimize-autoloader --no-interaction
php artisan key:generate --force
php artisan storage:link
```

### 3.3 Run Migrations & Seed the Full `serv5.com` XML Content
All content from the WordPress export file (`00201288863631.WordPress.2026-11-05.xml`) has already been extracted, cleaned, paired in **English (`en`) & Arabic (`ar`)**, and saved into `backend/database/seeders/data/*.json` (and the raw XML is also archived at `backend/database/seeders/data/serv5_wordpress_export.xml`).

Run a single migration + seed command to populate the entire database:

```bash
cd ~/serv5/backend
php artisan migrate --force
php artisan db:seed --force
```

**What `php artisan db:seed --force` imports automatically:**
- **1,091 Bilingual Blog Posts** (`posts` + `blog_categories`) — chunked safely (10 per query) so MySQL `max_allowed_packet` is never exceeded.
- **361 Portfolio Projects** (`projects` + `project_categories`) — with bilingual featured projects prioritized at the top.
- **16 Services** (`services`) — Web Design, Web Hosting, Mobile Apps, E-Commerce, Digital Marketing, SEO, etc.
- **16 Digital Products / Scripts** (`products`) — Classifieds, Watch & Earn, App Store, Tourism, Real Estate, etc.
- **4 Web Hosting Pricing Plans** (`pricing_plans`).
- **14 Bank & Payment Accounts** (`bank_accounts`) — CIB, NBE, Al Rajhi, Banque Misr, Vodafone Cash, PayPal, Western Union, etc.
- **12 Careers / Job Openings** (`careers`) & **12 FAQs** (`faqs`).
- **3 Leadership Team Members** (`team_members`), **8 Pages** (`pages`), **Header/Footer Navigation**, and **20 Global Site Settings** (`settings`).

### 3.4 Optimize Laravel Caches & Set Permissions

```bash
cd ~/serv5/backend
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

---

## Step 4 — Deploy Next.js 16 Frontend on cPanel

### 4.1 Create `.env.local` in Project Root (`~/serv5/.env.local`)

```bash
cd ~/serv5
nano .env.local
```

Add your production API and site URLs:

```env
NEXT_PUBLIC_SITE_URL=https://serv5.com.eg
INTERNAL_API_URL=https://api.serv5.com.eg/api/v1
NEXT_PUBLIC_API_URL=https://api.serv5.com.eg/api/v1
BACKEND_API_URL=https://api.serv5.com.eg/api/v1
```

### 4.2 Install Dependencies & Build Next.js App

```bash
cd ~/serv5
npm install
npm run build
```

### 4.3 Start Next.js in cPanel (Two Options)

#### Option A: Using cPanel "Setup Node.js App" (CloudLinux Passenger)
1. Go to **cPanel $\rightarrow$ Setup Node.js App $\rightarrow$ Create Application**.
2. Set:
   - **Node.js version**: `20.x` (or `22.x`)
   - **Application mode**: `Production`
   - **Application root**: `serv5`
   - **Application URL**: `serv5.com.eg`
   - **Application startup file**: `node_modules/next/dist/bin/next` (or `server.js`)
3. Click **Create** and then **Restart**.

#### Option B: Using PM2 / Reverse Proxy (VPS / Dedicated cPanel)
```bash
cd ~/serv5
pm2 start npm --name "serv5-frontend" -- start
pm2 save
```

---

## Step 5 — Admin Dashboard & Live Frontend Sync Verification

1. **Open the Filament Admin Dashboard**:
   - Production: `https://api.serv5.com.eg/admin`
   - Local: `http://127.0.0.1:8000/admin`
2. **Default Login Credentials**:
   - **Email**: `admin@serv5.com`
   - **Password**: `admin123456` *(or the `ADMIN_PASSWORD` set in `backend/.env`)*
3. **Zero-Delay Live Sync**:
   - Every Eloquent model (`Post`, `BlogCategory`, `Project`, `ProjectCategory`, `Service`, `PricingPlan`, `Product`, `Career`, `Faq`, `BankAccount`, `Page`, `TeamMember`, `Testimonial`, `Feature`, `NavLink`, `FooterLink`, `SocialLink`, `Setting`) uses the `ClearsApiCache` trait.
   - Whenever an admin creates, edits, or deletes any item in `/admin`, Laravel automatically increments the cache version (`api_cache_version:{scope}`), and the Next.js frontend reflects the change immediately without needing a rebuild or server restart.

---

## Step 6 — Running Locally (For Development / Testing)

To run the entire project locally on Windows with a single command:

```powershell
cd c:\Users\NV_USER\Desktop\serv5-last-last
npm run dev:full
```

Or manually in two terminals:
1. **Backend (`http://127.0.0.1:8000`)**:
   ```powershell
   Start-Process "C:\xampp\mysql\bin\mysqld.exe" -ArgumentList "--defaults-file=C:\xampp\mysql\bin\my.ini" -WindowStyle Hidden
   cd backend
   php artisan serve --host=127.0.0.1 --port=8000
   ```
2. **Frontend (`http://localhost:3000`)**:
   ```powershell
   npm run dev
   ```

---

## 9. دليل الرفع والتشغيل الكامل على cPanel (باللغة العربية)

### أولاً: المتطلبات الأساسية على cPanel
1. **إصدار PHP**: تفعيل **PHP 8.3** من داخل `MultiPHP Manager` في لوحة cPanel.
2. **إضافات PHP المطلوبة**: التأكد من تفعيل (`pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`, `intl`, `zip`, `bcmath`, `xml`).
3. **قاعدة البيانات**: إنشاء قاعدة بيانات MySQL ومستخدم وربطهما بكافة الصلاحيات (`ALL PRIVILEGES`) من `MySQL® Databases`.
4. **Node.js**: تفعيل **Node.js 20+** لتشغيل واجهة Next.js.

### ثانياً: سحب المشروع من GitHub (فرع `refactor`)
افتح **Terminal** داخل cPanel ونفذ الأوامر التالية:

```bash
cd ~
git clone -b refactor https://github.com/yousefhatem12/Refactor_serv.git serv5
cd ~/serv5
```

### ثالثاً: إعداد الباك إند (Laravel 12) ورفع محتوى ملف الـ XML بالكامل
1. اربط صب-دومين الـ API (مثل `api.serv5.com.eg`) ليشير إلى المسار:
   `/home/<cpanel_user>/serv5/backend/public`
2. انسخ ملف البيئة وعدل بيانات قاعدة البيانات والدومين:
   ```bash
   cd ~/serv5/backend
   cp .env.example .env
   nano .env
   ```
3. ثبّت الحزم وأنشئ مفتاح التطبيق واربط الجداول وبيانات ملف الـ XML:
   ```bash
   composer install --no-dev --optimize-autoloader --no-interaction
   php artisan key:generate --force
   php artisan storage:link
   php artisan migrate --force
   php artisan db:seed --force
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   > **ملاحظة هامة**: الأمر `php artisan db:seed --force` يقوم تلقائياً برفع كل محتوى موقع `serv5.com` المستخرج من ملف الـ XML (عربي + إنجليزي) بالكامل داخل قاعدة البيانات (1,091 مقال مدونة، 361 عمل في سابقة الأعمال، 16 خدمة، 16 منتج رقمي، 4 باقات استضافة، 14 حساب بنكي، 12 وظيفة، 12 سؤال شائع، فريق العمل، الصفحات، والإعدادات)، كما توجد نسخة من ملف الـ XML الأصلي في `backend/database/seeders/data/serv5_wordpress_export.xml`.

### رابعاً: إعداد الفرونت إند (Next.js 16)
1. أنشئ ملف `.env.local` في المجلد الرئيسي `~/serv5/.env.local`:
   ```env
   NEXT_PUBLIC_SITE_URL=https://serv5.com.eg
   INTERNAL_API_URL=https://api.serv5.com.eg/api/v1
   NEXT_PUBLIC_API_URL=https://api.serv5.com.eg/api/v1
   BACKEND_API_URL=https://api.serv5.com.eg/api/v1
   ```
2. قم بتثبيت الحزم وعمل Build للمشروع:
   ```bash
   cd ~/serv5
   npm install
   npm run build
   ```
3. شغّل تطبيق Next.js من خلال **Setup Node.js App** في cPanel أو عبر `pm2 start npm --name "serv5-frontend" -- start`.

### خامساً: الدخول إلى لوحة التحكم (Admin Dashboard)
- **الرابط**: `https://api.serv5.com.eg/admin` (أو محلياً: `http://127.0.0.1:8000/admin`)
- **البريد الإلكتروني**: `admin@serv5.com`
- **كلمة المرور**: `admin123456`
- أي مقال أو مشروع أو منتج أو تعديل يتم إضافته من لوحة التحكم يظهر **فوراً** في موقع Next.js بدون الحاجة لإعادة بناء الموقع.
