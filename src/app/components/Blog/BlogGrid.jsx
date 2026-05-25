"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";

const BlogGrid = ({ posts = [] }) => {
  const { t, language } = useLanguage();
  const [activeTab, setActiveTab] = useState("ALL");

  const categoryTranslations = {
    en: {
      "ALL": "All",
      "TECHNOLOGY": "Technology",
      "WEB DEVELOPMENT": "Web Development",
      "STARTUP": "Startup",
      "DROP SHIPPING": "Drop Shipping",
      "HARDWARE": "Hardware",
      "SOFTWARE": "Software",
      "MARKETING": "Marketing",
      "DESIGN": "Design",
      "HOSTING": "Hosting",
      "SECURITY": "Security",
      "ARTIFICIAL INTELLIGENCE": "Artificial Intelligence",
    },
    ar: {
      "ALL": "الكل",
      "TECHNOLOGY": "تكنولوجيا",
      "WEB DEVELOPMENT": "تطوير الويب",
      "STARTUP": "شركات ناشئة",
      "DROP SHIPPING": "دروب شيبنج",
      "HARDWARE": "عتاد هاردوير",
      "SOFTWARE": "برمجيات",
      "MARKETING": "تسويق",
      "DESIGN": "تصميم",
      "HOSTING": "استضافة",
      "SECURITY": "حماية",
      "ARTIFICIAL INTELLIGENCE": "الذكاء الاصطناعي",
    }
  };

  // Build a map of raw category keys to their translated labels dynamically
  const categoryMap = {};
  const lang = language === "ar" ? "ar" : "en";
  const defaults = categoryTranslations[lang] || categoryTranslations.en;
  
  Object.keys(defaults).forEach(key => {
    categoryMap[key] = defaults[key];
  });
  
  // Add/overwrite custom categories from the posts
  posts.forEach(post => {
    if (post.category_raw && post.category) {
      if (!categoryMap[post.category_raw]) {
        categoryMap[post.category_raw] = post.category;
      }
    }
  });

  // Dynamic categories list for the tabs (always starts with ALL, followed by categories actually present in the posts)
  const categories = Array.from(
    new Set([
      "ALL",
      ...posts.map(post => post.category_raw).filter(Boolean)
    ])
  );

  const filteredPosts = activeTab === "ALL" 
    ? posts 
    : posts.filter(post => post.category_raw === activeTab);

  return (
    <section className="w-full max-w-7xl mx-auto px-6 py-12 text-white relative z-10">
      <div className="mb-8">
        <h2 className="text-2xl md:text-3xl font-bold mb-6 uppercase tracking-wide">
          {t("recentBlogPosts")}
        </h2>
        
        {/* Tabs */}
        <div className="flex flex-wrap gap-3 mb-10">
          {categories.map((cat) => (
            <button
              key={cat}
              onClick={() => setActiveTab(cat)}
              className={`px-6 py-2 rounded-full text-sm font-semibold transition-all duration-300 border ${
                activeTab === cat
                  ? "bg-[#FF8A00] border-[#FF8A00] text-white"
                  : "border-blue-500/50 text-gray-300 hover:border-blue-400 hover:text-white"
              }`}
            >
              {categoryMap[cat] || cat}
            </button>
          ))}
        </div>
      </div>

      {/* Grid */}
      <motion.div layout className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12 min-h-[400px] content-start">
        <AnimatePresence mode="popLayout">
          {filteredPosts.length > 0 ? (
            filteredPosts.map((post) => (
              <motion.div
                key={post.id}
                layout
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.9 }}
                transition={{ duration: 0.3 }}
              >
                <Link href={`/blog/${post.slug || post.id}`} className="group cursor-pointer flex flex-col h-full">
                  <div className="relative w-full h-[220px] rounded-lg overflow-hidden mb-4 bg-gray-800">
                    <Image
                      src={post.image || "/placeholder.png"}
                      alt={post.title}
                      fill
                      className="object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                  </div>
                  <div className="flex items-center gap-2 mb-2">
                    <div className="text-blue-400 text-xs font-semibold tracking-wider uppercase">
                      {post.published_at}
                    </div>
                    <span className="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
                    <div className="text-orange-400 text-xs font-semibold tracking-wider uppercase">
                      {post.category || (language === 'ar' ? 'تكنولوجيا' : 'Technology')}
                    </div>
                  </div>
                  <h3 className="text-lg font-bold mb-3 leading-snug group-hover:text-blue-400 transition-colors uppercase">
                    {post.title}
                  </h3>
                  <div className="text-gray-400 text-sm line-clamp-3" dangerouslySetInnerHTML={{ __html: post.content }}>
                  </div>
                </Link>
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
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <path d="M3 9h18"/>
                <path d="M9 21V9"/>
              </svg>
              <p className="text-lg font-medium">{t("blogNoPosts")}</p>
              <p className="text-sm">{t("blogCheckBack")}</p>
            </motion.div>
          )}
        </AnimatePresence>
      </motion.div>

      {/* Load More Button */}
      {filteredPosts.length > 0 && (
        <motion.div layout className="flex justify-center">
          <button className="px-8 py-3 rounded-full border border-gray-500 text-gray-300 hover:bg-white/10 transition-colors flex items-center gap-2 cursor-pointer">
            {t("loadMore")}
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={`${language === 'ar' ? 'rotate-180' : ''}`}>
              <path d="m9 18 6-6-6-6"/>
            </svg>
          </button>
        </motion.div>
      )}
    </section>
  );
};

export default BlogGrid;
