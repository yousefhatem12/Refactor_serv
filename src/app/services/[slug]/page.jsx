import React from "react";
import { notFound } from "next/navigation";
import { headers } from "next/headers";
import { getCachedService, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import ServiceDetailClient from "./ServiceDetailClient";

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const apiSlug = slug?.startsWith("services-") ? slug.replace("services-", "") : slug;

  let serviceData = null;
  let settings = null;
  let alternates = null;

  try {
    const [serviceRes, settingsRes, alts] = await Promise.all([
      getCachedService(apiSlug, lang).catch(() => null),
      getCachedSettings(lang).catch(() => null),
      getSeoAlternates().catch(() => null),
    ]);
    serviceData = serviceRes?.data || serviceRes;
    settings = settingsRes;
    alternates = alts;
  } catch (err) {
    // API fallback
  }

  const title =
    serviceData?.hero_title ||
    serviceData?.title ||
    (lang === "ar" ? "خدماتنا | Serv5" : "Our Services | Serv5");

  const description =
    serviceData?.hero_subtitle ||
    serviceData?.description ||
    settings?.site_description ||
    "";

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const ogImage = serviceData?.image || serviceData?.default_hero_image
    ? getMediaUrl(serviceData.image || serviceData.default_hero_image)
    : `${siteUrl}/logo.svg`;

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${lang}/services/${slug}`,
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

export default async function ServicePage({ params }) {
  const { slug } = await params;
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const apiSlug = slug?.startsWith("services-") ? slug.replace("services-", "") : slug;

  let initialServiceData = null;
  try {
    const res = await getCachedService(apiSlug, lang).catch(() => null);
    if (res && res.message !== "Service not found") {
      initialServiceData = res?.data || res;
    }
  } catch (error) {
    console.error("Failed to fetch initial service on server:", error);
  }

  if (!initialServiceData) {
    notFound();
  }

  return (
    <ServiceDetailClient
      initialServiceData={initialServiceData}
      initialLang={lang}
      slug={slug}
    />
  );
}
