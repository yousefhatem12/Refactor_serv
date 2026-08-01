"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { motion } from "framer-motion";
import { fetchAPI } from "../../lib/api";
import Loading from "../loading";

import { useLanguage } from "../context/LanguageContext";

const OurProductsListingPage = () => {
  const { language, t } = useLanguage();
  const isAr = language === "ar";
  const [productsList, setProductsList] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [loading, setLoading] = useState(true);
  const [isLoadingMore, setIsLoadingMore] = useState(false);

  const [prevLanguage, setPrevLanguage] = useState(language);

  if (language !== prevLanguage) {
    setPrevLanguage(language);
    setLoading(true);
    setCurrentPage(1);
  }

  useEffect(() => {
    fetchAPI(`/products?lang=${language}&page=1&per_page=9`)
      .then((res) => {
        if (res && Array.isArray(res.data)) {
          setProductsList(res.data);
          setHasMore(res.has_more);
        } else {
          setProductsList([]);
          setHasMore(false);
        }
      })
      .catch((err) => {
        console.error("Failed to fetch products:", err);
        setProductsList([]);
        setHasMore(false);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [language]);

  if (loading) {
    return <Loading />;
  }

  const loadMoreProducts = () => {
    if (isLoadingMore || !hasMore) return;
    setIsLoadingMore(true);
    const nextPage = currentPage + 1;
    fetchAPI(`/products?lang=${language}&page=${nextPage}&per_page=9`)
      .then((res) => {
        if (res && Array.isArray(res.data)) {
          setProductsList((prev) => {
            const newProducts = res.data.filter((item) => !prev.some((p) => p.id === item.id));
            return [...prev, ...newProducts];
          });
          setHasMore(res.has_more);
          setCurrentPage(nextPage);
        }
      })
      .catch((err) => {
        console.error("Failed to load more products:", err);
      })
      .finally(() => {
        setIsLoadingMore(false);
      });
  };

  const titleText = t("ourProducts") || "";
  const words = titleText ? titleText.split(" ") : [];
  const lastWord = words.pop() || "";
  const firstPart = words.join(" ");

  return (
    <main className="min-h-screen pt-32 pb-16 flex flex-col items-center">
      {/* Header Section */}
      <section className="w-full max-w-[1400px] mx-auto px-6 md:px-10 mb-16 flex flex-col items-center text-center z-10">
        <motion.h1
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6"
        >
          {firstPart && <span className="text-white">{firstPart} </span>}
          <span className="text-[#F18805]">{lastWord}</span>
        </motion.h1>
        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.1 }}
          className="text-white/80 text-sm md:text-lg leading-relaxed max-w-3xl"
        >
          {t("ourProductsDesc")}
        </motion.p>
      </section>

      {/* Grid Section */}
      <section className="w-full max-w-[1400px] mx-auto px-6 md:px-10 flex flex-col items-center z-10">
        {loading ? (
          <div className="flex items-center justify-center py-12">
            <div className="w-12 h-12 border-4 border-[#F18805] border-t-transparent rounded-full animate-spin"></div>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full mb-8">
              {productsList.map((item, index) => (
                <Link
                  href={`/our-products/${item.id}`}
                  key={item.id}
                  className="block group"
                  prefetch={false}
                >
                  <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, delay: index * 0.1 }}
                    className="flex flex-col h-full bg-[#1e1e2d]/60 backdrop-blur-md rounded-xl overflow-hidden border border-white/10 hover:border-[#F18805]/50 transition-colors shadow-lg"
                  >
                    {/* Image Container */}
                    <div className="relative w-full aspect-video overflow-hidden bg-[#2a2a35]">
                      <Image
                        src={item.image || "/portfolio/image-1.webp"}
                        alt={item.title || item.name}
                        fill
                        className="object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-[#1e1e2d] to-transparent opacity-80" />
                      <div className="absolute top-4 left-4 bg-[#F18805] text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                        {item.category}
                      </div>
                    </div>

                    {/* Content Container */}
                    <div className="p-6 flex flex-col flex-grow">
                      <h3 className="text-2xl font-bold text-white mb-3 group-hover:text-[#F18805] transition-colors">
                        {item.name}
                      </h3>
                      <p className="text-white/70 text-sm mb-6 flex-grow line-clamp-3">
                        {item.description}
                      </p>
                      <div className="mt-auto flex items-center justify-between">
                        <span className="text-[#F18805] font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
                          {t("viewFullDetails")}
                          <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            className={isAr ? "rotate-180" : ""}
                          >
                            <path
                              d="M5 12H19M19 12L12 5M19 12L12 19"
                              stroke="currentColor"
                              strokeWidth="2"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                            />
                          </svg>
                        </span>
                      </div>
                    </div>
                  </motion.div>
                </Link>
              ))}
              {isLoadingMore && [1, 2, 3].map((i) => (
                <div
                  key={`skeleton-more-${i}`}
                  className="flex flex-col h-full bg-[#1e1e2d]/60 rounded-xl overflow-hidden border border-white/10 animate-pulse aspect-video min-h-[300px]"
                />
              ))}
            </div>

            {/* Load More Button */}
            {hasMore && (
              <div className="flex justify-center mt-12 w-full z-10">
                <button
                  onClick={loadMoreProducts}
                  disabled={isLoadingMore}
                  className="flex items-center gap-2 px-8 py-3 text-white bg-transparent border border-[#F18805] hover:bg-[#F18805] rounded-full transition-all duration-300 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-base font-semibold"
                >
                  {isLoadingMore ? (
                    <svg
                      className="w-5 h-5 animate-spin"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  ) : (
                    <svg
                      className="w-5 h-5"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M19 13H5v-2h14v2z"
                        fill="currentColor"
                      />
                      <path
                        d="M12 5v14M5 12h14"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                      />
                    </svg>
                  )}
                  {isLoadingMore ? t("loading") : t("loadMore")}
                </button>
              </div>
            )}
          </>
        )}
      </section>
    </main>
  );
};

export default OurProductsListingPage;
