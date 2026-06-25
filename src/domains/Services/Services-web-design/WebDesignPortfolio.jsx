"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI } from "@/lib/api";
import Link from "next/link";
import Image from "next/image";

const WebDesignPortfolio = ({ isMobileApp }) => {
  const { language, t } = useLanguage();
  const getTranslation = (key, fallback) => {
    const val = t(key);
    return val === key ? fallback : val;
  };
  const [activeTab, setActiveTab] = useState("all");
  const [visibleTabsCount, setVisibleTabsCount] = useState(3);
  const [portfolioItems, setPortfolioItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [visibleCount, setVisibleCount] = useState(4);

  const targetCategory = isMobileApp ? "mobile" : "websites";

  useEffect(() => {
    fetchAPI(`/portfolio?lang=${language}&category=${targetCategory}`)
      .then((data) => {
        if (data && Array.isArray(data)) {
          setPortfolioItems(data);
        }
      })
      .catch((err) => console.error("Error fetching portfolio:", err))
      .finally(() => setLoading(false));
  }, [language, targetCategory]);

  const [prevActiveTab, setPrevActiveTab] = useState(activeTab);
  if (activeTab !== prevActiveTab) {
    setPrevActiveTab(activeTab);
    setVisibleCount(4);
  }

  const items = portfolioItems.filter(
    (item) => item.category === targetCategory,
  );

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
    const activeId = items.some((item) => item.id.toString() === activeTab)
      ? activeTab
      : items[0]?.id.toString();
    const activeItem = items.find((item) => item.id.toString() === activeId);
    filteredItems = activeItem ? [activeItem] : [];
  } else {
    const allTags = new Set();
    items.forEach((item) => {
      if (Array.isArray(item.tags)) {
        item.tags.forEach((tag) => allTags.add(tag));
      }
    });
    const uniqueTags = Array.from(allTags);

    tabs = [
      { id: "all", label: t("portfolioTabsAll") || "All" },
      ...uniqueTags.map((tag) => ({ id: tag, label: tag.toUpperCase() })),
    ];

    filteredItems =
      activeTab === "all"
        ? items
        : items.filter(
            (item) => Array.isArray(item.tags) && item.tags.includes(activeTab),
          );
  }

  const displayedItems = filteredItems.slice(0, visibleCount);

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
              className="text-white text-3xl xl:text-5xl font-bold mb-6 leading-tight"
            >
              {isMobileApp
                ? getTranslation("maPortfolioTitle", language === "ar" ? "بعض من أعمالنا في تطوير تطبيقات الهاتف" : "Some of our Mobile app development work at Serv5")
                : getTranslation("wdPortfolioTitle", language === "ar" ? "بعض من أعمالنا في تصميم المواقع" : "Some of our Web design work at Serv5")}
            </motion.h2>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.2 }}
              className="text-gray-400 text-lg mb-10 leading-relaxed"
            >
              {isMobileApp
                ? getTranslation("maPortfolioDesc", language === "ar" ? "تصفح أحدث تطبيقات الجوال التي قمنا بتطويرها لعملائنا في مختلف المجالات." : "The latest of Serv5 works, as you browse our portfolio. Select the market in which you want to see works to find everything you look for.")
                : getTranslation("wdPortfolioDesc", language === "ar" ? "تصفح أحدث تصميمات المواقع التي قمنا بتطويرها لعملائنا." : "The latest of Serv5 works, as you browse our portfolio. Select the market in which you want to see works to find everything you look for.")}
            </motion.p>

            {tabs.length > 1 && (
              <div className="w-full flex flex-col gap-3">
                <div className="flex flex-col gap-3 overflow-y-auto overflow-x-hidden pr-2 lg:max-h-[300px] desktop-scrollbar w-full">
                  {tabs.map((tab, index) => {
                    const isActive = isMobileApp
                      ? (activeTab === tab.id || (activeTab === "all" && index === 0))
                      : activeTab === tab.id;
                    const isHiddenOnMobile = index >= visibleTabsCount;
                    return (
                      <motion.button
                        key={tab.id}
                        initial={{ opacity: 0, y: 10, x: language === 'ar' ? 20 : -20 }}
                        whileInView={{ opacity: 1, y: 0, x: 0 }}
                        viewport={{ once: true }}
                        transition={{ delay: 0.05 * (index % 5) }}
                        onClick={() => setActiveTab(tab.id)}
                        className={`text-center lg:text-left rtl:lg:text-right px-5 py-3 lg:px-6 lg:py-4 rounded-xl text-sm lg:text-base font-semibold transition-all duration-300 w-full whitespace-nowrap flex-shrink-0 ${
                          isHiddenOnMobile ? "hidden lg:flex" : "flex"
                        } items-center justify-center lg:justify-start ${
                          isActive
                            ? "bg-[#D98B50]/10 text-[#D98B50] border-l-4 rtl:border-l-0 rtl:border-r-4 border-[#D98B50] shadow-[0_4px_20px_rgba(217,139,80,0.15)]"
                            : "text-white/60 hover:text-white hover:bg-white/[0.03] border-l-4 rtl:border-l-0 rtl:border-r-4 border-transparent"
                        }`}
                      >
                        {tab.label}
                      </motion.button>
                    );
                  })}
                </div>
                
                {tabs.length > 3 && (
                  <div>
                    {tabs.length > visibleTabsCount ? (
                      <button
                        type="button"
                        onClick={() => setVisibleTabsCount((prev) => prev + 5)}
                        className="lg:hidden text-sm font-semibold text-[#D98B50] hover:underline mt-2 flex items-center justify-center gap-1 self-center bg-white/5 px-4 py-2.5 rounded-xl border border-white/10 hover:bg-white/10 transition-all cursor-pointer w-full"
                      >
                        {language === "ar" ? "عرض المزيد" : "Show More"}
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                        </svg>
                      </button>
                    ) : (
                      <button
                        type="button"
                        onClick={() => setVisibleTabsCount(3)}
                        className="lg:hidden text-sm font-semibold text-[#D98B50] hover:underline mt-2 flex items-center justify-center gap-1 self-center bg-white/5 px-4 py-2.5 rounded-xl border border-white/10 hover:bg-white/10 transition-all cursor-pointer w-full"
                      >
                        {language === "ar" ? "عرض أقل" : "Show Less"}
                        <svg className="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                        </svg>
                      </button>
                    )}
                  </div>
                )}
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
                  <Image
                    src="/template/iphone13.webp"
                    alt="iPhone Frame"
                    width={320}
                    height={650}
                    priority
                    className="relative z-10 w-full h-auto drop-shadow-2xl"
                  />
                  {/* Image inside frame */}
                  {filteredItems[0] && (
                    <div className="absolute top-[3%] left-[5%] right-[5%] bottom-[3%] z-0 rounded-[2.5rem] overflow-hidden">
                      <Image
                        src={
                          filteredItems[0].image || "/services-page/card-1.webp"
                        }
                        alt={filteredItems[0].name}
                        fill
                        sizes="(max-width: 768px) 100vw, 320px"
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
                  className="flex flex-col items-center gap-8 w-full"
                >
                  <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-2 gap-6 w-full">
                    {displayedItems.map((item, index) => (
                      <div
                        key={item.id}
                        className="relative group rounded-xl overflow-hidden aspect-[4/3] bg-white/5 border border-white/5"
                      >
                        <Image
                          src={item.image || "/services-page/card-1.webp"}
                          alt={item.name}
                          fill
                          sizes="(max-width: 768px) 50vw, 33vw"
                          className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center p-4 text-center">
                          <h4 className="text-white text-lg font-bold mb-2">
                            {item.name}
                          </h4>
                          <Link
                            href={`/portfolio/${item.slug || item.id}`}
                            prefetch={false}
                            className="text-white font-semibold tracking-wider uppercase text-xs border border-white px-4 py-2 rounded-full backdrop-blur-sm hover:bg-white hover:text-black transition-colors"
                          >
                            {t("viewProject") || "View Project"}
                          </Link>
                        </div>
                      </div>
                    ))}
                  </div>
                  {filteredItems.length > visibleCount && (
                    <button
                      onClick={() => setVisibleCount((prev) => prev + 4)}
                      className="flex items-center justify-center gap-2 px-8 py-3 text-sm md:text-base font-semibold text-white bg-transparent border border-white/20 hover:border-[#D98B50] hover:text-[#D98B50] rounded-xl transition-all duration-300 cursor-pointer"
                    >
                      {t("loadMore") || (language === "ar" ? "عرض المزيد" : "Load More")}
                    </button>
                  )}
                </motion.div>
              )}
            </AnimatePresence>
          </div>
        </div>
      </div>
      {/* Custom styles for desktop scrollbar */}
      <style
        dangerouslySetInnerHTML={{
          __html: `
        .desktop-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .desktop-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 4px;
        }
        .desktop-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(217, 139, 80, 0.3);
            border-radius: 4px;
        }
        .desktop-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(217, 139, 80, 0.6);
        }
      `,
        }}
      />
    </section>
  );
};

export default WebDesignPortfolio;
