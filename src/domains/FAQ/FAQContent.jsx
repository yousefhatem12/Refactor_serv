"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Link from "next/link";
import { useLanguage } from "@/app/context/LanguageContext";
import { fetchAPI, getApiPage } from "@/lib/api";
import { executeWorkerTask } from "@/lib/worker-pool";

const FAQContent = ({ data }) => {
  const [openId, setOpenId] = useState(null);
  const [faqs, setFaqs] = useState([]);
  const [loading, setLoading] = useState(true);
  const { t, language } = useLanguage();

  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [isLoadingMore, setIsLoadingMore] = useState(false);

  const title =
    data?.title || t("faqTitle1") + " " + t("faqTitle2") + " " + t("faqTitle3");
  const description = data?.content || "";
  const heroImage = data?.hero_image || "/Hero/earth.webp";

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      const loadInitialFaqs = async () => {
        try {
          setLoading(true);
          setCurrentPage(1);
          const encodedSearch = searchQuery ? encodeURIComponent(searchQuery) : "";
          const res = await fetchAPI(`/faqs?lang=${language}&page=1&per_page=6&search=${encodedSearch}`);

          const page = getApiPage(res);
          if (page.items.length > 0 || res) {
            const formattedData = page.items.map((item, index) => ({
              ...item,
              displayId: (index + 1).toString().padStart(2, "0"),
            }));
            setFaqs(formattedData);
            setHasMore(page.hasMore);
            if (formattedData.length > 0) {
              setOpenId(formattedData[0].id);
            } else {
              setOpenId(null);
            }
          } else {
            setFaqs([]);
            setHasMore(false);
            setOpenId(null);
          }
        } catch (error) {
          console.error("Failed to fetch FAQs:", error);
          setFaqs([]);
          setHasMore(false);
          setOpenId(null);
        } finally {
          setLoading(false);
        }
      };

      loadInitialFaqs();
    }, 300);

    return () => clearTimeout(delayDebounceFn);
  }, [language, searchQuery]);

  const loadMoreFaqs = async () => {
    if (isLoadingMore || !hasMore) return;
    try {
      setIsLoadingMore(true);
      const nextPage = currentPage + 1;
      const encodedSearch = searchQuery ? encodeURIComponent(searchQuery) : "";
      const res = await fetchAPI(`/faqs?lang=${language}&page=${nextPage}&per_page=6&search=${encodedSearch}`);

      const page = getApiPage(res);
      if (page.items.length > 0 || res) {
        const startIdx = faqs.length;
        const formatted = page.items.map((item, index) => ({
          ...item,
          displayId: (startIdx + index + 1).toString().padStart(2, "0"),
        }));
        executeWorkerTask("DEDUPLICATE_ITEMS", { existing: faqs, incoming: formatted }).then((newFaqs) => {
          setFaqs((prev) => [...prev, ...newFaqs]);
        });
        setHasMore(page.hasMore);
        setCurrentPage(page.currentPage || nextPage);
      }
    } catch (error) {
      console.error("Failed to load more FAQs:", error);
    } finally {
      setIsLoadingMore(false);
    }
  };

  const toggleAccordion = (id) => {
    setOpenId(openId === id ? null : id);
  };

  return (
    <div className="max-w-[1400px] mx-auto px-6 md:px-10 relative mt-10 mb-32 min-h-screen">
      <div className="flex flex-col lg:flex-row relative">
        {/* Left Side: Text and Accordions */}
        <div className="w-full lg:w-[55%] z-20">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="mb-10"
          >
            <h1 className="text-4xl md:text-5xl lg:text-5xl font-bold tracking-widest leading-[1.2] text-white mb-4">
              {title}
            </h1>
            {description && (
              <p className="text-white/80 text-lg md:text-xl leading-relaxed max-w-2xl">
                {description}
              </p>
            )}
          </motion.div>

          {/* Search Input */}
          <div className="relative w-full mb-8 z-30">
            <span className={`absolute inset-y-0 ${language === "ar" ? "left-4" : "right-4"} flex items-center`}>
              <svg className="w-5 h-5 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
            <input
              type="text"
              aria-label={language === "ar" ? "ابحث عن الأسئلة الشائعة" : "Search frequently asked questions"}
              placeholder={language === "ar" ? "ابحث عن الأسئلة الشائعة..." : "Search frequently asked questions..."}
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className={`w-full px-5 py-4 bg-[#1a2650]/20 hover:bg-[#1a2650]/30 focus:bg-[#1a2650]/45 border border-white/15 focus:border-[#4B79A1] rounded-xl text-sm focus:outline-none transition-all text-white placeholder-white/30 backdrop-blur-md shadow-2xl ${language === "ar" ? "pl-12" : "pr-12"}`}
            />
          </div>

          <div className="flex flex-col gap-4">
            {loading ? (
              <div className="text-white text-center py-10 flex flex-col items-center justify-center gap-3">
                <div className="w-10 h-10 border-4 border-[#4B79A1] border-t-transparent rounded-full animate-spin"></div>
                <span>{language === 'ar' ? 'جاري التحميل...' : 'Loading FAQs...'}</span>
              </div>
            ) : faqs.length === 0 ? (
              <div className="text-white/50 text-center py-12 bg-[#05050f]/30 border border-white/5 rounded-xl">
                {language === 'ar' ? 'لا توجد نتائج مطابقة لبحثك.' : 'No FAQs match your search.'}
              </div>
            ) : (
              faqs.map((item) => {
                const isOpen = openId === item.id;
                return (
                  <motion.div
                    key={item.id}
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    className={`border ${isOpen ? "border-[#4B79A1]/60 bg-[#1a2650]/40 shadow-[0_0_20px_rgba(75,121,161,0.15)]" : "border-white/10 bg-[#05050f]/60 hover:border-white/20"} rounded-xl overflow-hidden backdrop-blur-md transition-all duration-300`}
                  >
                    <button
                      onClick={() => toggleAccordion(item.id)}
                      aria-expanded={isOpen}
                      aria-controls={`faq-ans-${item.id}`}
                      className="w-full px-6 py-5 flex items-center justify-between text-left focus:outline-none cursor-pointer"
                    >
                      <div className="flex items-center gap-6">
                        <span className="text-[#4B79A1] font-extrabold text-lg">
                          {item.displayId}
                        </span>
                        <span className="text-white font-bold text-base md:text-lg transition-colors duration-300 hover:text-[#4B79A1] text-start">
                          {item.question}
                        </span>
                      </div>
                      <div
                        className={`w-10 h-10 flex items-center justify-center rounded-lg shrink-0 transition-colors ${isOpen ? "bg-[#4B79A1]" : "bg-[#1a2650] hover:bg-[#294a73]"} `}
                      >
                        {isOpen ? (
                          <svg
                            width="14"
                            height="2"
                            viewBox="0 0 14 2"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path d="M0 1H14" stroke="white" strokeWidth="2" />
                          </svg>
                        ) : (
                          <svg
                            width="14"
                            height="14"
                            viewBox="0 0 14 14"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              d="M7 0V14M0 7H14"
                              stroke="white"
                              strokeWidth="2"
                            />
                          </svg>
                        )}
                      </div>
                    </button>

                    <AnimatePresence>
                      {isOpen && (
                        <motion.div
                          initial={{ height: 0, opacity: 0 }}
                          animate={{ height: "auto", opacity: 1 }}
                          exit={{ height: 0, opacity: 0 }}
                          transition={{ duration: 0.3 }}
                        >
                          <div className={`px-6 pb-6 ${language === 'ar' ? 'pr-[4.5rem]' : 'pl-[4.5rem]'} text-white/70 text-sm leading-relaxed space-y-4`}>
                            {(item.answer || "")
                              .split("\n")
                              .map((paragraph, idx) => (
                                <p key={idx} className="font-light">{paragraph}</p>
                              ))}
                          </div>
                        </motion.div>
                      )}
                    </AnimatePresence>
                  </motion.div>
                );
              })
            )}
          </div>

          {/* Load More Button */}
          {hasMore && (
            <div className="flex justify-center mt-8 w-full">
              <button
                onClick={loadMoreFaqs}
                disabled={isLoadingMore}
                className="flex items-center gap-2 px-8 py-3.5 bg-[#4B79A1] hover:bg-[#3d6385] disabled:bg-gray-700 text-white font-semibold rounded-lg transition-all duration-300 cursor-pointer shadow-[0_0_15px_rgba(75,121,161,0.3)] hover:shadow-[0_0_25px_rgba(75,121,161,0.55)] disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {isLoadingMore ? (
                  <svg className="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                  </svg>
                ) : null}
                <span>{isLoadingMore ? t("loading") : t("loadMore")}</span>
              </button>
            </div>
          )}

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3 }}
            className="mt-16 flex flex-col sm:flex-row items-center gap-6"
          >
            <h2 className="text-3xl font-bold text-white">
              {t("faqStillQuestions")}
            </h2>
            <Link href={`/${language}/contact-us`} prefetch={false}>
              <button className="px-8 py-3 bg-[#D98B50] hover:bg-[#c47c45] text-white font-semibold rounded-md transition-colors shadow-[0_0_15px_rgba(217,139,80,0.4)] cursor-pointer">
                {t("getInTouch2")}
              </button>
            </Link>
          </motion.div>
        </div>
      </div>

      {/* Right Side: Earth Image */}
      <div
        className={`absolute top-[5%] md:w-[500px] lg:w-[800px] h-auto z-10 pointer-events-none hidden md:block ${language === "ar"
          ? "md:left-[-25%] lg:left-[-25%] md:right-auto lg:right-auto"
          : "md:right-[-25%] lg:right-[-25%] md:left-auto lg:left-auto"
          }`}
      >
        <motion.img
          initial={{ opacity: 0, rotate: -10 }}
          animate={{ opacity: 1, rotate: 0 }}
          transition={{ duration: 1.5, ease: "easeOut" }}
          src={heroImage}
          alt="FAQ Illustration"
          className={`w-full h-full object-contain drop-shadow-[0_0_40px_rgba(59,130,246,0.3)] ${language === "ar" ? "scale-x-[-1]" : ""
            }`}
        />
      </div>
    </div>
  );
};

export default FAQContent;
