"use client";
import React, { useState, useEffect } from "react";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import Image from "next/image";
import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";
import ServicesFeatures from "../components/Services-web-hosting/ServicesFeatures";
import PortfolioGallery from "../components/Portfolio/PortfolioGallery";
import SocialMediaHowWeWork from "../components/Services-social-media/SocialMediaHowWeWork";
import HostingOffers from "../components/Services-web-hosting/HostingOffers";
import SocialMediaContact from "../components/Services-social-media/SocialMediaContact";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export default function TemplateOnePage() {
  const [pageData, setPageData] = useState(null);
  const { language } = useLanguage();

  useEffect(() => {
    fetchAPI(`/services/network-marketing?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-10">
        {pageData?.show_hero !== false && (
          <SocialMediaHero 
            title={pageData?.hero_title || pageData?.title || "Network Marketing"}
            desc={pageData?.hero_subtitle || pageData?.description || "Network marketing is a business model that depends on person-to-person sales by independent representatives, often working from home. A network marketing business may require you to build a network of business partners or salespeople to assist with lead generation and closing sales."}
            bgImage="/template/template-one-hero.png"
            planetImage={null}
          />
        )}
        
        {pageData?.show_features !== false && (
          <ServicesFeatures 
            featuresTitle={pageData?.features_title || "Advantages of a web marketing website from serv5"}
            featuresSubtitle={pageData?.features_subtitle || ""}
            featuresList={pageData?.features_list || [
              {
                id: 1,
                title: "Network marketing strategy",
                desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              },
              {
                id: 2,
                title: "Targeted audience engagement",
                desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              },
              {
                id: 3,
                title: "Social media management",
                desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              },
              {
                id: 4,
                title: "Content creation and distribution",
                desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              },
              {
                id: 5,
                title: "Performance tracking and analytics",
                desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              },
              {
                id: 6,
                title: "Continuous optimization",
                desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              },
            ]}
          />
        )}
        
        {pageData?.show_portfolio !== false && (
          <div className="py-10">
            <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-10">
              <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
                Some of our web marketing website work
              </h2>
            </div>
            <div className="max-w-7xl mx-auto px-6 lg:px-20">
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {[1, 2, 3, 4, 5, 6].map((item) => (
                  <div key={item} className="relative group overflow-hidden rounded-xl aspect-[1.6/1] bg-[#ffffff10] border border-[#ffffff20]">
                    <Image
                      src={`/portfolio/image-${item}.png`}
                      alt={`Portfolio Work ${item}`}
                      fill
                      className="object-cover transition-transform duration-500 group-hover:scale-110"
                    />
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {pageData?.show_how_we_work !== false && (
          <SocialMediaHowWeWork howWeWorkList={pageData?.how_we_work_list} />
        )}

        {pageData?.show_hosting_offers !== false && (
          <HostingOffers offersList={pageData?.hosting_offers_list} title="WPR Marketing Offers" />
        )}

        {pageData?.show_request !== false && <SocialMediaContact />}
        
        {pageData?.show_blogs !== false && <LatestBlogs />}
      </div>
    </main>
  );
}
