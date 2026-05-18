"use client";
import React, { useState, useEffect } from "react";
import MobileAppsHero from "../components/Services-mobile-apps/MobileAppsHero";
import MobileAppsFeatures from "../components/Services-mobile-apps/MobileAppsFeatures";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function MobileAppsPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);

  useEffect(() => {
    fetchAPI(`/services/app-development?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        {pageData?.show_hero !== false && <MobileAppsHero data={pageData} />}
        {pageData?.show_features !== false && <MobileAppsFeatures featuresList={pageData?.features_list} />}
        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
    </main>
  );
}
