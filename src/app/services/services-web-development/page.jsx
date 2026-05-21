"use client";
import React, { useState, useEffect } from "react";
import WebDevHero from "../../components/Services-web-dev/WebDevHero";
import WebDesignFeatures from "../../components/Services-web-design/WebDesignFeatures";
import WebDevHowWeWork from "../../components/Services-web-dev/WebDevHowWeWork";
import LatestBlogs from "../../components/Services-web-hosting/LatestBlogs";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";

export default function WebDevPage() {
  const { language, t } = useLanguage();
  const [pageData, setPageData] = useState(null);

  useEffect(() => {
    fetchAPI(`/services/web-development?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        {pageData?.show_hero !== false && <WebDevHero data={pageData} />}
        
        {pageData?.show_features !== false && (
          <WebDesignFeatures 
            title={pageData?.features_title || t("webDevFeaturesTitle")} 
            description={pageData?.features_subtitle} 
            featuresList={pageData?.features_list}
          />
        )}

        {pageData?.show_how_we_work !== false && (
          <WebDevHowWeWork howWeWorkList={pageData?.how_we_work_list} />
        )}

        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
    </main>
  );
}
