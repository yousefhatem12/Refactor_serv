"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useRouter } from "next/navigation";

const HostingOffers = ({ title, offersList }) => {
  const router = useRouter();
  const { language, t } = useLanguage();
  const [activeTab, setActiveTab] = useState("Web hosting");

  const tabs = [
    t("tabWebHosting") || "Web hosting",
    t("tabFullServers") || "Full Servers",
    t("tabVps") || "VPS",
    t("tabReseller") || "Reseller",
  ];

  const defaultOffers = [
    {
      id: "silver",
      name: t("silverServer") || "Silver Server",
      price: "$180",
      period: t("perMonth") || "/mo",
      buttonText: t("bookNow") || "Book Now !",
      isPopular: false,
      features: [
        t("storage200gb"),
        t("freeSsl"),
        t("dailyBackup"),
        t("support247"),
        t("unlimitedBandwidth"),
      ],
    },
    {
      id: "bronze",
      name: t("bronzeServer") || "Bronze Server",
      price: "$270",
      period: t("perMonth") || "/mo",
      buttonText: t("bookNow") || "Book Now !",
      isPopular: false,
      features: [
        t("storage300gb"),
        t("freeSsl"),
        t("dailyBackup"),
        t("support247"),
        t("unlimitedBandwidth"),
      ],
    },
    {
      id: "unique",
      name: t("uniqueServer") || "Unique Server",
      price: "$500",
      period: t("perMonth") || "/mo",
      subtitle:
        t("uniqueServerSubtitle") ||
        "Yearly price for new users. Plus $70.00 on 30% renew.",
      buttonText: t("choosePlan") || "Choose Plan",
      isPopular: true,
      features: [
        t("advancedDdos"),
        t("quickTimeCollab"),
        t("dailyBackup"),
        t("support247"),
        t("unlimitedBandwidth"),
        t("malwareShield"),
        t("securityAudit"),
        t("freeMigration"),
      ],
    },
    {
      id: "golden",
      name: t("goldenServer") || "Golden Server",
      price: "$350",
      period: t("perMonth") || "/mo",
      buttonText: t("bookNow") || "Book Now !",
      isPopular: false,
      features: [
        t("storage400gb"),
        t("freeSsl"),
        t("dailyBackup"),
        t("support247"),
        t("unlimitedBandwidth"),
        t("prioritySupport"),
      ],
    },
  ];

  const offers = (offersList && Array.isArray(offersList) && offersList.length > 0)
    ? offersList.map((item, index) => {
        const name = item.title && typeof item.title === "object" ? (item.title[language] || item.title.en || "") : (item.title || "");
        
        // Structured pricing fields check
        if (item.price !== undefined && item.price !== null) {
          const price = item.price;
          const currency = item.currency || "$";
          const period = item.period && typeof item.period === "object" ? (item.period[language] || item.period.en || "") : (item.period || "");
          const buttonText = item.button_text && typeof item.button_text === "object" ? (item.button_text[language] || item.button_text.en || "") : (item.button_text || t("bookNow") || "Book Now !");
          const buttonUrl = item.button_url || "/contact-us";
          const isPopular = item.is_popular === true || item.is_popular === "true" || item.is_popular === 1 || item.is_popular === "1";
          
          let featuresList = [];
          if (item.features) {
            const rawFeatures = typeof item.features === "object" ? (item.features[language] || item.features.en || item.features) : item.features;
            if (typeof rawFeatures === "string") {
              featuresList = rawFeatures.split(/\r?\n/).map(f => f.trim()).filter(Boolean);
            } else if (Array.isArray(rawFeatures)) {
              featuresList = rawFeatures;
            }
          }
          
          return {
            id: `offer-${index}`,
            name: name,
            price: `${currency}${price}`,
            period: period,
            buttonText: buttonText,
            buttonUrl: buttonUrl,
            isPopular: isPopular,
            features: featuresList,
          };
        }

        // Legacy format parsing logic
        const desc = item.desc && typeof item.desc === "object" ? (item.desc[language] || item.desc.en || "") : (item.desc || "");
        const lines = desc ? desc.split(/\r?\n/).map(f => f.trim()).filter(Boolean) : [];
        
        let price = "$180";
        let period = "/mo";
        let subtitle = "";
        let filteredFeatures = [];
        
        lines.forEach(f => {
          if (f.startsWith("Price:") || f.includes("$") || f.toLowerCase().includes("price")) {
            price = f.replace(/price:?/i, "").trim();
          } else if (f.startsWith("Period:") || f.toLowerCase().includes("month") || f.toLowerCase().includes("year") || f.includes("/mo") || f.includes("/yr")) {
            period = f.replace(/period:?/i, "").trim();
          } else if (f.startsWith("Subtitle:") || f.toLowerCase().includes("subtitle")) {
            subtitle = f.replace(/subtitle:?/i, "").trim();
          } else {
            filteredFeatures.push(f);
          }
        });
        
        if (filteredFeatures.length === 0 && desc) {
          filteredFeatures = [desc];
        }

        return {
          id: `offer-${index}`,
          name: name,
          price: price,
          period: period,
          subtitle: subtitle || undefined,
          buttonText: t("bookNow") || "Book Now !",
          buttonUrl: "/contact-us",
          isPopular: index === 2 || (offersList.length === 1),
          features: filteredFeatures,
        };
      })
    : [];

  if (offers.length === 0) return null;
  const showTabs = false;

  let offersToRender = [...offers];
  if (offersToRender.length === 3) {
    const popularIndex = offersToRender.findIndex(o => o.isPopular);
    if (popularIndex === 2) {
      const temp = offersToRender[1];
      offersToRender[1] = offersToRender[2];
      offersToRender[2] = temp;
    } else if (popularIndex === 0) {
      const temp = offersToRender[1];
      offersToRender[1] = offersToRender[0];
      offersToRender[0] = temp;
    }
  }

  return (
    <section className="relative w-full py-24 px-4 sm:px-6 lg:px-6 z-20">
      <div className="max-w-7xl mx-auto">
        <motion.h2
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-white text-3xl md:text-5xl font-bold text-center mb-14 uppercase tracking-wide"
        >
          {title || t("hostingOffersTitle")}
        </motion.h2>

        {/* Tabs */}
        {showTabs && (
          <div className="flex justify-center gap-3 mb-16">
            {tabs.map((tab) => (
              <button
                key={tab}
                onClick={() => setActiveTab(tab)}
                className={`px-5 py-2.5 md:py-3 rounded-full border transition-all duration-300 text-xs md:text-sm font-semibold tracking-wider uppercase
                  ${
                    activeTab === tab
                      ? "border-[#D98B50] text-[#D98B50] bg-[#D98B50]/10 shadow-[0_0_15px_rgba(217,139,80,0.2)]"
                      : "border-gray-500/50 text-gray-400 hover:border-gray-300 hover:text-white bg-white/5 backdrop-blur-sm"
                  }`}
              >
                {tab}
              </button>
            ))}
          </div>
        )}

        {/* Cards */}
        <div
          className={`grid gap-6 items-stretch pt-6 ${
            offersToRender.length === 3
              ? "grid-cols-1 md:grid-cols-3 max-w-6xl mx-auto"
              : "grid-cols-1 md:grid-cols-2 xl:grid-cols-4"
          }`}
        >
          {offersToRender.map((offer, index) => (
            <motion.div
              key={offer.id}
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.15, duration: 0.5 }}
              className={`relative flex flex-col h-full rounded-3xl p-6 lg:p-8 transition-all duration-300 hover:-translate-y-2
                ${
                  offer.isPopular
                    ? "bg-gradient-to-b from-white to-gray-50 text-black xl:scale-105 z-10 shadow-[0_0_40px_rgba(217,139,80,0.25)] border-2 border-[#D98B50]"
                    : "bg-[#0d143a]/80 backdrop-blur-md text-white border border-white/10 hover:border-white/20 hover:shadow-xl hover:shadow-black/50"
                }`}
            >
              {offer.isPopular && (
                <div className="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-[#D98B50] to-orange-600 text-white text-xs md:text-sm font-bold px-6 py-1.5 md:py-2 rounded-full shadow-lg whitespace-nowrap uppercase tracking-wider">
                  {t("mostPopular") || "Most Popular"}
                </div>
              )}

              <div className="mb-6 flex-grow-0 text-center xl:text-start">
                <h3
                  className={`text-xl md:text-2xl font-bold mb-4 ${offer.isPopular ? "text-gray-900" : "text-white"}`}
                >
                  {offer.name}
                </h3>
                <div className="flex items-baseline justify-center xl:justify-start gap-1 mb-2">
                  <span
                    className={`text-4xl lg:text-5xl font-extrabold ${offer.isPopular ? "text-black" : "text-white"}`}
                  >
                    {offer.price}
                  </span>
                  <span
                    className={
                      offer.isPopular
                        ? "text-gray-500 font-medium text-sm md:text-base"
                        : "text-gray-400 font-medium text-sm md:text-base"
                    }
                  >
                    {offer.period}
                  </span>
                </div>
                {offer.subtitle ? (
                  <p className="text-xs md:text-sm text-gray-500 mt-3 leading-relaxed xl:min-h-[40px]">
                    {offer.subtitle}
                  </p>
                ) : (
                  <div className="mt-3 xl:min-h-[40px]"></div>
                )}
              </div>

              <button
                onClick={() => router.push(offer.buttonUrl || "/contact-us")}
                className={`w-full py-3.5 md:py-4 rounded-xl font-bold text-sm transition-all mb-8 shadow-md hover:shadow-lg
                  ${
                    offer.isPopular
                      ? "bg-gradient-to-r from-[#D98B50] to-orange-600 text-white hover:opacity-90 hover:-translate-y-0.5"
                      : "bg-white/5 border border-[#D98B50]/50 text-[#D98B50] hover:bg-[#D98B50] hover:text-white hover:-translate-y-0.5"
                  }`}
              >
                {offer.buttonText}
              </button>

              <div className="flex-grow flex flex-col">
                <h4
                  className={`text-sm font-semibold mb-4 border-b pb-3 uppercase tracking-wider
                    ${offer.isPopular ? "text-gray-900 border-gray-200" : "text-gray-300 border-white/10"}
                  `}
                >
                  {t("featuresLabel") || "Top Features"}
                </h4>
                <div className="space-y-3.5 pt-2">
                  {offer.features.map((feature, i) => (
                    <div key={i} className="flex items-start gap-3">
                      <svg
                        className={`w-5 h-5 flex-shrink-0 mt-0.5 ${offer.isPopular ? "text-[#D98B50]" : "text-[#D98B50]"}`}
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth={2.5}
                          d="M5 13l4 4L19 7"
                        />
                      </svg>
                      <span
                        className={`text-sm leading-relaxed ${offer.isPopular ? "text-gray-700 font-medium" : "text-gray-300"}`}
                      >
                        {feature}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default HostingOffers;
