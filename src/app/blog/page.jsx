"use client";
import React, { useState, useEffect } from "react";
import BlogHero from "../components/Blog/BlogHero";
import BlogGrid from "../components/Blog/BlogGrid";
import Newsletter from "../components/Blog/Newsletter";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import Loading from "../loading";

export default function BlogPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    Promise.all([
      fetchAPI(`/pages/blog?lang=${language}`).catch((err) => {
        console.error("Error fetching blog page data:", err);
        return null;
      }),
      fetchAPI(`/blog?lang=${language}&limit=4`).catch((err) => {
        console.error("Error fetching blog posts:", err);
        return null;
      })
    ])
      .then(([page, blogPosts]) => {
        if (page) setPageData(page);
        if (blogPosts) setPosts(blogPosts);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  }, [language]);

  if (loading || !pageData) {
    return <Loading />;
  }

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
