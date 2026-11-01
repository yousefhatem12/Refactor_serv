import React, { Suspense } from "react";
import HomePageClient from "./HomePageClient";
import { getCachedHomeData, getCachedSettings } from "@/lib/cached-api";
import { headers } from "next/headers";

// generateMetadata MUST be fully static (no headers(), cookies(), or getSeoAlternates()).
// Any dynamic API call here makes Next.js PPR inject __next_metadata_boundary__ into the
// static shell, which mismatches the prebuilt structure → React error #419 hydration fail.
// The root layout's generateMetadata provides the template; this just overrides the title.
export async function generateMetadata() {
  // Static locale — the root layout handles the template.
  // Per-locale metadata is handled by the server component (HomeContent) rendering
  // and client-side LanguageContext, NOT in generateMetadata.
  const locale = "en";

  let data = null;
  let settings = null;
  try {
    const [response, settingsData] = await Promise.all([
      getCachedHomeData(locale),
      getCachedSettings(locale),
    ]);
    data = response?.data || response;
    settings = settingsData;
  } catch (e) {
    // API unavailable during build — use static fallbacks
  }

  return {
    title: {
      absolute: settings?.site_title || data?.seo?.title || "Serv5",
    },
    description:
      data?.seo?.description ||
      "Leading company in web development and AI solutions.",
  };
}

async function HomeContent() {
  const headerList = await headers();
  const locale = headerList.get("x-locale") || "en";

  let initialHomeData = null;
  try {
    const data = await getCachedHomeData(locale);
    initialHomeData = data?.data || data || null;
  } catch (error) {
    console.error("Failed to fetch initial home data on server:", error);
  }

  return <HomePageClient initialHomeData={initialHomeData} initialLocale={locale} />;
}

export default function HomePage() {
  return (
    <Suspense fallback={<div className="min-h-screen bg-[#0a0a0f]"></div>}>
      <HomeContent />
    </Suspense>
  );
}
