"use client";
import React, { useState, useEffect } from "react";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

import WebDesignHero from "../components/Services-web-design/WebDesignHero";
import WebDesignFeatures from "../components/Services-web-design/WebDesignFeatures";
import WebDesignPortfolio from "../components/Services-web-design/WebDesignPortfolio";
import WebDesignRequest from "../components/Services-web-design/WebDesignRequest";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";



export default function WebDesignPage() {
  const [pageData, setPageData] = useState(null);
  const { language } = useLanguage();

  useEffect(() => {
    fetchAPI(`/services/ui-ux-design?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        {pageData?.show_hero !== false && <WebDesignHero data={pageData} />}
        
        {pageData?.show_features !== false && (
          <WebDesignFeatures 
            title={pageData?.features_title} 
            description={pageData?.features_subtitle} 
            featuresList={pageData?.features_list}
          />
        )}
        
        {pageData?.show_portfolio !== false && <WebDesignPortfolio />}
        {pageData?.show_request !== false && <WebDesignRequest />}
        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
    </main>
  );
}
