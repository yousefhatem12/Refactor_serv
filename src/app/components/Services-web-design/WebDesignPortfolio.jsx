"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI } from "@/lib/api";

const WebDesignPortfolio = ({ isMobileApp }) => {
  const { language, t } = useLanguage();
  const [activeTab, setActiveTab] = useState("all");
  const [portfolioItems, setPortfolioItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAPI(`/portfolio?lang=${language}`)
      .then((data) => {
        if (data && Array.isArray(data)) {
          setPortfolioItems(data);
        }
      })
      .catch((err) => console.error("Error fetching portfolio:", err))
      .finally(() => setLoading(false));
  }, [language]);

  const targetCategory = isMobileApp ? "mobile" : "websites";
  const items = portfolioItems.filter(item => item.category === targetCategory);

  if (loading) {
    return (
      <div className="py-24 text-center">
        <div className="w-10 h-10 border-2 border-[#D98B50] border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>
    );
  }

  if (items.length === 0) return null;

  let tabs = [];
  let filteredItems = [];

  if (isMobileApp) {
    tabs = items.map((item) => ({ id: item.id.toString(), label: item.name }));
    const activeId = items.some(item => item.id.toString() === activeTab)
      ? activeTab
      : items[0]?.id.toString();
    const activeItem = items.find(item => item.id.toString() === activeId);
    filteredItems = activeItem ? [activeItem] : [];
  } else {
    const allTags = new Set();
    items.forEach(item => {
      if (Array.isArray(item.tags)) {
        item.tags.forEach(tag => allTags.add(tag));
      }
    });
    const uniqueTags = Array.from(allTags);
    
    tabs = [
      { id: "all", label: t("portfolioTabsAll") || "All" },
      ...uniqueTags.map(tag => ({ id: tag, label: tag.toUpperCase() }))
    ];

    filteredItems = activeTab === "all"
      ? items
      : items.filter(item => Array.isArray(item.tags) && item.tags.includes(activeTab));
  }

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
              {isMobileApp ? t("maPortfolioTitle") || "Some of our Mobile app development work at Serve5" : t("wdPortfolioTitle")}
            </motion.h2>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.2 }}
              className="text-gray-400 text-lg mb-10 leading-relaxed"
            >
              {isMobileApp ? t("maPortfolioDesc") || "The latest of Serv5 works, as you brows our portfolio. Select the market in which you want to see works to find everything you look for." : t("wdPortfolioDesc")}
            </motion.p>

            {tabs.length > 1 && (
              <div className="flex flex-col gap-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                {tabs.map((tab, index) => (
                  <motion.button
                    key={tab.id}
                    initial={{ opacity: 0, x: -20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ delay: 0.3 + index * 0.1 }}
                    onClick={() => setActiveTab(tab.id)}
                    className={`text-left px-6 py-4 rounded-xl text-lg font-medium transition-all ${
                      (activeTab === tab.id || (isMobileApp && filteredItems[0]?.id.toString() === tab.id))
                        ? "bg-[#0A1128] text-[#D98B50] border-l-4 border-[#D98B50]"
                        : "text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent"
                    }`}
                  >
                    {tab.label}
                  </motion.button>
                ))}
              </div>
            )}
          </div>

          {/* Right Column - Images Grid */}
          <div className="lg:w-2/3 flex justify-center items-center w-full">
            <AnimatePresence mode="wait">
              {isMobileApp ? (
                <motion.div
                  key={filteredItems[0]?.id || "empty"}
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
                  {filteredItems[0] && (
                    <div className="absolute top-[3%] left-[5%] right-[5%] bottom-[3%] z-0 rounded-[2.5rem] overflow-hidden">
                      <img
                        src={filteredItems[0].image || "/services-page/card-1.png"}
                        alt={filteredItems[0].name}
                        className="w-full h-full object-cover"
                      />
                    </div>
                  )}
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
                  {filteredItems.map((item, index) => (
                    <div
                      key={item.id}
                      className="relative group rounded-xl overflow-hidden aspect-[4/3] bg-white/5 border border-white/5"
                    >
                      <img
                        src={item.image || "/services-page/card-1.png"}
                        alt={item.name}
                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                      <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center p-4 text-center">
                        <h4 className="text-white text-lg font-bold mb-2">{item.name}</h4>
                        {item.url && (
                          <a
                            href={item.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-white font-semibold tracking-wider uppercase text-xs border border-white px-4 py-2 rounded-full backdrop-blur-sm hover:bg-white hover:text-black transition-colors"
                          >
                            {t("viewProject")}
                          </a>
                        )}
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
