import React from "react";
import { headers } from "next/headers";
import { getCachedPage, getCachedBlogPosts, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import BlogClient from "./BlogClient";

export async function generateMetadata() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  let pageData = null;
  let settings = null;
  let alternates = null;

  try {
    const [pageRes, settingsRes, alts] = await Promise.all([
      getCachedPage("blog", lang).catch(() => null),
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
    (lang === "ar" ? "المدونة | Serv5" : "Blog | Serv5");

  const description =
    pageData?.seo?.description ||
    pageData?.description ||
    settings?.site_description ||
    (lang === "ar"
      ? "مقالات ورؤى تقنية حول تطوير المواقع وتطبيقات الجوال وأحدث استراتيجيات التسويق الرقمي."
      : "Articles and tech insights on web development, mobile applications, and digital marketing strategies.");

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
      url: `${siteUrl}/${lang}/blog`,
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

export default async function BlogPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialPageData = null;
  let initialPosts = [];

  try {
    const [pageRes, postsRes] = await Promise.all([
      getCachedPage("blog", lang).catch(() => null),
      getCachedBlogPosts(lang, 4).catch(() => null),
    ]);
    initialPageData = pageRes?.data || pageRes || null;
    initialPosts = postsRes?.data || postsRes || [];
  } catch (error) {
    console.error("Failed to fetch initial blog data on server:", error);
  }

  return (
    <BlogClient
      initialPageData={initialPageData}
      initialPosts={initialPosts}
      initialLang={lang}
    />
  );
}
