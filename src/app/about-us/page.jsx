import React from "react";
import { headers } from "next/headers";
import { getCachedPage, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import AboutUsClient from "./AboutUsClient";

export async function generateMetadata() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  let pageData = null;
  let settings = null;
  let alternates = null;

  try {
    const [pageRes, settingsRes, alts] = await Promise.all([
      getCachedPage("about-us", lang).catch(() => null),
      getCachedSettings(lang).catch(() => null),
      getSeoAlternates().catch(() => null),
    ]);
    pageData = pageRes?.data || pageRes;
    settings = settingsRes;
    alternates = alts;
  } catch (err) {
    // API unavailable during build
  }

  const title =
    pageData?.seo?.title ||
    pageData?.title ||
    (lang === "ar" ? "من نحن | Serv5" : "About Us | Serv5");

  const description =
    pageData?.seo?.description ||
    pageData?.description ||
    settings?.site_description ||
    (lang === "ar"
      ? "تعرف على شركة سيرف فايف وخبرتنا الممتدة لأكثر من 15 عاماً في تصميم وبرمجة المواقع والتطبيقات."
      : "Learn more about Serv5 and our 15+ years of experience in web design, custom development, and digital solutions.");

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
      url: `${siteUrl}/${lang}/about-us`,
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

export default async function AboutUsPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialPageData = null;
  try {
    const res = await getCachedPage("about-us", lang).catch(() => null);
    initialPageData = res?.data || res || null;
  } catch (error) {
    console.error("Failed to fetch initial about-us data on server:", error);
  }

  return (
    <AboutUsClient initialPageData={initialPageData} initialLang={lang} />
  );
}
