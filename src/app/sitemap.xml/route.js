export async function GET() {
  const baseUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || "https://api.serv5.com.eg/api/v1";

  // Static pages list
  const staticPaths = [
    "",
    "/about-us",
    "/bank-accounts",
    "/project-consultation",
    "/request-quote",
    "/portfolio",
    "/blog",
    "/our-products",
    "/careers",
    "/faq",
    "/contact-us",
  ];

  const routes = [];

  // 1. Add static paths with alternates
  for (const path of staticPaths) {
    routes.push({
      url: `${baseUrl}/en${path}`,
      lastModified: new Date(),
      changeFrequency: "weekly",
      priority: path === "" ? 1.0 : 0.8,
      alternates: {
        languages: {
          en: `${baseUrl}/en${path}`,
          ar: `${baseUrl}/ar${path}`,
          "x-default": `${baseUrl}/en${path}`,
        },
      },
    });
  }

  // 2. Fetch dynamic services
  try {
    const res = await fetch(`${apiUrl}/services?lang=en`, { cache: "no-store" });
    if (res.ok) {
      const services = await res.json();
      const servicesList = Array.isArray(services) ? services : (services?.data || []);
      if (Array.isArray(servicesList)) {
        for (const service of servicesList) {
          const serviceId = service.id; // slug (e.g. web-development)
          if (serviceId) {
            const path = `/services/services-${serviceId}`;
            routes.push({
              url: `${baseUrl}/en${path}`,
              lastModified: new Date(),
              changeFrequency: "weekly",
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
    console.error("Error generating sitemap for services:", error);
  }

  // 3. Fetch dynamic blog posts
  try {
    const res = await fetch(`${apiUrl}/blog?lang=en`, { cache: "no-store" });
    if (res.ok) {
      const blogs = await res.json();
      const blogList = Array.isArray(blogs) ? blogs : (blogs?.data || []);
      if (Array.isArray(blogList)) {
        for (const post of blogList) {
          const postSlug = post.slug;
          if (postSlug) {
            const path = `/blog/${postSlug}`;
            routes.push({
              url: `${baseUrl}/en${path}`,
              lastModified: new Date(),
              changeFrequency: "weekly",
              priority: 0.6,
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
    console.error("Error generating sitemap for blogs:", error);
  }

  // 4. Fetch dynamic products
  try {
    const res = await fetch(`${apiUrl}/products?lang=en`, { cache: "no-store" });
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
              priority: 0.6,
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
    console.error("Error generating sitemap for products:", error);
  }

  // 5. Fetch dynamic portfolio (projects)
  try {
    const res = await fetch(`${apiUrl}/portfolio?lang=en`, { cache: "no-store" });
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
              changeFrequency: "weekly",
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
    console.error("Error generating sitemap for portfolio:", error);
  }

  // Generate XML string with stylesheet reference
  let xml = `<?xml version="1.0" encoding="UTF-8"?>\n`;
  xml += `<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>\n`;
  xml += `<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">\n`;

  for (const route of routes) {
    xml += `  <url>\n`;
    xml += `    <loc>${route.url}</loc>\n`;
    
    if (route.lastModified) {
      const dateStr = route.lastModified instanceof Date 
        ? route.lastModified.toISOString() 
        : new Date(route.lastModified).toISOString();
      xml += `    <lastmod>${dateStr}</lastmod>\n`;
    }
    
    if (route.changeFrequency) {
      xml += `    <changefreq>${route.changeFrequency}</changefreq>\n`;
    }
    
    if (route.priority !== undefined) {
      xml += `    <priority>${route.priority.toFixed(1)}</priority>\n`;
    }
    
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
