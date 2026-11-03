# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: smoke.spec.ts >> Homepage >> security headers are present
- Location: e2e\smoke.spec.ts:30:7

# Error details

```
Error: apiRequestContext.get: connect ETIMEDOUT 141.95.125.36:443
Call log:
  - → GET https://serv5.com.eg/ar
    - user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36
    - accept: */*
    - accept-encoding: gzip,deflate,br
    - Accept-Language: ar,en;q=0.9

```

# Test source

```ts
  1   | import { test, expect } from '@playwright/test';
  2   | 
  3   | /**
  4   |  * Critical path E2E tests for serv5.com.eg
  5   |  *
  6   |  * These smoke tests verify that the most important user-facing pages load
  7   |  * correctly and that the API-driven content is rendered.  They run against
  8   |  * the live production URL (or BASE_URL env var) so they act as both a
  9   |  * post-deploy health-check and a regression guard.
  10  |  */
  11  | 
  12  | // ── Helpers ──────────────────────────────────────────────────────────────────
  13  | 
  14  | /** Navigate and wait for the network to quiet down. */
  15  | async function goto(page: any, path: string) {
  16  |   await page.goto(path, { waitUntil: 'domcontentloaded' });
  17  | }
  18  | 
  19  | // ── Homepage ──────────────────────────────────────────────────────────────────
  20  | 
  21  | test.describe('Homepage', () => {
  22  |   test('loads with HTTP 200 and renders hero content', async ({ page }) => {
  23  |     const res = await page.goto('/ar');
  24  |     expect(res?.status()).toBe(200);
  25  | 
  26  |     // Navbar should be present
  27  |     await expect(page.locator('nav')).toBeVisible({ timeout: 10_000 });
  28  |   });
  29  | 
  30  |   test('security headers are present', async ({ request }) => {
> 31  |     const res = await request.get('/ar');
      |                               ^ Error: apiRequestContext.get: connect ETIMEDOUT 141.95.125.36:443
  32  |     expect(res.status()).toBe(200);
  33  | 
  34  |     const h = res.headers();
  35  |     expect(h['x-content-type-options']).toBe('nosniff');
  36  |     expect(h['x-frame-options']).toMatch(/SAMEORIGIN/i);
  37  |     expect(h['referrer-policy']).toBeTruthy();
  38  |     expect(h['content-security-policy']).toBeTruthy();
  39  |   });
  40  | 
  41  |   test('no console errors on load', async ({ page }) => {
  42  |     const errors: string[] = [];
  43  |     page.on('console', (msg) => {
  44  |       if (msg.type() === 'error') errors.push(msg.text());
  45  |     });
  46  |     await goto(page, '/ar');
  47  |     await page.waitForTimeout(2000);
  48  |     // Filter out known benign errors (e.g. browser extension noise)
  49  |     const realErrors = errors.filter(
  50  |       (e) => !e.includes('Extension') && !e.includes('chrome-extension')
  51  |     );
  52  |     expect(realErrors).toHaveLength(0);
  53  |   });
  54  | });
  55  | 
  56  | // ── Responsive (320px mobile) ─────────────────────────────────────────────────
  57  | 
  58  | test.describe('Responsive layout @ 320px', () => {
  59  |   test.use({ viewport: { width: 320, height: 568 } });
  60  | 
  61  |   test('homepage has no horizontal overflow', async ({ page }) => {
  62  |     await goto(page, '/ar');
  63  |     const bodyWidth = await page.evaluate(() => document.body.scrollWidth);
  64  |     const viewportWidth = await page.evaluate(() => window.innerWidth);
  65  |     expect(bodyWidth).toBeLessThanOrEqual(viewportWidth);
  66  |   });
  67  | 
  68  |   test('nav does not overflow viewport', async ({ page }) => {
  69  |     await goto(page, '/ar');
  70  |     const nav = page.locator('nav').first();
  71  |     await expect(nav).toBeVisible();
  72  |     const navBox = await nav.boundingBox();
  73  |     if (navBox) expect(navBox.width).toBeLessThanOrEqual(320 + 1);
  74  |   });
  75  | });
  76  | 
  77  | // ── Services ──────────────────────────────────────────────────────────────────
  78  | 
  79  | test.describe('Services page', () => {
  80  |   test('loads and shows at least one service card', async ({ page }) => {
  81  |     await goto(page, '/ar/services');
  82  |     await expect(page.locator('main')).toBeVisible({ timeout: 10_000 });
  83  |   });
  84  | });
  85  | 
  86  | // ── Blog ─────────────────────────────────────────────────────────────────────
  87  | 
  88  | test.describe('Blog page', () => {
  89  |   test('returns 200 and renders blog listing', async ({ page }) => {
  90  |     const res = await page.goto('/ar/blog', { waitUntil: 'domcontentloaded' });
  91  |     expect(res?.status()).toBe(200);
  92  |     await expect(page.locator('main')).toBeVisible({ timeout: 10_000 });
  93  |   });
  94  | });
  95  | 
  96  | // ── Portfolio ─────────────────────────────────────────────────────────────────
  97  | 
  98  | test.describe('Portfolio page', () => {
  99  |   test('loads successfully', async ({ page }) => {
  100 |     const res = await page.goto('/ar/portfolio', { waitUntil: 'domcontentloaded' });
  101 |     expect(res?.status()).toBe(200);
  102 |     await expect(page.locator('main')).toBeVisible({ timeout: 10_000 });
  103 |   });
  104 | });
  105 | 
  106 | // ── Contact form ──────────────────────────────────────────────────────────────
  107 | 
  108 | test.describe('Contact page', () => {
  109 |   test('shows contact form with required inputs', async ({ page }) => {
  110 |     await goto(page, '/ar/contact');
  111 |     // The form should be present — Turnstile widget will load in real browsers
  112 |     await expect(page.locator('form')).toBeVisible({ timeout: 10_000 });
  113 |   });
  114 | 
  115 |   test('form submit button is present and enabled', async ({ page }) => {
  116 |     await goto(page, '/ar/contact');
  117 |     const btn = page.locator('form button[type="submit"]').first();
  118 |     await expect(btn).toBeVisible({ timeout: 10_000 });
  119 |   });
  120 | });
  121 | 
  122 | // ── 404 ───────────────────────────────────────────────────────────────────────
  123 | 
  124 | test.describe('404 page', () => {
  125 |   test('non-existent path returns 404', async ({ page }) => {
  126 |     const res = await page.goto('/ar/this-page-does-not-exist-xyz');
  127 |     // Next.js returns 200 for client-rendered 404 pages in some configs
  128 |     expect([200, 404]).toContain(res?.status());
  129 |   });
  130 | });
  131 | 
```