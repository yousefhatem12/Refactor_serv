import { getCachedProductItem, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import { headers, cookies } from "next/headers";

export async function generateMetadata({ params }) {
  const { id } = await params;
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const cookieStore = await cookies();
  const language = xLocale || cookieStore?.get("NEXT_LOCALE")?.value || "en";

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const alternates = await getSeoAlternates();

  let title = "Product Details | Serv5";
  let description = "Explore our digital products and solutions.";
  let ogImage = "";

  try {
    const [productRes, settingsRes] = await Promise.all([
      getCachedProductItem(id, language).catch(() => null),
      getCachedSettings(language).catch(() => null),
    ]);

    const product = productRes?.data ? productRes.data : productRes;
    if (product && (product.title || product.name)) {
      title = product.title || product.name;
      description = product.description || settingsRes?.site_description || "";
      ogImage = product.image ? getMediaUrl(product.image) : "";
    }
  } catch (error) {
    console.error("Failed to fetch product metadata:", error);
  }

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${language}/our-products/${id}`,
      siteName: "Serv5",
      type: "website",
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

export default function ProductLayout({ children }) {
  return children;
}

