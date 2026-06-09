import { fetchAPI } from "@/lib/api";
import { getSeoAlternates } from "@/lib/seo";
import { headers, cookies } from "next/headers";

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const cookieStore = await cookies();
  const language = xLocale || cookieStore?.get("NEXT_LOCALE")?.value || "en";
  
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const alternates = await getSeoAlternates();
  
  let title = "Serv5";
  let description = "Advanced Agentic Coding Solutions";
  let ogImage = "";

  try {
    if (slug.startsWith("services-")) {
      const serviceSlug = slug.replace("services-", "");
      const data = await fetchAPI(`/services/${serviceSlug}?lang=${language}`);
      if (data && data.message !== "Service not found") {
        title = data.hero_title || data.title || "Serv5 Service";
        description = data.hero_subtitle || data.description || "";
        ogImage = data.image || data.default_hero_image || "";
      }
    } else {
      const data = await fetchAPI(`/pages/${slug}?lang=${language}`);
      if (data && data.message !== "Page not found") {
        title = data.seo?.title || data.title || "Serv5";
        description = data.seo?.description || data.description || "";
        ogImage = data.seo?.image || "";
      }
    }
  } catch (error) {
    console.error("Failed to fetch dynamic page metadata:", error);
  }

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${language}/${slug}`,
      siteName: "Serv5",
      images: ogImage ? [{ url: ogImage }] : [],
    },
    twitter: {
      card: "summary_large_image",
      title,
      description,
      images: ogImage ? [ogImage] : [],
    },
  };
}

export default function SlugLayout({ children }) {
  return children;
}
