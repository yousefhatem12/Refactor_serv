"use client";
import React, { useState, useEffect } from "react";
import BlogHero from "../components/Blog/BlogHero";
import BlogGrid from "../components/Blog/BlogGrid";
import Newsletter from "../components/Blog/Newsletter";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function BlogPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);
  const [posts, setPosts] = useState([]);

  useEffect(() => {
    fetchAPI(`/pages/blog?lang=${language}`).then(data => {
      if (data) setPageData(data);
    }).catch(console.error);

    fetchAPI(`/blog?lang=${language}&limit=4`).then(data => {
      if (data) setPosts(data);
    }).catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      {/* Background Color Layer */}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      <div className="w-full relative z-10 flex flex-col items-center">
        <BlogHero data={pageData} posts={posts} />
        <BlogGrid posts={posts} />
        <Newsletter />
      </div>
    </main>
  );
}
