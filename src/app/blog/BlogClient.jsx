"use client";
import React, { useState, useEffect, useRef } from "react";
import { motion } from "framer-motion";
import BlogHero from "../components/Blog/BlogHero";
import BlogGrid from "../components/Blog/BlogGrid";
import Newsletter from "../components/Blog/Newsletter";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function BlogClient({ initialPageData, initialPosts = [], initialLang = "en" }) {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(initialPageData);
  const [posts, setPosts] = useState(initialPosts);
  const initialLangRef = useRef(initialLang);
  const isInitialMountRef = useRef(true);

  useEffect(() => {
    if (isInitialMountRef.current) {
      isInitialMountRef.current = false;
      if (initialPageData && language === initialLangRef.current) {
        return;
      }
    }

    let isMounted = true;
    Promise.all([
      fetchAPI(`/pages/blog?lang=${language}`).catch((err) => {
        console.error("Error fetching blog page data:", err);
        return null;
      }),
      fetchAPI(`/blog?lang=${language}&limit=4`).catch((err) => {
        console.error("Error fetching blog posts:", err);
        return null;
      }),
    ])
      .then(([page, blogPosts]) => {
        if (!isMounted) return;
        if (page) setPageData(page.data || page);
        if (blogPosts) setPosts(blogPosts.data || blogPosts);
      })
      .catch(console.error);

    return () => {
      isMounted = false;
    };
  }, [language, initialPageData, initialPosts]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      {/* Background Color Layer */}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      {/* Visual background glows */}
      <div className="absolute top-0 left-1/4 w-[500px] h-[500px] bg-[#FF8A00]/5 rounded-full filter blur-[120px] pointer-events-none z-0" />
      <div className="absolute top-1/2 right-1/4 w-[600px] h-[600px] bg-blue-500/5 rounded-full filter blur-[150px] pointer-events-none z-0" />

      <motion.div 
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.5 }}
        className="w-full relative z-10 flex flex-col items-center"
      >
        <BlogHero data={pageData} posts={posts} />
        <BlogGrid posts={posts} />
        <Newsletter />
      </motion.div>
    </main>
  );
}

