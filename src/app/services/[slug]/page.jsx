"use client";
import React, { useState, useEffect } from "react";
import { useParams, useRouter } from "next/navigation";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";

// Import all possible sections
import ServicesHero from "../../components/Services-web-hosting/ServicesHero";
import ServicesFeatures from "../../components/Services-web-hosting/ServicesFeatures";
import SocialMediaHowWeWork from "../../components/Services-social-media/SocialMediaHowWeWork";
import HostingOffers from "../../components/Services-web-hosting/HostingOffers";
import DigitalMarketingServices from "../../components/Services-social-media/DigitalMarketingServices";
import SocialMediaWhyChooseUs from "../../components/Services-social-media/SocialMediaWhyChooseUs";
import SocialMediaContact from "../../components/Services-social-media/SocialMediaContact";
import LatestBlogs from "../../components/Services-web-hosting/LatestBlogs";
import Navbar from "../../components/Navbar/Navbar";
import Footer from "../../components/Footer/Footer";

export default function DynamicServicePage() {
  const { slug } = useParams();
  const { language } = useLanguage();
  const router = useRouter();
  const [pageData, setPageData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!slug) return;
    
    fetchAPI(`/services/${slug}?lang=${language}`)
      .then((data) => {
        if (!data || data.message === "Service not found") {
          router.push("/404");
          return;
        }
        setPageData(data);
      })
      .catch((error) => {
        console.error("Error fetching service:", error);
        router.push("/404");
      })
      .finally(() => {
        setLoading(false);
      });
  }, [slug, language, router]);

  if (loading) {
    return (
      <div className="min-h-screen bg-[#020617] flex items-center justify-center">
        <div className="w-16 h-16 border-4 border-[#FF8A00] border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  if (!pageData) return null;

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden bg-[#020617]">
      <Navbar />
      <div className="w-full relative z-10 pb-10 pt-20">
        {pageData?.show_hero !== false && (
          <ServicesHero 
            title={pageData?.hero_title || pageData?.name} 
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

        {pageData?.show_how_we_work !== false && (
          <SocialMediaHowWeWork 
            howWeWorkList={pageData?.how_we_work_list}
          />
        )}

        {pageData?.show_hosting_offers !== false && (
          <HostingOffers 
            offersList={pageData?.hosting_offers_list}
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

        {pageData?.show_request !== false && <SocialMediaContact />}
        
        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
      <Footer />
    </main>
  );
}
