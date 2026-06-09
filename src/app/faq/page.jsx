"use client";
import React, { useState, useEffect } from "react";
import FAQContent from "../components/FAQ/FAQContent";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import Loading from "../loading";
import { notFound } from "next/navigation";

export default function FAQPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);
  const [error, setError] = useState(false);

  useEffect(() => {
    fetchAPI(`/pages/faq?lang=${language}`)
      .then((res) => {
        if (res) {
          setPageData(res.data || res);
        } else {
          setError(true);
        }
      })
      .catch((err) => {
        console.error(err);
        setError(true);
      });
  }, [language]);

  if (error) {
    notFound();
  }

  if (!pageData) {
    return <Loading />;
  }

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-32">
        <FAQContent data={pageData} />
      </div>
    </main>
  );
}
