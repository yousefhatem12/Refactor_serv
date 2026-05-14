"use client";
import React, { useState, useEffect } from "react";
import AboutHero from "../components/AboutUs/AboutHero";
import PageSections from "../components/Shared/PageSections";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function AboutUsPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);

  useEffect(() => {
    fetchAPI(`/pages/about-us?lang=${language}`).then(data => {
      if (data) setPageData(data);
    }).catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10">
        <AboutHero data={pageData} />
        
        {/* Dynamic Sections from Dashboard, exclude stats as it's in the hero */}
        <PageSections sections={pageData?.sections?.filter(s => s.type !== 'stats')} />
      </div>
    </main>
  );
}
