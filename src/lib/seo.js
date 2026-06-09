import { headers } from "next/headers";

/**
 * Returns alternates configuration for metadata (canonical and languages)
 * based on x-pathname header injected in middleware.
 */
export async function getSeoAlternates() {
  try {
    const headersList = await headers();
    const pathname = headersList.get("x-pathname") || "";
    const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
    
    // Normalize pathParts to determine locale and core path
    const pathParts = pathname.split("/");
    const currentLocale = pathParts[1] === "ar" || pathParts[1] === "en" ? pathParts[1] : "en";
    const remainder = "/" + pathParts.slice(2).join("/");
    const canonicalRemainder = remainder === "/" ? "" : remainder;
    
    const canonical = `${siteUrl}/${currentLocale}${canonicalRemainder}`;
    
    return {
      canonical,
      languages: {
        en: `${siteUrl}/en${canonicalRemainder}`,
        ar: `${siteUrl}/ar${canonicalRemainder}`,
        "x-default": `${siteUrl}/en${canonicalRemainder}`,
      },
    };
  } catch (error) {
    console.error("Failed to generate SEO alternates:", error);
    return {};
  }
}
