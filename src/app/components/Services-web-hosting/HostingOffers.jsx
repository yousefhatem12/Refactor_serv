"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const HostingOffers = () => {
  const { t } = useLanguage();
  const [activeTab, setActiveTab] = useState("Web hosting");

  const tabs = [t("tabWebHosting") || "Web hosting", t("tabFullServers") || "Full Servers", t("tabVps") || "VPS", t("tabReseller") || "Reseller"];

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
      id: "unique",
      name: t("uniqueServer") || "Unique Server",
      price: "$500",
      period: t("perMonth") || "/mo",
      subtitle: t("uniqueServerSubtitle") || "Yearly price for new users. Plus $70.00 on 30% renew.",
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
    <section className="relative w-full py-20 px-6 lg:px-20 z-20">
      <div className="max-w-6xl mx-auto">
        <motion.h2
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-white text-3xl md:text-5xl font-bold text-center mb-12 uppercase tracking-wide"
        >
          {t("hostingOffersTitle")}
        </motion.h2>

        {/* Tabs */}
        <div className="flex flex-wrap justify-center gap-4 md:gap-8 mb-20">
          {tabs.map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`px-8 py-3 rounded-full border transition-all duration-300 text-sm font-semibold tracking-wider uppercase
                ${
                  activeTab === tab
                    ? "border-[#D98B50] text-[#D98B50] bg-[#D98B50]/10"
                    : "border-gray-500 text-gray-400 hover:border-gray-300 hover:text-white"
                }`}
            >
              {tab}
            </button>
          ))}
        </div>

        {/* Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
          {offers.map((offer, index) => (
            <motion.div
              key={offer.id}
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.2, duration: 0.6 }}
              className={`relative rounded-3xl p-8 transition-transform duration-300 hover:-translate-y-2
                ${
                  offer.isPopular
                    ? "bg-white text-black md:scale-110 z-10 shadow-2xl py-12"
                    : "bg-[#0d143a] text-white border border-white/5"
                }`}
            >
              {offer.isPopular && (
                <div className="absolute top-0 right-8 -translate-y-1/2 bg-[#D98B50] text-white text-xs font-bold px-4 py-1.5 rounded-full">
                  {t("mostPopular")}
                </div>
              )}

              <div className="mb-8">
                <h3
                  className={`text-xl font-bold mb-4 ${offer.isPopular ? "text-gray-900" : "text-white"}`}
                >
                  {offer.name}
                </h3>
                <div className="flex items-baseline gap-1">
                  <span
                    className={`text-5xl font-extrabold ${offer.isPopular ? "text-black" : "text-white"}`}
                  >
                    {offer.price}
                  </span>
                  <span
                    className={
                      offer.isPopular
                        ? "text-gray-500 font-medium"
                        : "text-gray-400 font-medium"
                    }
                  >
                    {offer.period}
                  </span>
                </div>
                {offer.subtitle && (
                  <p className="text-xs text-gray-500 mt-4 leading-relaxed max-w-[200px]">
                    {offer.subtitle}
                  </p>
                )}
                {!offer.subtitle && <div className="h-4 mt-4"></div>}
              </div>

              <button
                className={`w-full py-4 rounded-xl font-bold text-sm transition-colors mb-10
                  ${
                    offer.isPopular
                      ? "bg-[#D98B50] text-white hover:bg-orange-600"
                      : "border border-[#D98B50] text-[#D98B50] hover:bg-[#D98B50] hover:text-white"
                  }`}
              >
                {offer.buttonText}
              </button>

              <div className="space-y-5">
                <h4
                  className={`text-sm font-semibold mb-4 ${offer.isPopular ? "text-gray-900" : "text-white"}`}
                >
                  {t("featuresLabel")}
                </h4>
                {offer.features.map((feature, i) => (
                  <div key={i} className="flex items-center gap-3">
                    <div
                      className={`w-1.5 h-1.5 rounded-full flex-shrink-0 ${offer.isPopular ? "bg-[#D98B50]" : "bg-[#D98B50]"}`}
                    ></div>
                    <span
                      className={`text-sm ${offer.isPopular ? "text-gray-600 font-medium" : "text-gray-300"}`}
                    >
                      {feature}
                    </span>
                  </div>
                ))}
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default HostingOffers;
