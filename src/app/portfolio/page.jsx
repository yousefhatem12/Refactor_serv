import React from "react";
import { headers } from "next/headers";
import { getCachedPage, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import PortfolioClient from "./PortfolioClient";

export async function generateMetadata() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  let pageData = null;
  let settings = null;
  let alternates = null;

  try {
    const [pageRes, settingsRes, alts] = await Promise.all([
      getCachedPage("portfolio", lang).catch(() => null),
      getCachedSettings(lang).catch(() => null),
      getSeoAlternates().catch(() => null),
    ]);
    pageData = pageRes?.data || pageRes;
    settings = settingsRes;
    alternates = alts;
  } catch (err) {
    // API fallback
  }

  const title =
    pageData?.seo?.title ||
    pageData?.title ||
    (lang === "ar" ? "أعمالنا | مشاريع شركة Serv5" : "Portfolio | Serv5 Projects");

  const description =
    pageData?.seo?.description ||
    pageData?.description ||
    settings?.site_description ||
    (lang === "ar"
      ? "استعرض أحدث أعمال ومشاريع شركة سيرف فايف في تصميم وتطوير المواقع والتطبيقات والهويات الرقمية."
      : "Explore our latest projects and case studies in web design, software development, and digital branding.");

  const ogImage = pageData?.seo?.image
    ? getMediaUrl(pageData.seo.image)
    : settings?.social_share_image
    ? getMediaUrl(settings.social_share_image)
    : `${siteUrl}/logo.svg`;

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${lang}/portfolio`,
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

export default async function PortfolioPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialPageData = null;
  try {
    const res = await getCachedPage("portfolio", lang).catch(() => null);
    initialPageData = res?.data || res || null;
  } catch (error) {
    console.error("Failed to fetch initial portfolio data on server:", error);
  }

  return (
    <PortfolioClient
      initialPageData={initialPageData}
      initialLang={lang}
    />
  );
}
