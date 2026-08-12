import React, { Suspense } from "react";
import HomePageClient from "./HomePageClient";
import { getCachedHomeData } from "@/lib/cached-api";
import { headers } from "next/headers";

export const unstable_instant = {
  prefetch: "runtime",
  samples: [
    {
      headers: [
        ["x-locale", "en"],
        ["x-pathname", "/"],
      ],
      cookies: [
        { name: "NEXT_LOCALE", value: "en" }
      ]
    },
    {
      headers: [
        ["x-locale", "ar"],
        ["x-pathname", "/ar"],
      ],
      cookies: [
        { name: "NEXT_LOCALE", value: "ar" }
      ]
    }
  ]
};

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

  return <HomePageClient initialHomeData={initialHomeData} />;
}

export default function HomePage() {
  return (
    <Suspense fallback={<div className="min-h-screen bg-[#0a0a0f]"></div>}>
      <HomeContent />
    </Suspense>
  );
}
