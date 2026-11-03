import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright E2E tests for serv5.com.eg
 * Run against the live production site to verify critical user flows.
 *
 * Usage:
 *   npx playwright test              # all tests
 *   npx playwright test --headed     # with browser visible
 *   npx playwright test --project=chromium  # single browser
 */
export default defineConfig({
  testDir: './e2e',
  timeout: 30_000,
  retries: 1,
  reporter: 'html',

  use: {
    baseURL: process.env.BASE_URL || 'https://serv5.com.eg',
    locale: 'ar',
    extraHTTPHeaders: { 'Accept-Language': 'ar,en;q=0.9' },
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },

  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    { name: 'mobile', use: { ...devices['iPhone 13'] } },
  ],
});
