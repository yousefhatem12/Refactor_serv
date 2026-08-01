import { fetchAPI } from "@/lib/api";
import { getSeoAlternates } from "@/lib/seo";
import { headers, cookies } from "next/headers";

export async function generateMetadata({ params }) {
  const { id } = await params;
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const cookieStore = await cookies();
  const language = xLocale || cookieStore?.get("NEXT_LOCALE")?.value || "en";
  
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const alternates = await getSeoAlternates();
  
  let title = "Portfolio | Serv5";
  let description = "Explore our successful projects and case studies.";
  let ogImage = "";

  try {
    const res = await fetchAPI(`/portfolio/${id}?lang=${language}`);
    const project = res.data ? res.data : res;
    if (project && project.name) {
      title = project.name;
      description = project.description || "";
      ogImage = project.image || "";
    }
  } catch (error) {
    console.error("Failed to fetch portfolio post metadata:", error);
  }

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${language}/portfolio/${id}`,
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

export default function PortfolioDetailsLayout({ children }) {
  return children;
}
