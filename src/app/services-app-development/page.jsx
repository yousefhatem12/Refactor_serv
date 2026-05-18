"use client";
import React, { useState, useEffect } from "react";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import WebDevHero from "../components/Services-web-dev/WebDevHero";
import WebDesignFeatures from "../components/Services-web-design/WebDesignFeatures";
import WebDesignPortfolio from "../components/Services-web-design/WebDesignPortfolio";
import MobileAppHowWeWork from "../components/Services-web-dev/MobileAppHowWeWork";
import WebDesignRequest from "../components/Services-web-design/WebDesignRequest";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";
import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";

export default function AppDevelopmentPage() {
  const [pageData, setPageData] = useState(null);
  const { language, t } = useLanguage();

  useEffect(() => {
    fetchAPI(`/services/app-development?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden ">
      <div className="w-full relative z-10 pb-10 pt-10">
        {pageData?.show_hero !== false && (
          <SocialMediaHero
            title={pageData?.hero_title || pageData?.title || "Mobile Application Development"}
            desc={pageData?.hero_subtitle || pageData?.description || "If your dream is to own a website with distinctive design..."}
            bgImage="/template/template-two-hero.png"
            planetImage={null}
          />
        )}
        
        {pageData?.show_features !== false && (
          <WebDesignFeatures
            title={pageData?.features_title || "What distinguish us at Mobile app development?"}
            description={pageData?.features_subtitle || " "}
            featuresList={pageData?.features_list}
          />
        )}
        
        {pageData?.show_portfolio !== false && <WebDesignPortfolio isMobileApp={true} />}
        
        {pageData?.show_how_we_work !== false && (
          <MobileAppHowWeWork howWeWorkList={pageData?.how_we_work_list} />
        )}
        
        {pageData?.show_request !== false && <WebDesignRequest />}
        
        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
    </main>
  );
}
