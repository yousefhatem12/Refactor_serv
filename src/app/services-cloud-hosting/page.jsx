"use client";
import React, { useState, useEffect } from "react";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import ServicesHero from "../components/Services-web-hosting/ServicesHero";
import ServicesFeatures from "../components/Services-web-hosting/ServicesFeatures";
import HostingOffers from "../components/Services-web-hosting/HostingOffers";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export default function ServicesPage() {
  const [pageData, setPageData] = useState(null);
  const { language } = useLanguage();

  useEffect(() => {
    fetchAPI(`/services/cloud-hosting?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        {pageData?.show_hero !== false && (
          <ServicesHero 
            title={pageData?.hero_title || pageData?.title} 
            desc={pageData?.hero_subtitle || pageData?.description} 
          />
        )}
        {pageData?.show_features !== false && (
          <ServicesFeatures 
            featuresTitle={pageData?.features_title}
            featuresSubtitle={pageData?.features_subtitle}
            featuresList={pageData?.features_list}
          />
        )}
        {pageData?.show_hosting_offers !== false && (
          <HostingOffers 
            offersList={pageData?.hosting_offers_list}
          />
        )}
        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
    </main>
  );
}
