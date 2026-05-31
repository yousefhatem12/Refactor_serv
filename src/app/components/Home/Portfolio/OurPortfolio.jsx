"use client";
import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../../context/LanguageContext";

const OurPortfolio = ({ activeCategoryId }) => {
  const { t, language } = useLanguage();
  const [portfolioItems, setPortfolioItems] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [isLoadingMore, setIsLoadingMore] = useState(false);

  // Reset page and items when language changes
  useEffect(() => {
    setCurrentPage(1);
    setPortfolioItems([]);
  }, [language]);

  useEffect(() => {
    let isMounted = true;

    if (currentPage === 1) {
      setIsLoading(true);
    } else {
      setIsLoadingMore(true);
    }

    fetchAPI(`/portfolio?lang=${language}&page=${currentPage}&per_page=3`)
      .then((res) => {
        if (!isMounted) return;

        if (res && Array.isArray(res.data)) {
          if (currentPage === 1) {
            setPortfolioItems(res.data);
          } else {
            setPortfolioItems((prev) => {
              const newItems = res.data.filter(
                (item) => !prev.some((p) => p.id === item.id),
              );
              return [...prev, ...newItems];
            });
          }
          setHasMore(res.has_more);
        } else if (res && Array.isArray(res)) {
          setPortfolioItems(res);
          setHasMore(false);
        } else {
          if (currentPage === 1) {
            setPortfolioItems([]);
          }
          setHasMore(false);
        }
      })
      .catch((err) => {
        console.error("Failed to fetch portfolio:", err);
        if (currentPage === 1 && isMounted) {
          setPortfolioItems([]);
          setHasMore(false);
        }
      })
      .finally(() => {
        if (isMounted) {
          setIsLoading(false);
          setIsLoadingMore(false);
        }
      });

    return () => {
      isMounted = false;
    };
  }, [currentPage, language]);

  // Filter items based on category if needed
  const items = Array.isArray(portfolioItems) ? portfolioItems : [];
  const filteredItems =
    activeCategoryId === "all"
      ? items
      : items.filter((item) => item.category === activeCategoryId);

  const isRtl = language === "ar";

  return (
    <div className="w-full max-w-7xl mx-auto px-4 md:px-8 py-12 rtl:pr-0 overflow-x-hidden">
      <motion.div
        initial={{ opacity: 0, y: -20 }}
        whileInView={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, ease: "easeOut" }}
        className="text-center mb-16"
      >
        <h2 className="text-4xl md:text-5xl lg:text-6xl font-black inline-block relative pb-4">
          <span className="text-[#FF8A00]">{t("our")}</span>{" "}
          <span className="text-white">{t("portfolio")}</span>
          <div
            className={`absolute bottom-0 ${isRtl ? "right-0" : "left-0"} w-[40%] h-1.5 bg-[#FF8A00] rounded-full shadow-[0_0_10px_#FF8A00]`}
          ></div>
        </h2>
      </motion.div>
      <div className="flex-1 h-auto lg:h-[700px] overflow-y-visible lg:overflow-y-auto overflow-x-hidden custom-scrollbar">
        <div className="grid grid-cols-2 gap-4 md:gap-8 lg:flex lg:flex-col lg:gap-20">
          {filteredItems.map((item, index) => {
            const isReversed = index % 2 !== 0;

            return (
              <motion.div
                key={item.id}
                initial={{ opacity: 0, x: isReversed ? 50 : -50 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true, margin: "-50px" }}
                transition={{
                  duration: 0.6,
                  ease: "easeOut",
                  delay: index * 0.15,
                }}
                className={`relative flex flex-col ${isReversed ? "lg:flex-row-reverse" : "lg:flex-row"} items-stretch gap-0 lg:gap-8 group rounded-2xl lg:rounded-none overflow-hidden lg:overflow-visible min-h-[250px] md:min-h-[350px] lg:min-h-0`}
              >
                {/* Image Side */}
                <div className="w-full lg:w-1/2 flex absolute inset-0 lg:relative lg:inset-auto z-0">
                  <div className="relative w-full h-full group overflow-hidden lg:rounded-xl lg:bg-white/5 lg:border lg:border-white/10 lg:shadow-2xl">
                    <img
                      src={item.image || "/our-portfolio/Rectangle 6.png"}
                      alt={item.name}
                      className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    {/* Dark overlay specifically for hover on desktop */}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 hidden lg:block"></div>
                  </div>
                </div>

                {/* Text Side */}
                <div className="relative z-10 w-full lg:w-1/2 flex flex-col justify-end lg:justify-center p-4 md:p-6 lg:p-0 lg:py-4 h-auto  pt-24 lg:pt-4 bg-gradient-to-t from-black via-black/80 to-transparent lg:bg-none">
                  <h3 className="text-xl md:text-3xl lg:text-3xl font-bold text-white mb-2  tracking-tight drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] lg:drop-shadow-none">
                    {item.name}
                  </h3>
                  <p className="text-gray-200 text-xs md:text-sm lg:text-md leading-relaxed mb-4 lg:mb-2 max-w-xl line-clamp-2 lg:line-clamp-none drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] lg:drop-shadow-none">
                    {item.description}
                  </p>
                  <div className="flex flex-row gap-3 lg:gap-6 justify-between items-center lg:items-start lg:justify-start mt-auto lg:mt-0">
                    <a
                      href="#"
                      className="flex items-center gap-1 lg:gap-2 lg:mt-4 text-white/90 hover:text-white transition-colors group text-xs md:text-sm lg:text-lg font-medium drop-shadow-md lg:drop-shadow-none"
                    >
                      <span className="hidden lg:inline">{t("learnMore")}</span>
                      <span className="lg:hidden">{t("details")}</span>
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        className={`w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 transition-transform ${isRtl ? "rotate-180 group-hover:-translate-x-1 lg:group-hover:-translate-x-2" : "group-hover:translate-x-1 lg:group-hover:translate-x-2"}`}
                      >
                        <path d="M5 12h14M12 5l7 7-7 7" />
                      </svg>
                    </a>

                    <a
                      href={item.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="flex items-center justify-center gap-1.5 lg:gap-3 w-fit px-3 py-1.5 md:px-4 md:py-2 lg:px-8 lg:py-3 bg-[#E1804E] hover:bg-[#c96d3f] text-white rounded-lg lg:rounded-xl transition-all shadow-[0_0_15px_rgba(225,128,78,0.4)] hover:shadow-[0_0_30px_rgba(225,128,78,0.5)] active:scale-95"
                    >
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        className="w-3.5 h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5"
                      >
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                      </svg>
                      <span className="font-bold text-xs md:text-sm lg:text-base whitespace-nowrap">
                        {t("live")}
                      </span>
                    </a>
                  </div>
                </div>
              </motion.div>
            );
          })}

          {hasMore && (
            <div className="flex justify-center mt-12 w-full col-span-2">
              <button
                onClick={() => setCurrentPage((prev) => prev + 1)}
                disabled={isLoadingMore}
                className="flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-[#FF8A00] to-[#E1804E] hover:from-[#E1804E] hover:to-[#FF8A00] text-white font-bold rounded-xl shadow-[0_0_20px_rgba(255,138,0,0.3)] hover:shadow-[0_0_30px_rgba(255,138,0,0.5)] active:scale-95 disabled:opacity-50 transition-all cursor-pointer border-none uppercase tracking-wider text-sm"
              >
                {isLoadingMore ? (
                  <>
                    <svg
                      className="animate-spin h-5 w-5 text-white"
                      viewBox="0 0 24 24"
                      fill="none"
                    >
                      <circle
                        className="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        strokeWidth="4"
                      />
                      <path
                        className="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                      />
                    </svg>
                    {language === "ar" ? "جاري التحميل..." : "Loading..."}
                  </>
                ) : (
                  <>
                    <svg
                      className="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2.5"
                        d="M12 4v16m8-8H4"
                      />
                    </svg>
                    {t("loadMore") ||
                      (language === "ar" ? "عرض المزيد" : "Load More")}
                  </>
                )}
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default OurPortfolio;
