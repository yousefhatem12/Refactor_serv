"use client";
import React, { useState, useEffect } from "react";
import FAQContent from "../components/FAQ/FAQContent";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function FAQPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);

  useEffect(() => {
    fetchAPI(`/pages/faq?lang=${language}`).then(data => {
      if (data) setPageData(data);
    }).catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-32">
        <FAQContent data={pageData} />
      </div>
    </main>
  );
}
