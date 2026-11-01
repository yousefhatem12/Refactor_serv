"use client";
import React, { useState, useEffect, useRef } from "react";
import FAQContent from "../components/FAQ/FAQContent";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function FAQClient({ initialPageData, initialLang = "en" }) {
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
    fetchAPI(`/pages/faq?lang=${language}`)
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
      <div className="w-full relative z-10 pb-10 pt-32">
        <FAQContent data={pageData} />
      </div>
    </main>
  );
}

