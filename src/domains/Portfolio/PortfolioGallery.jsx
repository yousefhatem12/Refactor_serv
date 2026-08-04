"use client";
import React, { useState, useEffect, useRef } from "react";
import Image from "next/image";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";

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

const PortfolioCard = ({ item, t, language, siteLogo }) => {
  const defaultImage = siteLogo || "/our-portfolio/Rectangle 6.webp";
  const initialImg = item.image ? getMediaUrl(item.image) : defaultImage;
  const [imgSrc, setImgSrc] = useState(initialImg);
  const [isImageLoaded, setIsImageLoaded] = useState(false);

  const [prevImage, setPrevImage] = useState(item.image);
  if (item.image !== prevImage) {
    setPrevImage(item.image);
    setImgSrc(item.image ? getMediaUrl(item.image) : defaultImage);
  }

  const projectUrl = `/portfolio/${item.slug || item.id}`;

  return (
    <motion.div
      layout
      variants={itemVariants}
      className="relative group overflow-hidden rounded-xl aspect-[1.6/1] bg-[#ffffff10] border border-[#ffffff20] cursor-pointer"
    >
      {/* Shimmer / Skeleton background behind the image */}
      {!isImageLoaded && (
        <div className="absolute inset-0 bg-white/5 animate-pulse z-0" />
      )}

      <Image
        src={imgSrc}
        alt={item.name}
        fill
        className={`transition-all duration-700 group-hover:scale-110 ${
          isImageLoaded ? "opacity-100 scale-100" : "opacity-0 scale-95"
        } ${imgSrc === siteLogo ? "object-contain p-4" : "object-cover"}`}
        onLoad={() => setIsImageLoaded(true)}
        onError={() => setImgSrc(defaultImage)}
      />

      {/* Overlay with Details - clickable link spanning the whole card */}
      <Link
        href={projectUrl}
        prefetch={false}
        className="absolute inset-0 bg-gradient-to-t from-[#040A37]/90 via-[#040A37]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6 z-10"
      >
        <h3 className="text-xl md:text-2xl font-bold text-white mb-2 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
          {item.name}
        </h3>
        {item.description && (
          <p className="text-gray-300 text-sm md:text-base line-clamp-2 mb-4 translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
            {item.description}
          </p>
        )}
        <div
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
        </div>
      </Link>
    </motion.div>
  );
};

export default function PortfolioGallery({ data, perPage = 9 }) {
  const { t, language } = useLanguage();
  const settings = useSettings();
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : "/logo.svg";

  const [activeTab, setActiveTab] = useState("all");
  const [portfolioItems, setPortfolioItems] = useState([]);
  const [categories, setCategories] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [categoriesLoaded, setCategoriesLoaded] = useState(false);
  const [fetchTrigger, setFetchTrigger] = useState(0); // forces re-fetch even when page stays 1

  const sentinelRef = useRef(null);

  useEffect(() => {
    if (!hasMore || isLoading) return;

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting) {
          setCurrentPage((prev) => prev + 1);
        }
      },
      { rootMargin: "200px" }
    );

    const el = sentinelRef.current;
    if (el) observer.observe(el);

    return () => {
      if (el) observer.unobserve(el);
    };
  }, [hasMore, isLoading]);

  // Reset categories cache when language changes
  const [prevLanguage, setPrevLanguage] = useState(language);
  if (language !== prevLanguage) {
    setPrevLanguage(language);
    setCategoriesLoaded(false);
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

  // Fetch projects dynamically when tab, page, language, categoriesLoaded, or fetchTrigger changes
  useEffect(() => {
    if (!categoriesLoaded) return;

    let isMounted = true;
    setIsLoading(true);

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
        } else {
          // API returned nothing — clear the list
          if (currentPage === 1) setPortfolioItems([]);
          setHasMore(false);
        }
      })
      .catch((err) => console.error("Failed to fetch portfolio projects:", err))
      .finally(() => {
        if (isMounted) setIsLoading(false);
      });

    return () => {
      isMounted = false;
    };
  }, [activeTab, currentPage, language, categoriesLoaded, perPage, fetchTrigger]);

  const handleTabChange = (tabId) => {
    if (tabId === activeTab) {
      // Same tab clicked — force re-fetch by bumping the trigger
      setFetchTrigger((n) => n + 1);
      return;
    }
    setPortfolioItems([]);
    setCurrentPage(1);
    setHasMore(false);
    setActiveTab(tabId);
    setFetchTrigger((n) => n + 1);
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
          <h1 className="text-4xl md:text-5xl lg:text-5xl font-bold text-white mb-6 uppercase">
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
        <AnimatePresence mode="wait">
          {portfolioItems.length > 0 ? (
            <>
              {portfolioItems.map((item) => (
                <PortfolioCard
                  key={item.id}
                  item={item}
                  t={t}
                  language={language}
                  siteLogo={siteLogo}
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

      {/* Infinite Scroll Sentinel */}
      {hasMore && (
        <div ref={sentinelRef} className="w-full h-16 flex items-center justify-center mt-8">
          <span className="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
        </div>
      )}
    </section>
  );
}
