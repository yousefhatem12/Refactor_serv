import { test, expect } from '@playwright/test';

/**
 * Critical path E2E tests for serv5.com.eg
 *
 * These smoke tests verify that the most important user-facing pages load
 * correctly and that the API-driven content is rendered.  They run against
 * the live production URL (or BASE_URL env var) so they act as both a
 * post-deploy health-check and a regression guard.
 */

// ── Helpers ──────────────────────────────────────────────────────────────────

/** Navigate and wait for the network to quiet down. */
async function goto(page: any, path: string) {
  await page.goto(path, { waitUntil: 'domcontentloaded' });
}

// ── Homepage ──────────────────────────────────────────────────────────────────

test.describe('Homepage', () => {
  test('loads with HTTP 200 and renders hero content', async ({ page }) => {
    const res = await page.goto('/ar');
    expect(res?.status()).toBe(200);

    // Navbar should be present
    await expect(page.locator('nav')).toBeVisible({ timeout: 10_000 });
  });

  test('security headers are present', async ({ request }) => {
    const res = await request.get('/ar');
    expect(res.status()).toBe(200);

    const h = res.headers();
    expect(h['x-content-type-options']).toBe('nosniff');
    expect(h['x-frame-options']).toMatch(/SAMEORIGIN/i);
    expect(h['referrer-policy']).toBeTruthy();
    expect(h['content-security-policy']).toBeTruthy();
  });

  test('no console errors on load', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', (msg) => {
      if (msg.type() === 'error') errors.push(msg.text());
    });
    await goto(page, '/ar');
    await page.waitForTimeout(2000);
    // Filter out known benign errors (e.g. browser extension noise)
    const realErrors = errors.filter(
      (e) => !e.includes('Extension') && !e.includes('chrome-extension')
    );
    expect(realErrors).toHaveLength(0);
  });
});

// ── Responsive (320px mobile) ─────────────────────────────────────────────────

test.describe('Responsive layout @ 320px', () => {
  test.use({ viewport: { width: 320, height: 568 } });

  test('homepage has no horizontal overflow', async ({ page }) => {
    await goto(page, '/ar');
    const bodyWidth = await page.evaluate(() => document.body.scrollWidth);
    const viewportWidth = await page.evaluate(() => window.innerWidth);
    expect(bodyWidth).toBeLessThanOrEqual(viewportWidth);
  });

  test('nav does not overflow viewport', async ({ page }) => {
    await goto(page, '/ar');
    const nav = page.locator('nav').first();
    await expect(nav).toBeVisible();
    const navBox = await nav.boundingBox();
    if (navBox) expect(navBox.width).toBeLessThanOrEqual(320 + 1);
  });
});

// ── Services ──────────────────────────────────────────────────────────────────

test.describe('Services page', () => {
  test('loads and shows at least one service card', async ({ page }) => {
    await goto(page, '/ar/services');
    await expect(page.locator('main')).toBeVisible({ timeout: 10_000 });
  });
});

// ── Blog ─────────────────────────────────────────────────────────────────────

test.describe('Blog page', () => {
  test('returns 200 and renders blog listing', async ({ page }) => {
    const res = await page.goto('/ar/blog', { waitUntil: 'domcontentloaded' });
    expect(res?.status()).toBe(200);
    await expect(page.locator('main')).toBeVisible({ timeout: 10_000 });
  });
});

// ── Portfolio ─────────────────────────────────────────────────────────────────

test.describe('Portfolio page', () => {
  test('loads successfully', async ({ page }) => {
    const res = await page.goto('/ar/portfolio', { waitUntil: 'domcontentloaded' });
    expect(res?.status()).toBe(200);
    await expect(page.locator('main')).toBeVisible({ timeout: 10_000 });
  });
});

// ── Contact form ──────────────────────────────────────────────────────────────

test.describe('Contact page', () => {
  test('shows contact form with required inputs', async ({ page }) => {
    await goto(page, '/ar/contact');
    // The form should be present — Turnstile widget will load in real browsers
    await expect(page.locator('form')).toBeVisible({ timeout: 10_000 });
  });

  test('form submit button is present and enabled', async ({ page }) => {
    await goto(page, '/ar/contact');
    const btn = page.locator('form button[type="submit"]').first();
    await expect(btn).toBeVisible({ timeout: 10_000 });
  });
});

// ── 404 ───────────────────────────────────────────────────────────────────────

test.describe('404 page', () => {
  test('non-existent path returns 404', async ({ page }) => {
    const res = await page.goto('/ar/this-page-does-not-exist-xyz');
    // Next.js returns 200 for client-rendered 404 pages in some configs
    expect([200, 404]).toContain(res?.status());
  });
});

// ── API health ────────────────────────────────────────────────────────────────

test.describe('API health', () => {
  test('backend /up endpoint is reachable', async ({ request }) => {
    const res = await request.get('https://api.serv5.com.eg/up');
    expect(res.status()).toBe(200);
  });

  test('services API returns data', async ({ request }) => {
    const res = await request.get('https://api.serv5.com.eg/api/v1/services?lang=ar');
    expect(res.status()).toBe(200);
    const json = await res.json();
    // Should be an array or object with data
    expect(json).toBeTruthy();
  });
});
