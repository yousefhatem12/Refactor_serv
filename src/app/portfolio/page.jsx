"use client";
import React, { useState, useEffect } from "react";
import PortfolioGallery from "../components/Portfolio/PortfolioGallery";
import CTA from "../components/Home/CTA/CTA";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import Loading from "../loading";
import { notFound } from "next/navigation";

export default function PortfolioPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);
  const [error, setError] = useState(false);

  useEffect(() => {
    fetchAPI(`/pages/portfolio?lang=${language}`)
      .then((data) => {
        if (data) {
          setPageData(data);
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
      {/* Background Color Layer */}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      <div className="w-full relative z-10">
        <PortfolioGallery data={pageData} />
        <CTA />
      </div>
    </main>
  );
}
