"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";

// Client-side cache store to make tab/page switching instant
const blogCache = {};

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
    }
  },
  exit: {
    opacity: 0,
    scale: 0.96,
    transition: { duration: 0.2 }
  }
};

const BlogCard = ({ post, t, language, categoryMap }) => {
  const [isImageLoaded, setIsImageLoaded] = useState(false);

  return (
    <motion.div
      layout
      variants={itemVariants}
    >
      <Link href={`/blog/${post.slug || post.id}`} prefetch={false} className="group cursor-pointer flex flex-col h-full">
        <div className="relative w-full h-[220px] rounded-lg overflow-hidden mb-4 bg-gray-800/50 border border-white/5">
          {!isImageLoaded && (
            <div className="absolute inset-0 bg-white/5 animate-pulse z-0" />
          )}
          <Image
            src={post.image || "/placeholder.webp"}
            alt={post.title}
            fill
            className={`object-cover transition-all duration-700 group-hover:scale-105 ${isImageLoaded ? "opacity-100 scale-100" : "opacity-0 scale-95"
              }`}
            onLoad={() => setIsImageLoaded(true)}
          />
        </div>
        <div className="flex items-center gap-2 mb-2">
          <div className="text-blue-400 text-xs font-semibold tracking-wider uppercase">
            {post.published_at}
          </div>
          <span className="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
          <div className="text-orange-400 text-xs font-semibold tracking-wider uppercase">
            {categoryMap[post.category_raw] || post.category || (language === 'ar' ? 'تكنولوجيا' : 'Technology')}
          </div>
        </div>
        <h3 className="text-lg font-bold mb-3 leading-snug group-hover:text-blue-400 transition-colors uppercase">
          {post.title}
        </h3>
        <div className="text-gray-400 text-sm line-clamp-3" dangerouslySetInnerHTML={{ __html: post.content }}>
        </div>
      </Link>
    </motion.div>
  );
};

const BlogGrid = () => {
  const { t, language } = useLanguage();
  const [activeTab, setActiveTab] = useState("ALL");
  const [categories, setCategories] = useState([]);
  const [categoryMap, setCategoryMap] = useState({});
  const [postsList, setPostsList] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [loading, setLoading] = useState(false);

  // Fetch categories dynamically
  useEffect(() => {
    fetchAPI(`/blog/categories?lang=${language}`)
      .then((data) => {
        if (data) {
          const allCat = { id: "ALL", label: language === "ar" ? "الكل" : "All" };
          setCategories([allCat, ...data]);

          const map = { ALL: language === "ar" ? "الكل" : "All" };
          data.forEach((cat) => {
            map[cat.id] = cat.label;
          });
          setCategoryMap(map);
        }
      })
      .catch(console.error);
  }, [language]);

  // Fetch posts dynamically when activeTab, currentPage, or language changes
  useEffect(() => {
    let isMounted = true;

    const categoryQuery = activeTab === "ALL" ? "ALL" : activeTab;
    const cacheKey = `${language}-${categoryQuery}-${currentPage}`;

    // Read from client-side cache first to avoid framework bootstrap delay
    if (blogCache[cacheKey]) {
      const cached = blogCache[cacheKey];
      if (currentPage === 1) {
        setPostsList(cached.data);
      } else {
        setPostsList((prev) => {
          const newPosts = cached.data.filter((post) => !prev.some((p) => p.id === post.id));
          return [...prev, ...newPosts];
        });
      }
      setHasMore(cached.hasMore);
      setLoading(false);
      return;
    }

    setLoading(true);

    fetchAPI(`/blog?lang=${language}&category_raw=${categoryQuery}&page=${currentPage}&per_page=9`)
      .then((res) => {
        if (!isMounted) return;

        if (res && Array.isArray(res.data)) {
          // Store in client-side cache
          blogCache[cacheKey] = {
            data: res.data,
            hasMore: res.has_more
          };

          if (currentPage === 1) {
            setPostsList(res.data);
          } else {
            setPostsList((prev) => {
              const newPosts = res.data.filter((post) => !prev.some((p) => p.id === post.id));
              return [...prev, ...newPosts];
            });
          }
          setHasMore(res.has_more);
        }
      })
      .catch(console.error)
      .finally(() => {
        if (isMounted) setLoading(false);
      });

    return () => {
      isMounted = false;
    };
  }, [activeTab, currentPage, language]);

  const handleTabChange = (cat) => {
    setActiveTab(cat);
    setCurrentPage(1);
    setPostsList([]); // Clear previous posts immediately to trigger skeleton loader
  };

  return (
    <section className="w-full max-w-7xl mx-auto px-6 py-12 text-white relative z-10">
      <div className="mb-8">
        <h2 className="text-2xl md:text-3xl font-bold mb-6 uppercase tracking-wide">
          {t("recentBlogPosts") || (language === 'ar' ? 'أحدث المقالات' : 'Recent Blog Posts')}
        </h2>

        {/* Tabs */}
        <div className="flex flex-wrap gap-3 mb-10">
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => handleTabChange(cat.id)}
              className={`px-6 py-2 rounded-full text-sm font-semibold transition-all duration-300 border ${activeTab === cat.id
                ? "bg-[#FF8A00] border-[#FF8A00] text-white"
                : "border-blue-500/50 text-gray-300 hover:border-blue-400 hover:text-white"
                }`}
            >
              {cat.label}
            </button>
          ))}
        </div>
      </div>

      {/* Grid */}
      <motion.div
        layout
        variants={containerVariants}
        initial="hidden"
        animate="show"
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12 min-h-[400px] content-start"
      >
        <AnimatePresence mode="popLayout">
          {postsList.length > 0 ? (
            <>
              {postsList.map((post) => (
                <BlogCard
                  key={post.id}
                  post={post}
                  t={t}
                  language={language}
                  categoryMap={categoryMap}
                />
              ))}
              {loading && [1, 2, 3].map((i) => (
                <motion.div
                  layout
                  variants={itemVariants}
                  key={`skeleton-more-${i}`}
                  className="flex flex-col h-full animate-pulse"
                >
                  <div className="w-full h-[220px] rounded-lg bg-gray-800/50 mb-4" />
                  <div className="w-1/3 h-4 bg-gray-800/50 rounded mb-2" />
                  <div className="w-3/4 h-6 bg-gray-800/50 rounded mb-3" />
                  <div className="w-full h-4 bg-gray-800/50 rounded mb-1" />
                  <div className="w-5/6 h-4 bg-gray-800/50 rounded" />
                </motion.div>
              ))}
            </>
          ) : loading ? (
            [1, 2, 3, 4, 5, 6].map((i) => (
              <motion.div
                variants={itemVariants}
                key={`skeleton-${i}`}
                className="flex flex-col h-full animate-pulse"
              >
                <div className="w-full h-[220px] rounded-lg bg-gray-800/50 mb-4" />
                <div className="w-1/3 h-4 bg-gray-800/50 rounded mb-2" />
                <div className="w-3/4 h-6 bg-gray-800/50 rounded mb-3" />
                <div className="w-full h-4 bg-gray-800/50 rounded mb-1" />
                <div className="w-5/6 h-4 bg-gray-800/50 rounded" />
              </motion.div>
            ))
          ) : (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="col-span-full flex flex-col items-center justify-center py-20 text-gray-400"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1" strokeLinecap="round" strokeLinejoin="round" className="mb-4 opacity-50">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                <path d="M3 9h18" />
                <path d="M9 21V9" />
              </svg>
              <p className="text-lg font-medium">{t("blogNoPosts") || (language === 'ar' ? 'لا توجد مقالات' : 'No posts found')}</p>
              <p className="text-sm">{t("blogCheckBack") || (language === 'ar' ? 'الرجاء التحقق لاحقاً' : 'Please check back later')}</p>
            </motion.div>
          )}
        </AnimatePresence>
      </motion.div>

      {/* Load More Button */}
      {hasMore && (
        <motion.div layout className="flex justify-center mt-8">
          <button
            onClick={() => setCurrentPage((prev) => prev + 1)}
            disabled={loading}
            className="px-8 py-3 rounded-full border border-gray-500 text-gray-300 hover:bg-white/10 transition-colors flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {loading ? (
              <>
                <span className="w-4 h-4   border-2 border-gray-300 border-t-transparent rounded-full animate-spin"></span>
                {t("loading")}
              </>
            ) : (
              <>
                {t("loadMore")}
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={`${language === 'ar' ? 'rotate-180' : ''}`}>
                  <path d="m9 18 6-6-6-6" />
                </svg>
              </>
            )}
          </button>
        </motion.div>
      )}
    </section>
  );
};

export default BlogGrid;

