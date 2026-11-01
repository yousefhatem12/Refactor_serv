import React from "react";
import { headers } from "next/headers";
import { getCachedProductsList, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl, getApiPage } from "@/lib/api";
import OurProductsClient from "./OurProductsClient";

export async function generateMetadata() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  let settings = null;
  let alternates = null;

  try {
    const [settingsRes, alts] = await Promise.all([
      getCachedSettings(lang).catch(() => null),
      getSeoAlternates().catch(() => null),
    ]);
    settings = settingsRes;
    alternates = alts;
  } catch (err) {
    // API fallback
  }

  const title =
    lang === "ar"
      ? "منتجاتنا الرقمية والبرمجية | Serv5"
      : "Our Products | Digital & Software Solutions | Serv5";

  const description =
    lang === "ar"
      ? "استعرض أحدث المنتجات الرقمية والأنظمة الجاهزة المطورة من شركة سيرف فايف لخدمة مختلف القطاعات التجارية."
      : "Discover ready-to-deploy software products and digital systems built by Serv5 to power modern businesses.";

  const ogImage = settings?.social_share_image
    ? getMediaUrl(settings.social_share_image)
    : `${siteUrl}/logo.svg`;

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${lang}/our-products`,
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

export default async function OurProductsPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialProducts = [];
  let initialHasMore = false;
  let initialPage = 1;

  try {
    const res = await getCachedProductsList(lang, 1, 9).catch(() => null);
    if (res) {
      const page = getApiPage(res);
      initialProducts = page.items || [];
      initialHasMore = page.hasMore || false;
      initialPage = page.currentPage || 1;
    }
  } catch (error) {
    console.error("Failed to fetch initial products on server:", error);
  }

  return (
    <OurProductsClient
      initialProducts={initialProducts}
      initialHasMore={initialHasMore}
      initialPage={initialPage}
      initialLang={lang}
    />
  );
}
