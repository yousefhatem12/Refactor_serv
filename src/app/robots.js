export default function robots() {
  const baseUrl =
    process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  return {
    rules: [
      // ── Default: allow all bots ──────────────────────────────────────────────
      {
        userAgent: "*",
        allow: ["/"],
        disallow: [
          // Next.js internals
          "/_next/",
          "/_next/static/",
          "/_next/image/",

          // API routes (no SEO value)
          "/api/",

          // Admin & dashboard paths
          "/admin/",
          "/dashboard/",

          // Authentication pages
          "/login",
          "/register",
          "/logout",
          "/auth/",

          // Search results (avoid duplicate content)
          "/search",
          "/search?*",

          // Private/utility pages
          "/bank-accounts",   // internal banking info — keep private from some bots
        ],
      },

      // ── GPTBot (OpenAI) — block AI training scraping ────────────────────────
      {
        userAgent: "GPTBot",
        disallow: ["/"],
      },

      // ── ChatGPT-User — block AI training scraping ────────────────────────────
      {
        userAgent: "ChatGPT-User",
        disallow: ["/"],
      },

      // ── CCBot (Common Crawl / AI training datasets) ──────────────────────────
      {
        userAgent: "CCBot",
        disallow: ["/"],
      },

      // ── Google-Extended (Bard / Gemini AI training) ──────────────────────────
      {
        userAgent: "Google-Extended",
        disallow: ["/"],
      },

      // ── anthropic-ai (Claude AI training) ───────────────────────────────────
      {
        userAgent: "anthropic-ai",
        disallow: ["/"],
      },

      // ── Googlebot: allow everything (including images) ───────────────────────
      {
        userAgent: "Googlebot",
        allow: ["/"],
        disallow: [
          "/_next/",
          "/api/",
          "/admin/",
          "/dashboard/",
        ],
      },

      // ── Bingbot ──────────────────────────────────────────────────────────────
      {
        userAgent: "Bingbot",
        allow: ["/"],
        disallow: [
          "/_next/",
          "/api/",
          "/admin/",
          "/dashboard/",
        ],
      },
    ],

    // Tell crawlers where to find the XML sitemap
    sitemap: `${baseUrl}/sitemap.xml`,

    // Optional: host directive (Yandex uses this)
    host: baseUrl,
  };
}

