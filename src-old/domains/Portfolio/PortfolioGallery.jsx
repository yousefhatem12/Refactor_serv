"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";

// Client-side cache store to make tab/page switching instant
const portfolioCache = {};

const containerVariants = {
  hidden: { opacity: 0 },
  show: {
    opacity: 1,
    transition: {
      staggerChildren: 0.06,
    },
  },
};

const itemVariants = {
  hidden: { opacity: 0, y: 20, scale: 0.96 },
  show: {
    opacity: 1,
    y: 0,
    scale: 1,
    transition: {
      type: "spring",
      stiffness: 110,
      damping: 15,
    },
  },
  exit: {
    opacity: 0,
    scale: 0.96,
    transition: { duration: 0.2 },
  },
};

const PortfolioCard = ({ item, t, language }) => {
  const defaultImage = "/our-portfolio/Rectangle 6.webp";
  const [imgSrc, setImgSrc] = useState(item.image || defaultImage);
  const [isImageLoaded, setIsImageLoaded] = useState(false);

  const [prevImage, setPrevImage] = useState(item.image);
  if (item.image !== prevImage) {
    setPrevImage(item.image);
    setImgSrc(item.image || defaultImage);
  }

  return (
    <motion.div
      layout
      variants={itemVariants}
      className="relative group overflow-hidden rounded-xl aspect-[1.6/1] bg-[#ffffff10] border border-[#ffffff20]"
    >
      {/* Shimmer / Skeleton background behind the image */}
      {!isImageLoaded && (
        <div className="absolute inset-0 bg-white/5 animate-pulse z-0" />
      )}

      <Image
        src={imgSrc}
        alt={item.name}
        fill
        className={`object-cover transition-all duration-700 group-hover:scale-110 ${
          isImageLoaded ? "opacity-100 scale-100" : "opacity-0 scale-95"
        }`}
        onLoad={() => setIsImageLoaded(true)}
        onError={() => setImgSrc(defaultImage)}
      />

      {/* Overlay with Details */}
      <div className="absolute inset-0 bg-gradient-to-t from-[#040A37]/90 via-[#040A37]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6 z-10">
        <h3 className="text-xl md:text-2xl font-bold text-white mb-2 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
          {item.name}
        </h3>
        {item.description && (
          <p className="text-gray-300 text-sm md:text-base line-clamp-2 mb-4 translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
            {item.description}
          </p>
        )}
        <Link
          href={`/portfolio/${item.slug || item.id}`}
          prefetch={false}
          className="inline-flex items-center gap-2 text-[#FF8A00] font-medium hover:text-[#ffb800] transition-colors translate-y-4 group-hover:translate-y-0 duration-300 delay-100 w-fit"
        >
          <span>{t("visitProject") || "Visit Project"}</span>
          <svg
            className={`w-4 h-4 ${language === "ar" ? "rotate-180" : ""}`}
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth="2"
              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
            />
          </svg>
        </Link>
      </div>
    </motion.div>
  );
};

export default function PortfolioGallery({ data, perPage = 9 }) {
  const { t, language } = useLanguage();
  const [activeTab, setActiveTab] = useState("all");
  const [portfolioItems, setPortfolioItems] = useState([]);
  const [categories, setCategories] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [categoriesLoaded, setCategoriesLoaded] = useState(false);

  // Render-phase state adjustments to avoid useEffect synchronous state updates
  const [prevLanguage, setPrevLanguage] = useState(language);
  if (language !== prevLanguage) {
    setPrevLanguage(language);
    setCategoriesLoaded(false);
  }

  const [prevTab, setPrevTab] = useState(activeTab);
  const [prevPage, setPrevPage] = useState(currentPage);
  const [prevLang, setPrevLang] = useState(language);

  if (activeTab !== prevTab || currentPage !== prevPage || language !== prevLang) {
    setPrevTab(activeTab);
    setPrevPage(currentPage);
    setPrevLang(language);
    setIsLoading(true);
  }

  // Fetch categories once on mount or when language changes
  useEffect(() => {
    fetchAPI(`/portfolio/categories?lang=${language}`)
      .then((data) => {
        if (data) setCategories(data || []);
        setCategoriesLoaded(true);
      })
      .catch((err) => {
        console.error("Failed to fetch portfolio categories:", err);
        setCategoriesLoaded(true);
      });
  }, [language]);

  // Fetch projects dynamically when tab, page, language, or categoriesLoaded changes
  useEffect(() => {
    if (!categoriesLoaded) return;

    let isMounted = true;

    const categoryQuery = activeTab === "all" ? "all" : activeTab;
    const cacheKey = `${language}-${categoryQuery}-${currentPage}-${perPage}`;

    // Read from client-side cache first to avoid framework bootstrap delay
    if (portfolioCache[cacheKey]) {
      const cached = portfolioCache[cacheKey];
      Promise.resolve().then(() => {
        if (!isMounted) return;
        if (currentPage === 1) {
          setPortfolioItems(cached.data);
        } else {
          setPortfolioItems((prev) => {
            const newItems = cached.data.filter(
              (item) => !prev.some((p) => p.id === item.id),
            );
            return [...prev, ...newItems];
          });
        }
        setHasMore(cached.hasMore);
        setIsLoading(false);
      });
      return;
    }

    fetchAPI(
      `/portfolio?lang=${language}&category=${categoryQuery}&page=${currentPage}&per_page=${perPage}`,
    )
      .then((res) => {
        if (!isMounted) return;

        if (res && Array.isArray(res.data)) {
          // Store in client-side cache
          portfolioCache[cacheKey] = {
            data: res.data,
            hasMore: res.has_more,
          };

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
        }
      })
      .catch((err) => console.error("Failed to fetch portfolio projects:", err))
      .finally(() => {
        if (isMounted) setIsLoading(false);
      });

    return () => {
      isMounted = false;
    };
  }, [activeTab, currentPage, language, categoriesLoaded, perPage]);

  const handleTabChange = (tabId) => {
    setActiveTab(tabId);
    setCurrentPage(1);
    setPortfolioItems([]); // Clear previous items immediately to trigger skeleton load loader
  };

  const tabs = [
    { label: t("portfolioTabsAll") || "ALL", id: "all" },
    ...categories.map((cat) => ({ label: cat.label, id: cat.id })),
  ];

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
        {!categoriesLoaded
          ? // Tabs loading skeleton
            Array.from({ length: 5 }).map((_, i) => (
              <div
                key={`tab-skeleton-${i}`}
                className="h-10 w-24 bg-white/5 border border-white/10 rounded-[2px] animate-pulse"
              />
            ))
          : tabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => handleTabChange(tab.id)}
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
      <motion.div
        layout
        variants={containerVariants}
        initial="hidden"
        animate="show"
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12 min-h-[300px] content-start"
      >
        <AnimatePresence mode="popLayout">
          {portfolioItems.length > 0 ? (
            <>
              {portfolioItems.map((item) => (
                <PortfolioCard
                  key={item.id}
                  item={item}
                  t={t}
                  language={language}
                />
              ))}
              {isLoading &&
                [1, 2, 3].map((i) => (
                  <motion.div
                    layout
                    variants={itemVariants}
                    key={`skeleton-more-${i}`}
                    className="relative overflow-hidden rounded-xl aspect-[1.6/1] bg-white/5 border border-white/10 animate-pulse"
                  />
                ))}
            </>
          ) : isLoading ? (
            Array.from({ length: perPage }).map((_, i) => (
              <motion.div
                variants={itemVariants}
                key={`skeleton-${i}`}
                className="relative overflow-hidden rounded-xl aspect-[1.6/1] bg-white/5 border border-white/10 animate-pulse"
              />
            ))
          ) : (
            <div className="col-span-full text-center py-20 text-gray-400">
              <p className="text-lg">
                {t("noProjectsFound") ||
                  (language === "ar"
                    ? "لا توجد أعمال في هذا القسم"
                    : "No projects found in this category")}
              </p>
            </div>
          )}
        </AnimatePresence>
      </motion.div>

      {/* Load More Button */}
      {hasMore && (
        <div className="flex justify-center mt-8">
          <button
            onClick={() => setCurrentPage((prev) => prev + 1)}
            disabled={isLoading}
            className="flex items-center gap-2 px-8 py-3 text-white bg-transparent border border-white rounded-full hover:bg-white hover:text-[#040A37] transition-all duration-300 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isLoading ? (
              <svg
                className="w-5 h-5 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle
                  className="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  strokeWidth="4"
                ></circle>
                <path
                  className="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
            ) : (
              <svg
                className="w-5 h-5"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M12 2V6M12 18V22M4.92896 4.92896L7.75738 7.75738M16.2427 16.2426L19.0711 19.0711M2 12H6M18 12H22M4.92896 19.0711L7.75738 16.2426M16.2427 7.75738L19.0711 4.92896"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                />
              </svg>
            )}
            {isLoading ? t("loading") : t("loadMore")}
          </button>
        </div>
      )}
    </section>
  );
}
