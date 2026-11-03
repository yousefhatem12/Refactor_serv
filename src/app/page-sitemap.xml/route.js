import { connection } from "next/server";
import { getSiteUrl } from "../../lib/site-url";

export async function GET() {
  await connection();
  const baseUrl = getSiteUrl();

  // Custom configuration for static paths
  const staticPaths = [
    { path: "", priority: 1.0, changefreq: "daily" },
    { path: "/about-us", priority: 0.8, changefreq: "weekly" },
    { path: "/bank-accounts", priority: 0.5, changefreq: "monthly" },
    { path: "/project-consultation", priority: 0.8, changefreq: "weekly" },
    { path: "/request-quote", priority: 0.8, changefreq: "weekly" },
    { path: "/portfolio", priority: 0.9, changefreq: "daily" },
    { path: "/blog", priority: 0.9, changefreq: "daily" },
    { path: "/our-products", priority: 0.9, changefreq: "daily" },
    { path: "/careers", priority: 0.8, changefreq: "weekly" },
    { path: "/faq", priority: 0.8, changefreq: "weekly" },
    { path: "/contact-us", priority: 0.8, changefreq: "weekly" },
  ];

  const routes = [];

  for (const item of staticPaths) {
    // English version (200 OK)
    routes.push({
      url: `${baseUrl}/en${item.path}`,
      lastModified: new Date(),
      changeFrequency: item.changefreq,
      priority: item.priority,
      alternates: {
        languages: {
          en: `${baseUrl}/en${item.path}`,
          ar: `${baseUrl}/ar${item.path}`,
          "x-default": `${baseUrl}/en${item.path}`,
        },
      },
    });
    // Arabic version (200 OK)
    routes.push({
      url: `${baseUrl}/ar${item.path}`,
      lastModified: new Date(),
      changeFrequency: item.changefreq,
      priority: item.priority,
      alternates: {
        languages: {
          en: `${baseUrl}/en${item.path}`,
          ar: `${baseUrl}/ar${item.path}`,
          "x-default": `${baseUrl}/en${item.path}`,
        },
      },
    });
  }

  // Generate XML
  let xml = `<?xml version="1.0" encoding="UTF-8"?>\n`;
  xml += `<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>\n`;
  xml += `<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">\n`;

  for (const route of routes) {
    xml += `  <url>\n`;
    xml += `    <loc>${route.url}</loc>\n`;
    xml += `    <lastmod>${route.lastModified.toISOString()}</lastmod>\n`;
    xml += `    <changefreq>${route.changeFrequency}</changefreq>\n`;
    xml += `    <priority>${route.priority.toFixed(1)}</priority>\n`;
    if (route.alternates && route.alternates.languages) {
      for (const [lang, url] of Object.entries(route.alternates.languages)) {
        xml += `    <xhtml:link rel="alternate" hreflang="${lang}" href="${url}"/>\n`;
      }
    }
    xml += `  </url>\n`;
  }
  xml += `</urlset>`;

  return new Response(xml, {
    headers: {
      "Content-Type": "application/xml; charset=utf-8",
      "Cache-Control": "public, max-age=0, s-maxage=300, must-revalidate",
    },
  });
}
