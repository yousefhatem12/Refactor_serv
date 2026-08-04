import { connection } from "next/server";
import { getSiteUrl } from "../../lib/site-url";

export async function GET() {
  await connection();
  const baseUrl = getSiteUrl();
  const lastModDate = new Date().toISOString();

  // Define sub-sitemaps in the index dynamically
  const sitemaps = [
    { loc: `${baseUrl}/page-sitemap.xml`, lastmod: lastModDate },
    { loc: `${baseUrl}/services-sitemap.xml`, lastmod: lastModDate },
    { loc: `${baseUrl}/products-sitemap.xml`, lastmod: lastModDate },
    { loc: `${baseUrl}/portfolio-sitemap.xml`, lastmod: lastModDate },
    { loc: `${baseUrl}/blog-sitemap.xml`, lastmod: lastModDate },
  ];

  let xml = `<?xml version="1.0" encoding="UTF-8"?>\n`;
  xml += `<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>\n`;
  xml += `<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n`;

  for (const sitemap of sitemaps) {
    xml += `  <sitemap>\n`;
    xml += `    <loc>${sitemap.loc}</loc>\n`;
    xml += `    <lastmod>${sitemap.lastmod}</lastmod>\n`;
    xml += `  </sitemap>\n`;
  }

  xml += `</sitemapindex>`;

  return new Response(xml, {
    headers: {
      "Content-Type": "application/xml; charset=utf-8",
      "Cache-Control": "public, max-age=0, s-maxage=300, must-revalidate",
    },
  });
}
