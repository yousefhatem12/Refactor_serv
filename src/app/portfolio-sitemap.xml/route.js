export async function GET(request) {
  const { origin: baseUrl } = new URL(request.url);
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || "https://api.serv5.com.eg/api/v1";

  const routes = [];

  // Fetch dynamic portfolio (projects) with 3-second timeout
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 3000);

  try {
    const res = await fetch(`${apiUrl}/portfolio?lang=en`, { 
      cache: "no-store",
      signal: controller.signal 
    });
    clearTimeout(timeoutId);

    if (res.ok) {
      const portfolio = await res.json();
      const portfolioList = Array.isArray(portfolio) ? portfolio : (portfolio?.data || []);
      if (Array.isArray(portfolioList)) {
        for (const project of portfolioList) {
          const projectId = project.id;
          if (projectId) {
            const path = `/portfolio/${projectId}`;
            routes.push({
              url: `${baseUrl}/en${path}`,
              lastModified: new Date(),
              changeFrequency: "monthly",
              priority: 0.7,
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
    console.error("Error generating sitemap for portfolio:", error.name === "AbortError" ? "Timeout" : error);
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
      "Cache-Control": "public, max-age=3600, s-maxage=18000, stale-while-revalidate=86400",
    },
  });
}
