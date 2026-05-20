"use client";
import React, { useState, useEffect } from "react";
import EcommerceHero from "../../components/Services-ecommerce/EcommerceHero";
import EcommerceFeatures from "../../components/Services-ecommerce/EcommerceFeatures";
import LatestBlogs from "../../components/Services-web-hosting/LatestBlogs";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";

export default function EcommercePage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);

  useEffect(() => {
    // We try to fetch dynamic page data if it exists in Strapi/backend.
    // We pass language for localization.
    fetchAPI(`/pages/services-ecommerce?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        <EcommerceHero data={pageData} />
        <EcommerceFeatures />
        <LatestBlogs />
      </div>
    </main>
  );
}
