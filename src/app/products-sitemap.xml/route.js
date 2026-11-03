import { connection } from "next/server";
import { getSiteUrl } from "../../lib/site-url";

export async function GET() {
  await connection();
  const baseUrl = getSiteUrl();
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || "https://api.serv5.com.eg/api/v1";

  const routes = [];

  // Fetch dynamic products with 10-second timeout
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 10000);

  try {
    const res = await fetch(`${apiUrl}/products?lang=en`, { 
      cache: "no-store",
      signal: controller.signal 
    });
    clearTimeout(timeoutId);

    if (res.ok) {
      const products = await res.json();
      const productList = Array.isArray(products) ? products : (products?.data || []);
      if (Array.isArray(productList)) {
        for (const product of productList) {
          const productId = product.id;
          if (productId) {
            const path = `/our-products/${productId}`;
            routes.push({
              url: `${baseUrl}/en${path}`,
              lastModified: new Date(),
              changeFrequency: "weekly",
              priority: 0.8,
              alternates: {
                languages: {
                  en: `${baseUrl}/en${path}`,
                  ar: `${baseUrl}/ar${path}`,
                  "x-default": `${baseUrl}/en${path}`,
                },
              },
            });
            routes.push({
              url: `${baseUrl}/ar${path}`,
              lastModified: new Date(),
              changeFrequency: "weekly",
              priority: 0.8,
              alternates: {
                languages: {
                  en: `${baseUrl}/en${path}`,
                  ar: `${baseUrl}/ar${path}`,
                  "x-default": `${baseUrl}/en${path}`,
                },
              },
            });
          }
        }
      }
    }
  } catch (error) {
    clearTimeout(timeoutId);
    console.error("Error generating sitemap for products:", error.name === "AbortError" ? "Timeout" : error);
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
