import React from "react";
import { headers } from "next/headers";
import { getCachedPage, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import FAQClient from "./FAQClient";

export async function generateMetadata() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  let pageData = null;
  let settings = null;
  let alternates = null;

  try {
    const [pageRes, settingsRes, alts] = await Promise.all([
      getCachedPage("faq", lang).catch(() => null),
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
    (lang === "ar" ? "الأسئلة الشائعة | Serv5" : "Frequently Asked Questions | Serv5");

  const description =
    pageData?.seo?.description ||
    pageData?.description ||
    settings?.site_description ||
    (lang === "ar"
      ? "إجابات شاملة على أكثر الأسئلة شيوعاً حول خدمات شركة سيرف فايف وطرق التعاقد والدعم الفني."
      : "Find answers to frequently asked questions about Serv5 services, contracts, timelines, and technical support.");

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
      url: `${siteUrl}/${lang}/faq`,
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

export default async function FAQPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialPageData = null;
  try {
    const res = await getCachedPage("faq", lang).catch(() => null);
    initialPageData = res?.data || res || null;
  } catch (error) {
    console.error("Failed to fetch initial FAQ data on server:", error);
  }

  return (
    <FAQClient
      initialPageData={initialPageData}
      initialLang={lang}
    />
  );
}
