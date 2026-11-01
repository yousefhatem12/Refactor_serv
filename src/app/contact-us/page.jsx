import React from "react";
import { headers } from "next/headers";
import { getCachedPage, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import ContactUsClient from "./ContactUsClient";

export async function generateMetadata() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  let pageData = null;
  let settings = null;
  let alternates = null;

  try {
    const [pageRes, settingsRes, alts] = await Promise.all([
      getCachedPage("contact-us", lang).catch(() => null),
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
    (lang === "ar" ? "اتصل بنا | Serv5" : "Contact Us | Serv5");

  const description =
    pageData?.seo?.description ||
    pageData?.description ||
    settings?.site_description ||
    (lang === "ar"
      ? "تواصل مع فريق سيرف فايف لطلب استشارة أو عرض سعر لمشروعك البرمجي والتسويقي."
      : "Contact Serv5 team for consultations, quotations, and inquiries about our software and digital marketing services.");

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
      url: `${siteUrl}/${lang}/contact-us`,
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

export default async function ContactUsPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialPageData = null;
  try {
    const res = await getCachedPage("contact-us", lang).catch(() => null);
    initialPageData = res?.data || res || null;
  } catch (error) {
    console.error("Failed to fetch initial contact data on server:", error);
  }

  return (
    <ContactUsClient
      initialPageData={initialPageData}
      initialLang={lang}
    />
  );
}
