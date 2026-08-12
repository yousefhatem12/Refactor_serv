"use client";
import React, { useState, useEffect } from "react";
import Link from "next/link";
import { motion } from "framer-motion";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";

const OurPortfolio = ({ activeCategoryId, isFeaturedOnly = false, mobileCategories = null }) => {
  const { t, language } = useLanguage();
  const settings = useSettings();
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : "/logo.svg";
  const defaultImage = siteLogo || "/our-portfolio/Rectangle 6.webp";

  const [portfolioItems, setPortfolioItems] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [isLoadingMore, setIsLoadingMore] = useState(false);

  const [prevCategory, setPrevCategory] = useState(activeCategoryId);
  const [prevLanguage, setPrevLanguage] = useState(language);

  if (activeCategoryId !== prevCategory || language !== prevLanguage) {
    setPrevCategory(activeCategoryId);
    setPrevLanguage(language);
    setCurrentPage(1);
    setPortfolioItems([]);
    setIsLoading(true);
  }

  useEffect(() => {
    let isMounted = true;

    const categoryQuery = activeCategoryId === "all" ? "all" : activeCategoryId;
    
    let apiUrl = `/portfolio?lang=${language}&category=${categoryQuery}&page=${currentPage}&per_page=3`;
    if (isFeaturedOnly) {
        apiUrl += `&featured=true`;
    }

    fetchAPI(apiUrl)
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
  }, [currentPage, language, activeCategoryId]);

  const filteredItems = Array.isArray(portfolioItems) ? portfolioItems : [];
  const isRtl = language === "ar";

  return (
    <div className="w-full max-w-7xl mx-auto px-4 md:px-8 py-12 overflow-x-hidden">
      <motion.div
        initial={{ opacity: 0, y: -20 }}
        whileInView={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, ease: "easeOut" }}
        className="text-center mb-16"
      >
        <h2 className="text-xl sm:text-2xl md:text-4xl lg:text-5xl font-black inline-block relative pb-4">
          <span className="text-[#FF8A00]">{t("our")}</span>{" "}
          <span className="text-white">{t("portfolio")}</span>
          <div
            className={`absolute bottom-0 ${isRtl ? "right-0" : "left-0"} w-[40%] h-1.5 bg-[#FF8A00] rounded-full shadow-[0_0_10px_#FF8A00]`}
          ></div>
        </h2>
      </motion.div>
      {mobileCategories}
      <div className="flex-1 h-auto lg:h-[700px] overflow-y-visible lg:overflow-y-auto overflow-x-hidden custom-scrollbar">
        <div className="flex flex-col gap-6 md:gap-10 lg:gap-20">
          {isLoading ? (
            Array.from({ length: 3 }).map((_, index) => {
              const isReversed = index % 2 !== 0;
              return (
                <div
                  key={`skeleton-${index}`}
                  className={`relative flex flex-col ${isReversed ? "lg:flex-row-reverse" : "lg:flex-row"} items-stretch gap-0 lg:gap-8 rounded-2xl lg:rounded-none overflow-hidden lg:overflow-visible min-h-[250px] md:min-h-[350px] lg:min-h-[380px] animate-pulse`}
                >
                  {/* Image Side Skeleton */}
                  <div className="w-full lg:w-1/2 flex absolute inset-0 lg:relative lg:inset-auto lg:h-[400px] z-0">
                    <div className="w-full h-full bg-white/5 rounded-xl border border-white/10" />
                  </div>
                  {/* Text Side Skeleton */}
                  <div className="relative z-10 w-full lg:w-1/2 flex flex-col justify-end lg:justify-center p-4 md:p-6 lg:p-0 lg:py-4 flex-1 pt-24 lg:pt-4 bg-gradient-to-t from-black via-black/80 to-transparent lg:bg-none">
                    <div className="h-6 bg-white/10 rounded w-[60%] mb-3 animate-pulse" />
                    <div className="h-4 bg-white/10 rounded w-[90%] mb-2 animate-pulse" />
                    <div className="h-4 bg-white/10 rounded w-[80%] mb-4 animate-pulse" />
                    <div className="flex flex-row gap-3 mt-4">
                      <div className="h-8 bg-white/10 rounded w-[80px] animate-pulse" />
                      <div className="h-8 bg-white/10 rounded w-[100px] animate-pulse" />
                    </div>
                  </div>
                </div>
              );
            })
          ) : filteredItems.length > 0 ? (
            filteredItems.map((item, index) => {
              const isReversed = index % 2 !== 0;

              return (
                <motion.div
                  key={item.id}
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true, margin: "-50px" }}
                  transition={{
                    duration: 0.6,
                    ease: "easeOut",
                    delay: index * 0.15,
                  }}
                  className={`relative flex flex-col ${isReversed ? "lg:flex-row-reverse" : "lg:flex-row"} items-stretch gap-0 lg:gap-8 group rounded-2xl lg:rounded-none border border-white/10 lg:border-none overflow-hidden lg:overflow-visible min-h-[300px] md:min-h-[350px] lg:min-h-[380px]`}
                >
                  {/* Image Side */}
                  <div className="w-full lg:w-1/2 flex absolute inset-0 lg:relative lg:inset-auto lg:h-[400px] z-0">
                    <div className="relative w-full h-full group overflow-hidden lg:rounded-xl lg:bg-white/5 lg:border lg:border-white/10 lg:shadow-2xl">
                      <img
                        src={item.image ? getMediaUrl(item.image) : siteLogo}
                        onError={(e) => {
                          e.target.src = siteLogo;
                          e.target.classList.remove("object-cover");
                          e.target.classList.add("object-contain", "p-8", "bg-gray-800");
                        }}
                        alt={item.name}
                        className={`w-full h-full transition-transform duration-500 group-hover:scale-105 ${!(item.image) ? "object-contain p-8 bg-gray-800" : "object-cover"}`}
                      />
                      {/* Dark overlay specifically for hover on desktop */}
                      <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 hidden lg:block"></div>
                    </div>
                  </div>

                  {/* Text Side */}
                  <div className="relative z-10 w-full lg:w-1/2 flex flex-col justify-end lg:justify-center p-4 md:p-6 lg:p-0 lg:py-4 flex-1 pt-24 lg:pt-4 bg-gradient-to-t from-black via-black/80 to-transparent lg:bg-none">
                    <h3 className="text-sm sm:text-base md:text-3xl lg:text-3xl font-bold text-white mb-2 tracking-tight drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] lg:drop-shadow-none mt-auto lg:mt-0">
                      {item.name}
                    </h3>
                    <p className="text-gray-200 text-[10px] sm:text-xs md:text-sm lg:text-md leading-relaxed mb-4 lg:mb-2 max-w-xl line-clamp-2 lg:line-clamp-none drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] lg:drop-shadow-none">
                      {item.description}
                    </p>
                    <div className="flex flex-row gap-2 sm:gap-3 lg:gap-6 justify-between items-center lg:items-start lg:justify-start mt-4 lg:mt-0">
                      <Link
                        href={`/portfolio/${item.id}`}
                        className="flex items-center gap-1 lg:gap-2 lg:mt-4 text-white/90 hover:text-white transition-colors group text-[10px] sm:text-xs md:text-sm lg:text-lg font-medium drop-shadow-md lg:drop-shadow-none"
                      >
                        <span className="hidden lg:inline">
                          {t("learnMore")}
                        </span>
                        <span className="lg:hidden">{t("details")}</span>
                        <svg
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          strokeWidth="2"
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          className={`w-3.5 h-3.5 md:w-5 md:h-5 lg:w-6 lg:h-6 transition-transform ${isRtl ? "rotate-180 group-hover:-translate-x-1 lg:group-hover:-translate-x-2" : "group-hover:translate-x-1 lg:group-hover:translate-x-2"}`}
                        >
                          <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                      </Link>

                      <a
                        href={item.url || "#"}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="flex items-center justify-center gap-1 sm:gap-1.5 lg:gap-3 w-fit px-2 py-1.5 sm:px-3 sm:py-1.5 md:px-4 md:py-2 lg:px-8 lg:py-3 bg-[#E1804E] hover:bg-[#c96d3f] text-white rounded-lg lg:rounded-xl transition-all shadow-[0_0_15px_rgba(225,128,78,0.4)] hover:shadow-[0_0_30px_rgba(225,128,78,0.5)] active:scale-95"
                      >
                        <svg
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          strokeWidth="2"
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          className="w-3 h-3 sm:w-3.5 sm:h-3.5 md:w-4 md:h-4 lg:w-5 lg:h-5"
                        >
                          <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                        <span className="font-bold text-[10px] sm:text-xs md:text-sm lg:text-base whitespace-nowrap">
                          {t("live")}
                        </span>
                      </a>
                    </div>
                  </div>
                </motion.div>
              );
            })
          ) : (
            <div className="text-center py-20 text-gray-400 w-full">
              {language === "ar"
                ? "لا توجد أعمال في هذا القسم"
                : "No projects found in this category"}
            </div>
          )}

          {hasMore && (
            <div className="flex hidden justify-center mt-12 w-full">
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
                    {t("loading")}
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
                    {t("loadMore")}
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
