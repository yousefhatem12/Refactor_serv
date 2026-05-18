"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const HostingOffers = ({ title }) => {
  const { t } = useLanguage();
  const [activeTab, setActiveTab] = useState("Web hosting");

  const tabs = [
    t("tabWebHosting") || "Web hosting",
    t("tabFullServers") || "Full Servers",
    t("tabVps") || "VPS",
    t("tabReseller") || "Reseller",
  ];

  const offers = [
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
        <div className="flex  justify-center gap-3 mb-16">
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

        {/* Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-5 items-stretch pt-6">
          {offers.map((offer, index) => (
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
