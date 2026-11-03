import React from "react";
import { headers } from "next/headers";
import { getCachedCareersList, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl, getApiPage } from "@/lib/api";
import CareersClient from "./CareersClient";

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
      ? "وظائف سيرف فايف | انضم إلى فريقنا"
      : "Careers at Serv5 | Join Our Team";

  const description =
    lang === "ar"
      ? "استكشف الفرص الوظيفية المتاحة في شركة سيرف فايف وانضم إلى فريق من المبدعين في تطوير البرمجيات والتسويق الرقمي."
      : "Explore open career opportunities at Serv5 and join our passionate team of developers, designers, and marketing experts.";

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
      url: `${siteUrl}/${lang}/careers`,
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

export default async function CareersPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialJobs = [];
  let initialDepts = [];
  let initialHasMore = false;

  try {
    const res = await getCachedCareersList(lang).catch(() => null);
    if (res) {
      const page = getApiPage(res);
      initialJobs = page.items || [];
      initialHasMore = page.hasMore || false;
      if (res.departments) {
        initialDepts = res.departments;
      }
    }
  } catch (error) {
    console.error("Failed to fetch initial careers on server:", error);
  }

  return (
    <CareersClient
      initialJobs={initialJobs}
      initialDepts={initialDepts}
      initialHasMore={initialHasMore}
      initialLang={lang}
    />
  );
}
