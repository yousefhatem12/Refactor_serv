"use client";
import React, { useState } from "react";
import Image from "next/image";

import { motion, AnimatePresence } from "framer-motion";

import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";

export default function PortfolioGallery({ data }) {
  const { t, language } = useLanguage();
  const [activeTab, setActiveTab] = useState("all");
  const [portfolioItems, setPortfolioItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  const tabs = [
    { label: t("portfolioTabsAll") || "ALL", id: "all" },
    { label: t("portfolioTabsWeb") || "WEBSITES", id: "websites" },
    { label: t("portfolioTabsMobile") || "MOBILE APPS", id: "mobile" },
    { label: t("portfolioTabsMotion") || "MOTIONS GRAPHICS", id: "motion" },
    { label: t("portfolioTabsDigital") || "DIGITAL IDENTITY", id: "digital" },
    { label: t("portfolioTabsSocial") || "SOCIAL MEDIA", id: "social1" },
  ];

  React.useEffect(() => {
    const fetchPortfolio = async () => {
      try {
        setIsLoading(true);
        const data = await fetchAPI(`/portfolio?lang=${language}`);
        setPortfolioItems(data);
      } catch (error) {
        console.error("Failed to fetch portfolio:", error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchPortfolio();
  }, [language]);

  const items = Array.isArray(portfolioItems) ? portfolioItems : [];
  const filteredItems = activeTab === "all" 
    ? items 
    : items.filter(item => item.category === activeTab);

  return (
    <section className="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20 relative z-20">
      {/* Dynamic Hero Section */}
      {data?.title && (
        <div className="text-center mb-16">
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 uppercase">
            {data.title}
          </h1>
          {data.subtitle && (
            <p className="text-gray-300 text-lg md:text-xl max-w-3xl mx-auto">
              {data.subtitle}
            </p>
          )}
        </div>
      )}

      {/* Tabs */}
      <div className="flex flex-wrap justify-center gap-4 mb-12">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            onClick={() => setActiveTab(tab.id)}
            className={`px-6 py-2 text-sm md:text-base font-medium uppercase transition-colors duration-300 rounded-[2px]
              ${
                activeTab === tab.id
                  ? "bg-white text-[#040A37]"
                  : "bg-transparent text-white border border-[#ffffff40] hover:border-white"
              }
            `}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {/* Grid */}
      <motion.div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <AnimatePresence>
          {filteredItems.map((item) => (
            <motion.div
              layout
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.9 }}
              transition={{ duration: 0.3 }}
              key={item.id}
              className="relative group overflow-hidden rounded-xl aspect-[1.6/1] bg-[#ffffff10] border border-[#ffffff20]"
            >
              <Image
                src={item.image || "/our-portfolio/Rectangle 6.png"}
                alt={item.name}
                fill
                className="object-cover transition-transform duration-500 group-hover:scale-110"
              />
              {/* Overlay with Details */}
              <div className="absolute inset-0 bg-gradient-to-t from-[#040A37]/90 via-[#040A37]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
                <h3 className="text-xl md:text-2xl font-bold text-white mb-2 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                  {item.name}
                </h3>
                {item.description && (
                  <p className="text-gray-300 text-sm md:text-base line-clamp-2 mb-4 translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
                    {item.description}
                  </p>
                )}
                {item.url && (
                  <a
                    href={item.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-2 text-[#FF8A00] font-medium hover:text-[#ffb800] transition-colors translate-y-4 group-hover:translate-y-0 duration-300 delay-100 w-fit"
                  >
                    <span>{t("visitProject")}</span>
                    <svg className={`w-4 h-4 ${language === 'ar' ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                  </a>
                )}
              </div>
            </motion.div>
          ))}
        </AnimatePresence>
      </motion.div>

      {/* Load More Button */}
      <div className="flex justify-center mt-8">
        <button className="flex items-center gap-2 px-8 py-3 text-white bg-transparent border border-white rounded-full hover:bg-white hover:text-[#040A37] transition-all duration-300">
          <svg
            className="w-5 h-5 animate-spin-slow"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M12 2V6"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
            <path
              d="M12 18V22"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
            <path
              d="M4.92896 4.92896L7.75738 7.75738"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
            <path
              d="M16.2427 16.2426L19.0711 19.0711"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
            <path
              d="M2 12H6"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
            <path
              d="M18 12H22"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
            <path
              d="M4.92896 19.0711L7.75738 16.2426"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
            <path
              d="M16.2427 7.75738L19.0711 4.92896"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
            />
          </svg>
          {t("loadMore")}
        </button>
      </div>
    </section>
  );
}
