"use client";
import React, { useState, useEffect } from "react";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";
import DigitalMarketingServices from "../components/Services-social-media/DigitalMarketingServices";
import SocialMediaWhyChooseUs from "../components/Services-social-media/SocialMediaWhyChooseUs";
import SocialMediaHowWeWork from "../components/Services-social-media/SocialMediaHowWeWork";
import SocialMediaContact from "../components/Services-social-media/SocialMediaContact";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export default function SocialMediaPage() {
  const [pageData, setPageData] = useState(null);
  const { language } = useLanguage();

  useEffect(() => {
    fetchAPI(`/services/social-media?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        {pageData?.show_hero !== false && (
          <SocialMediaHero 
            title={pageData?.hero_title || pageData?.title} 
            desc={pageData?.hero_subtitle || pageData?.description} 
          />
        )}
        {pageData?.show_digital_marketing !== false && (
          <DigitalMarketingServices 
            marketingTitle={pageData?.digital_marketing_title}
            marketingSubtitle={pageData?.digital_marketing_subtitle}
            marketingList={pageData?.digital_marketing_list}
          />
        )}
        {pageData?.show_why_choose_us !== false && (
          <SocialMediaWhyChooseUs 
            whyTitle={pageData?.why_choose_us_title}
            whySubtitle={pageData?.why_choose_us_subtitle}
            whyList={pageData?.why_choose_us_list}
          />
        )}
        {pageData?.show_how_we_work !== false && (
          <SocialMediaHowWeWork 
            howWeWorkList={pageData?.how_we_work_list}
          />
        )}
        {pageData?.show_request !== false && <SocialMediaContact />}
        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
    </main>
  );
}
