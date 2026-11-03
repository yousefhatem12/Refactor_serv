"use client";
import React, { useState, useEffect, useRef } from "react";
import PortfolioGallery from "../components/Portfolio/PortfolioGallery";
import CTA from "../components/Home/CTA/CTA";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function PortfolioClient({ initialPageData, initialLang = "en" }) {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(initialPageData);
  const initialLangRef = useRef(initialLang);
  const isInitialMountRef = useRef(true);

  useEffect(() => {
    if (isInitialMountRef.current) {
      isInitialMountRef.current = false;
      if (initialPageData && language === initialLangRef.current) {
        return;
      }
    }

    let isMounted = true;
    fetchAPI(`/pages/portfolio?lang=${language}`)
      .then((res) => {
        if (isMounted && res) {
          setPageData(res.data || res);
        }
      })
      .catch(console.error);

    return () => {
      isMounted = false;
    };
  }, [language, initialPageData]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      {/* Background Color Layer */}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      <div className="w-full relative z-10">
        <PortfolioGallery data={pageData} perPage={9} />
        <CTA />
      </div>
    </main>
  );
}

