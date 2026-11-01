import React from "react";
import { headers } from "next/headers";
import { getCachedServicesList, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import ServicesClient from "./ServicesClient";

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
      ? "خدماتنا | Serv5"
      : "Our Services | Serv5";

  const description =
    lang === "ar"
      ? "استكشف خدمات سيرف فايف: تصميم وتطوير المواقع، تطبيقات الجوال، المتاجر الإلكترونية، التسويق الرقمي، والاستضافة السحابية."
      : "Explore Serv5 services: web design and development, mobile applications, e-commerce, digital marketing, and cloud hosting solutions.";

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
      url: `${siteUrl}/${lang}/services`,
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

export default async function ServicesPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialServices = [];
  try {
    const res = await getCachedServicesList(lang).catch(() => null);
    initialServices = Array.isArray(res) ? res : res?.data || [];
  } catch (error) {
    console.error("Failed to fetch initial services on server:", error);
  }

  return (
    <ServicesClient
      initialServices={initialServices}
      initialLang={lang}
    />
  );
}

