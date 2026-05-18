"use client";
import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const WebDesignPortfolio = ({ isMobileApp }) => {
  const { t } = useLanguage();
  const [activeTab, setActiveTab] = useState("companies");

  const tabs = [
    { id: "companies", label: t("wdTabCompanies") || "Companies" },
    { id: "medical", label: t("wdTabMedical") || "Medical" },
    { id: "e-commerce", label: t("wdTabEcommerce") || "E-commerce" },
    { id: "entertainment", label: t("wdTabEntertainment") || "Entertainment" },
    { id: "drop-shipping", label: t("wdTabDropShipping") || "Drop shipping" },
  ];

  const portfolioItems = {
    companies: [
      { id: 1, img: "/services-page/card-1.png" },
      { id: 2, img: "/services-page/card-2.png" },
      { id: 3, img: "/services-page/card-3.png" },
      { id: 4, img: "/services-page/card-1.png" },
    ],
    medical: [
      { id: 5, img: "/services-page/card-2.png" },
      { id: 6, img: "/services-page/card-3.png" },
    ],
    "e-commerce": [
      { id: 7, img: "/services-page/card-3.png" },
      { id: 8, img: "/services-page/card-1.png" },
      { id: 9, img: "/services-page/card-2.png" },
    ],
    entertainment: [{ id: 10, img: "/services-page/card-1.png" }],
    "drop-shipping": [
      { id: 11, img: "/services-page/card-2.png" },
      { id: 12, img: "/services-page/card-3.png" },
    ],
  };

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col lg:flex-row gap-12">
          {/* Left Column - Text & Tabs */}
          <div className="lg:w-1/3">
            <motion.h2
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="text-white text-3xl md:text-5xl font-bold mb-6 leading-tight"
            >
              {isMobileApp ? "Some of our Mobile app development work at Serve5" : t("wdPortfolioTitle")}
            </motion.h2>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.2 }}
              className="text-gray-400 text-lg mb-10 leading-relaxed"
            >
              {isMobileApp ? "The latest of Serv5 works, as you brows our portfolio. Select the market in which you want to see works to find everything you look for." : t("wdPortfolioDesc")}
            </motion.p>

            <div className="flex flex-col gap-2">
              {tabs.map((tab, index) => (
                <motion.button
                  key={tab.id}
                  initial={{ opacity: 0, x: -20 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: 0.3 + index * 0.1 }}
                  onClick={() => setActiveTab(tab.id)}
                  className={`text-left px-6 py-4 rounded-xl text-lg font-medium transition-all ${
                    activeTab === tab.id
                      ? "bg-[#0A1128] text-[#D98B50] border-l-4 border-[#D98B50]"
                      : "text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent"
                  }`}
                >
                  {tab.label}
                </motion.button>
              ))}
            </div>
          </div>

          {/* Right Column - Images Grid */}
          <div className="lg:w-2/3 flex justify-center items-center">
            <AnimatePresence mode="wait">
              {isMobileApp ? (
                <motion.div
                  key={activeTab}
                  initial={{ opacity: 0, scale: 0.95 }}
                  animate={{ opacity: 1, scale: 1 }}
                  exit={{ opacity: 0, scale: 0.95 }}
                  transition={{ duration: 0.4 }}
                  className="relative w-full max-w-[320px] mx-auto"
                >
                  <img
                    src="/template/iphone13.png"
                    alt="iPhone Frame"
                    className="relative z-10 w-full h-auto drop-shadow-2xl"
                  />
                  {/* Image inside frame */}
                  <div className="absolute top-[3%] left-[5%] right-[5%] bottom-[3%] z-0 rounded-[2.5rem] overflow-hidden">
                    <img
                      src={portfolioItems[activeTab]?.[0]?.img || portfolioItems["companies"][0].img}
                      alt="App Screen"
                      className="w-full h-full object-cover"
                    />
                  </div>
                </motion.div>
              ) : (
                <motion.div
                  key={activeTab}
                  initial={{ opacity: 0, scale: 0.95 }}
                  animate={{ opacity: 1, scale: 1 }}
                  exit={{ opacity: 0, scale: 0.95 }}
                  transition={{ duration: 0.4 }}
                  className="grid grid-cols-1 md:grid-cols-2 gap-6 w-full"
                >
                  {portfolioItems[activeTab]?.map((item, index) => (
                    <div
                      key={item.id}
                      className="relative group rounded-xl overflow-hidden aspect-[4/3] bg-white/5"
                    >
                      <img
                        src={item.img}
                        alt="Portfolio Item"
                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                      <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span className="text-white font-semibold tracking-wider uppercase text-sm border border-white px-4 py-2 rounded-full backdrop-blur-sm">
                          {t("viewProject")}
                        </span>
                      </div>
                    </div>
                  ))}
                </motion.div>
              )}
            </AnimatePresence>
          </div>
        </div>
      </div>
    </section>
  );
};

export default WebDesignPortfolio;
