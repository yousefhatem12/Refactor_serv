"use client";
import React, { useState, useRef, useEffect } from "react";
import Link from "next/link";
import {
  motion,
  AnimatePresence,
  useMotionValue,
  useSpring,
  useTransform,
} from "framer-motion";
import { fetchAPI, getApiItems, getApiPage } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";

// --- Gradients for dynamic data ---
const gradients = [
  "from-blue-600/40 to-purple-900/40",
  "from-orange-600/40 to-red-900/40",
  "from-cyan-600/40 to-blue-900/40",
  "from-pink-600/40 to-purple-900/40",
  "from-emerald-600/40 to-teal-900/40",
  "from-indigo-600/40 to-blue-900/40",
];

// Dynamic categories will be generated from products

// --- 3D Card Component ---
const ProductCard = ({ product, index }) => {
  const { t, language } = useLanguage();
  const x = useMotionValue(0);
  const y = useMotionValue(0);

  const mouseXSpring = useSpring(x, { stiffness: 300, damping: 30 });
  const mouseYSpring = useSpring(y, { stiffness: 300, damping: 30 });

  const rotateX = useTransform(mouseYSpring, [-0.5, 0.5], ["15deg", "-15deg"]);
  const rotateY = useTransform(mouseXSpring, [-0.5, 0.5], ["-15deg", "15deg"]);

  const handleMouseMove = (e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const width = rect.width;
    const height = rect.height;
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    const xPct = mouseX / width - 0.5;
    const yPct = mouseY / height - 0.5;
    x.set(xPct);
    y.set(yPct);
  };

  const handleMouseLeave = () => {
    x.set(0);
    y.set(0);
  };

  const cardGradient = product.gradient || gradients[index % gradients.length];

  return (
    <motion.div
      layout
      initial={{ opacity: 0.4, scale: 0.8 }}
      whileInView={{ opacity: 1, scale: 1 }}
      viewport={{ amount: 0.6, margin: "0px" }}
      exit={{ opacity: 0, scale: 0.8, transition: { duration: 0.2 } }}
      transition={{ duration: 0.4, ease: "easeOut" }}
      className="shrink-0 snap-center w-[75vw] sm:w-[250px] md:w-[280px] perspective-[1000px] py-10"
    >
      <motion.div
        onMouseMove={handleMouseMove}
        onMouseLeave={handleMouseLeave}
        style={{
          rotateX,
          rotateY,
          transformStyle: "preserve-3d",
        }}
        className={`relative w-full h-[280px] sm:h-[350px] md:h-[400px] lg:h-[420px] rounded-3xl bg-gradient-to-br ${cardGradient} p-[1px] group cursor-pointer`}
      >
        {/* Glow border on hover */}
        <div className="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl blur-md" />

        {/* Card Content */}
        <div className="relative w-full h-full bg-[#0a0a0f]/80 backdrop-blur-2xl rounded-3xl p-5 md:p-6 flex flex-col justify-between overflow-hidden border border-white/5 group-hover:border-white/20 transition-colors duration-500">
          {/* Animated Background Particles inside card */}
          <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none">
            <div className="absolute top-10 left-10 w-24 h-24 bg-blue-500/20 rounded-full blur-[30px] animate-pulse" />
            <div className="absolute bottom-10 right-10 w-24 h-24 bg-orange-500/20 rounded-full blur-[30px] animate-pulse delay-150" />
          </div>

          <div style={{ transform: "translateZ(50px)" }}>
            <div className="h-16 sm:h-20 md:h-24 mb-4 lg:mb-6 flex items-center justify-center">
              <img
                src={product.image || product.logo}
                alt={product.name || product.title}
                className="h-full w-auto max-w-[85%] object-contain object-left filter drop-shadow-[0_0_20px_rgba(255,255,255,0.5)]"
              />
            </div>

            <h3 className="text-lg sm:text-xl md:text-2xl font-bold text-white mb-2 tracking-wide">
              {product.name || product.title}
            </h3>
            <p className="text-gray-400 text-[11px] sm:text-xs md:text-sm leading-relaxed mb-4 font-medium line-clamp-2">
              {product.description}
            </p>
          </div>

          <div style={{ transform: "translateZ(40px)" }} className="mt-auto">
            {product.tags && product.tags.length > 0 && (
              <div className="flex flex-wrap h-[26px] md:h-[30px] overflow-hidden gap-1.5 mb-4 md:mb-5">
                {product.tags.map((tag) => (
                  <span
                    key={tag}
                    className="text-[8px] sm:text-[9px] md:text-[10px] uppercase tracking-wider font-bold px-2 py-1 rounded-full bg-white/5 text-white/70 border border-white/10"
                  >
                    {tag}
                  </span>
                ))}
              </div>
            )}

            {/* CTA Button */}
            <div className="rounded-xl">
              <Link
                href={`/our-products/${product.id}`}
                className="block rounded-xl"
              >
                <motion.button className="w-full py-2.5 md:py-3 bg-gradient-to-r from-[#FF8A00] to-[#D98B50] text-white font-bold text-[10px] sm:text-xs md:text-sm tracking-widest uppercase flex items-center justify-center gap-2 rounded-xl transition-all duration-300 hover:brightness-110 hover:scale-[1.02] shadow-[0_0_20px_rgba(255,138,0,0.3)]">
                  {t("viewProject")}
                  <svg
                    className={`w-3.5 h-3.5 md:w-4 md:h-4 transform transition-transform duration-300 ${language === "ar" ? "rotate-180" : ""
                      }`}
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M14 5l7 7m0 0l-7 7m7-7H3"
                    />
                  </svg>
                </motion.button>
              </Link>
            </div>
          </div>
        </div>
      </motion.div>
    </motion.div>
  );
};

const OnlineWebsite = () => {
  const { t, language } = useLanguage();
  const [activeTab, setActiveTab] = useState("all");
  const sectionRef = useRef(null);
  const sliderRef = useRef(null);
  const [mounted, setMounted] = useState(false);  const [stars, setStars] = useState([]);

  // Pagination & Loading States
  const [productsList, setProductsList] = useState([]);
  const [categories, setCategories] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [isLoadingMore, setIsLoadingMore] = useState(false);

  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(true);

  const checkScroll = () => {
    if (sliderRef.current) {
      const { scrollLeft, scrollWidth, clientWidth } = sliderRef.current;
      const maxScroll = scrollWidth - clientWidth;
      
      if (maxScroll <= 10) {
        setCanScrollLeft(false);
        setCanScrollRight(false);
        return;
      }

      const isRtl = language === "ar";
      const currentScroll = Math.abs(scrollLeft);
      const atStart = currentScroll <= 10;
      const atEnd = currentScroll >= maxScroll - 10;

      if (isRtl) {
        setCanScrollLeft(!atEnd);
        setCanScrollRight(!atStart);
      } else {
        setCanScrollLeft(!atStart);
        setCanScrollRight(!atEnd);
      }
    }
  };

  useEffect(() => {
    if (!isLoading && productsList.length > 0) {
      setTimeout(checkScroll, 100);
    }
  }, [productsList, isLoading, language]);

  // Fetch categories dynamically on mount
  useEffect(() => {
    // Generate stars client-side only to avoid SSR hydration mismatch
    const generatedStars = Array.from({ length: 40 }).map((_, i) => ({
      id: i,
      width: Math.random() * 3 + 1 + "px",
      height: Math.random() * 3 + 1 + "px",
      top: Math.random() * 100 + "%",
      left: Math.random() * 100 + "%",
      opacity: Math.random() * 0.5 + 0.1,
      duration: Math.random() * 5 + 5,
    }));

    Promise.resolve().then(() => {
      setMounted(true);
      setStars(generatedStars);
    });

    fetchAPI(`/products/categories?lang=${language}`)
      .then((data) => {
        if (data) {
          setCategories([
            { id: "all", label: language === "ar" ? "الكل" : "All" },
            ...getApiItems(data),
          ]);
        }
      })
      .catch((err) => {
        console.error("Failed to fetch product categories:", err);
      });
  }, [language]);

  // Fetch products dynamically when tab, page, or language changes
  useEffect(() => {
    let isMounted = true;

    Promise.resolve().then(() => {
      if (!isMounted) return;
      if (currentPage === 1) {
        setIsLoading(true);
      } else {
        setIsLoadingMore(true);
      }
    });

    const categoryQuery = activeTab === "all" ? "" : activeTab;

    fetchAPI(
      `/products?lang=${language}&category=${categoryQuery}&page=${currentPage}&per_page=3`,
    )
      .then((res) => {
        if (!isMounted) return;

        const page = getApiPage(res);
        if (res) {
          if (currentPage === 1) {
            setProductsList(page.items);
          } else {
            setProductsList((prev) => {
              const newProducts = page.items.filter(
                (item) => !prev.some((p) => p.id === item.id),
              );
              return [...prev, ...newProducts];
            });
          }
          setHasMore(page.hasMore);
        } else {
          if (currentPage === 1) {
            setProductsList([]);
          }
          setHasMore(false);
        }
      })
      .catch((err) => {
        console.error("Failed to fetch products:", err);
        if (currentPage === 1 && isMounted) {
          setProductsList([]);
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
  }, [activeTab, currentPage, language]);

  const handleTabChange = (tabId) => {
    if (activeTab === tabId) return;
    setActiveTab(tabId);
    setCurrentPage(1);
    setProductsList([]); // Clear previous list immediately to trigger skeleton
  };

  const scrollLeft = () => {
    if (sliderRef.current) {
      sliderRef.current.scrollBy({ left: -320, behavior: "smooth" });
      setTimeout(checkScroll, 300);
    }
  };

  const scrollRight = () => {
    if (sliderRef.current) {
      sliderRef.current.scrollBy({ left: 320, behavior: "smooth" });
      setTimeout(checkScroll, 300);
    }
  };

  if (!mounted) return null;

  return (
    <section
      ref={sectionRef}
      className="relative w-full min-h-screen overflow-hidden flex flex-col lg:flex-row items-center lg:py-10 px-6 md:px-16 lg:px-24 gap-12"
    >
      {/* Background Visuals */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden -z-10">
        {/* Nebula Glows */}
        <div className="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] bg-blue-900/10 blur-[150px] rounded-full mix-blend-screen" />
        <div className="absolute bottom-[-10%] right-[-10%] w-[40vw] h-[40vw] bg-[#FF8A00]/10 blur-[150px] rounded-full mix-blend-screen" />

        {/* Floating Stars */}
        {stars.map((star) => (
          <motion.div
            key={star.id}
            className="absolute bg-white rounded-full"
            style={{
              width: star.width,
              height: star.height,
              top: star.top,
              left: star.left,
              opacity: star.opacity,
            }}
            animate={{
              y: [0, -30, 0],
              opacity: [0.1, 0.6, 0.1],
            }}
            transition={{
              duration: star.duration,
              repeat: Infinity,
              ease: "easeInOut",
            }}
          />
        ))}
      </div>

      {/* Left Content: Title & Tabs */}
      <div className="w-full lg:w-[35%] z-20 flex flex-col pt-10 lg:pt-0 shrink-0">
        <motion.div
          initial={{ opacity: 0, x: -50 }}
          whileInView={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.8 }}
          viewport={{ once: true }}
          className="mb-12"
        >
          <h2 className="text-3xl sm:text-4xl md:text-4xl lg:text-5xl font-black mb-4 leading-[1.1] tracking-tight flex gap-2">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-500">
              {t("ourProductsTitlePart1")}
            </span>
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#FF8A00] to-[#D98B50] drop-shadow-[0_0_30px_rgba(255,138,0,0.2)]">
              {t("ourProductsTitlePart2")}
            </span>
          </h2>
          <p className="text-gray-400 text-base sm:text-lg md:text-xl font-medium max-w-md">
            {t("ourProductsDesc")}
          </p>
        </motion.div>

        {/* Categories / Tabs */}
        <div className="flex flex-wrap justify-center sm:grid sm:grid-cols-3 lg:flex lg:flex-col gap-2 sm:gap-3 lg:gap-1.5 w-full px-2 lg:px-0">
          {categories.map((cat, index) => (
            <motion.button
              key={cat.id}
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.05, duration: 0.4 }}
              viewport={{ once: true }}
              onClick={() => handleTabChange(cat.id)}
              className={`relative px-3 py-2 sm:px-4 sm:py-3 lg:px-3 lg:py-2.5 text-center lg:text-left whitespace-nowrap rounded-xl lg:rounded-2xl transition-all duration-300 flex items-center justify-center lg:justify-start group overflow-hidden z-10 ${activeTab === cat.id
                ? "text-white font-bold bg-transparent lg:bg-white/10 border border-transparent lg:border-white/20 shadow-none lg:shadow-[0_0_30px_rgba(255,255,255,0.05)] lg:backdrop-blur-md"
                : "text-gray-400 lg:text-gray-500 font-medium bg-white/[0.03] lg:bg-transparent border border-white/[0.08] lg:border-transparent hover:text-white hover:bg-white/5"
                }`}
            >
              {activeTab === cat.id && (
                <>
                  {/* Desktop active vertical indicator (with RTL support) */}
                  <motion.div
                    layoutId="activeTabIndicatorVertical"
                    className={`absolute top-0 bottom-0 w-1.5 bg-gradient-to-b from-[#FF8A00] to-[#D98B50] shadow-[0_0_15px_#FF8A00] hidden lg:block ${language === "ar"
                      ? "right-0 rounded-r-2xl"
                      : "left-0 rounded-l-2xl"
                      }`}
                  />
                  {/* Mobile active background slider */}
                  <motion.div
                    layoutId="activeTabBackgroundMobile"
                    className="absolute inset-0 bg-gradient-to-r from-[#FF8A00] to-[#D98B50] rounded-xl -z-10 lg:hidden shadow-[0_0_20px_rgba(255,138,0,0.3)]"
                    transition={{ type: "spring", stiffness: 300, damping: 25 }}
                  />
                </>
              )}
              <span className="relative z-10 text-[11px] sm:text-sm md:text-base lg:text-lg mr-0 lg:mr-5">
                {cat.label}
              </span>

              {/* Hover highlight */}
              <div className="absolute inset-0 bg-gradient-to-r from-white/0 via-white/5 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-in-out pointer-events-none" />
            </motion.button>
          ))}
        </div>
      </div>

      {/* Right Content: Staggered Cards Slider */}
      <div className="w-full lg:pt-20 lg:w-[65%] z-20 relative flex items-center group/slider">
        {/* Navigation Arrows */}
        <button
          onClick={scrollLeft}
          disabled={!canScrollLeft}
          className={`absolute left-2 z-30 w-10 h-10 md:w-12 md:h-12 hidden md:flex items-center justify-center rounded-full bg-[#0a0a0f]/80 backdrop-blur-md border border-white/10 text-white transition-all duration-300 shadow-[0_0_20px_rgba(0,0,0,0.5)] ${
            canScrollLeft
              ? "opacity-100 hover:bg-[#FF8A00] hover:border-[#FF8A00] hover:scale-110 cursor-pointer"
              : "opacity-30 cursor-not-allowed"
          }`}
          aria-label="Scroll Left"
        >
          <svg
            className="w-5 h-5 md:w-6 md:h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M15 19l-7-7 7-7"
            />
          </svg>
        </button>

        <button
          onClick={scrollRight}
          disabled={!canScrollRight}
          className={`absolute right-2 lg:-right-6 z-30 w-10 h-10 md:w-12 md:h-12 hidden md:flex items-center justify-center rounded-full bg-[#0a0a0f]/80 backdrop-blur-md border border-white/10 text-white transition-all duration-300 shadow-[0_0_20px_rgba(0,0,0,0.5)] ${
            canScrollRight
              ? "opacity-100 hover:bg-[#FF8A00] hover:border-[#FF8A00] hover:scale-110 cursor-pointer"
              : "opacity-30 cursor-not-allowed"
          }`}
          aria-label="Scroll Right"
        >
          <svg
            className="w-5 h-5 md:w-6 md:h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M9 5l7 7-7 7"
            />
          </svg>
        </button>

        <div
          className="w-full flex items-center"
          style={{
            WebkitMaskImage:
              "linear-gradient(to right, transparent, black 10%, black 90%, transparent)",
            maskImage:
              "linear-gradient(to right, transparent, black 10%, black 90%, transparent)",
          }}
        >
          <div
            ref={sliderRef}
            onScroll={checkScroll}
            className="w-full overflow-x-auto pb-16  scrollbar-hide snap-x snap-mandatory flex gap-8"
            style={{ msOverflowStyle: "none", scrollbarWidth: "none" }}
          >
            {/* Empty spacer for horizontal scroll start padding */}
            <div className="w-8 lg:w-16 shrink-0" />

            <AnimatePresence mode="popLayout">
              {isLoading ? (
                // Initial page loading skeletons (3 cards)
                [1, 2, 3].map((i) => (
                  <div
                    key={`skeleton-init-${i}`}
                    className="shrink-0 snap-center w-[75vw] sm:w-[250px] md:w-[280px] py-10"
                  >
                    <div className="relative w-full h-[280px] sm:h-[350px] md:h-[400px] lg:h-[420px] rounded-3xl bg-white/5 border border-white/10 animate-pulse" />
                  </div>
                ))
              ) : productsList.length > 0 ? (
                <>
                  {productsList.map((product, index) => (
                    <ProductCard
                      key={product.id}
                      product={product}
                      index={index}
                    />
                  ))}

                  {/* Skeletons while loading more */}
                  {isLoadingMore &&
                    [1, 2].map((i) => (
                      <div
                        key={`skeleton-more-${i}`}
                        className="shrink-0 snap-center w-[75vw] sm:w-[250px] md:w-[280px] py-10"
                      >
                        <div className="relative w-full h-[280px] sm:h-[350px] md:h-[400px] lg:h-[420px] rounded-3xl bg-white/5 border border-white/10 animate-pulse" />
                      </div>
                    ))}

                  {/* Gorgeous glowing "Load More" Card */}
                  {hasMore && !isLoadingMore && (
                    <motion.div
                      initial={{ opacity: 0, scale: 0.8 }}
                      animate={{ opacity: 1, scale: 1 }}
                      exit={{ opacity: 0, scale: 0.8 }}
                      transition={{ duration: 0.4 }}
                      className="shrink-0 snap-center w-[250px] md:w-[280px] perspective-[1000px] py-10"
                    >
                      <div
                        onClick={() => setCurrentPage((prev) => prev + 1)}
                        className="relative w-full h-[280px] sm:h-[350px] md:h-[400px] lg:h-[420px] rounded-3xl bg-gradient-to-br from-orange-600/20 to-red-900/20 p-[1px] group cursor-pointer"
                      >
                        <div className="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl blur-md" />
                        <div className="relative w-full h-full bg-[#0a0a0f]/80 backdrop-blur-2xl rounded-3xl p-6 flex flex-col items-center justify-center border border-white/5 group-hover:border-[#FF8A00]/30 transition-all duration-500 text-center">
                          <div className="w-16 h-16 rounded-full bg-[#FF8A00]/10 border border-[#FF8A00]/30 flex items-center justify-center mb-6 group-hover:bg-[#FF8A00] group-hover:text-white group-hover:scale-110 transition-all duration-500 shadow-[0_0_20px_rgba(255,138,0,0.2)]">
                            <svg
                              className="w-8 h-8 text-[#FF8A00] group-hover:text-white transition-colors"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2.5}
                                d="M12 4v16m8-8H4"
                              />
                            </svg>
                          </div>
                          <h3 className="text-xl font-bold text-white mb-2 group-hover:text-[#FF8A00] transition-colors uppercase tracking-wide">
                            {t("loadMore")}
                          </h3>
                          <p className="text-gray-400 text-xs leading-relaxed max-w-[180px]">
                            {language === "ar"
                              ? "اكتشف المزيد من المنتجات والمشاريع التقنية المميزة"
                              : "Discover more premium tech products and solutions"}
                          </p>
                        </div>
                      </div>
                    </motion.div>
                  )}
                </>
              ) : (
                <div className="col-span-full py-16 flex flex-col items-center justify-center text-center w-full min-w-[300px]">
                  <svg
                    className="w-12 h-12 text-gray-500 mb-4 opacity-50"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={1.5}
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    />
                  </svg>
                  <p className="text-gray-400 font-medium">
                    {language === "ar"
                      ? "لا توجد منتجات في هذا القسم حالياً"
                      : "No products found in this category"}
                  </p>
                </div>
              )}
            </AnimatePresence>

            {/* Empty spacer for horizontal scroll end padding */}
            <div className="w-8 lg:w-16 shrink-0" />
          </div>
        </div>
      </div>

      {/* Custom styles to hide scrollbar for webkit */}
      <style
        dangerouslySetInnerHTML={{
          __html: `
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
      `,
        }}
      />
    </section>
  );
};

export default OnlineWebsite;
