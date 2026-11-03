"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import { getMediaUrl } from "@/lib/api";
import { htmlToText } from "@/lib/sanitize-html";

const SafeImage = ({ src, fallback, alt, className, ...props }) => {
  const [imgSrc, setImgSrc] = useState(src || fallback);
  
  useEffect(() => {
    setImgSrc(src || fallback);
  }, [src, fallback]);

  const isFallback = imgSrc === fallback;

  return (
    <Image
      src={imgSrc}
      alt={alt}
      className={`${className} ${isFallback ? "object-contain p-8 bg-gray-800" : "object-cover"}`}
      onError={() => setImgSrc(fallback)}
      {...props}
    />
  );
};

const BlogHero = ({ data, posts = [] }) => {
  const { t } = useLanguage();
  const settings = useSettings();
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : "/logo.svg";
  
  const featuredPost = posts[0] || null;
  const sidePosts = posts.slice(1, 4);

  return (
    <section className="w-full max-w-7xl mx-auto px-6 py-16 text-white relative z-10 mt-20">
      <div className="mb-8 overflow-hidden">
        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-3xl md:text-4xl font-bold mb-2 uppercase tracking-wide"
        >
          {data?.title || t("recentBlogPosts")}
        </motion.h1>
        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.15 }}
          className="text-gray-400 text-sm md:text-base"
        >
          {data?.subtitle || t("blogHeroDesc")}
        </motion.p>
      </div>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* Left Large Post */}
        {featuredPost ? (
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.3 }}
            className="w-full lg:w-3/5"
          >
            <Link href={`/blog/${featuredPost.slug || featuredPost.id}`} prefetch={false} className="w-full lg:w-full group cursor-pointer block">
              <div className="relative w-full h-[300px] md:h-[400px] rounded-lg overflow-hidden mb-6 border border-white/5 shadow-2xl group-hover:border-blue-500/30 transition-all duration-300 flex items-center justify-center">
                <SafeImage
                  src={featuredPost.image}
                  fallback={siteLogo}
                  alt={featuredPost.title}
                  fill
                  sizes="(max-width: 1024px) 100vw, 60vw"
                  className="transition-transform duration-700 ease-out group-hover:scale-105"
                  priority
                />
              </div>
              <div className="text-blue-400 text-sm font-semibold mb-3 tracking-wider uppercase flex items-center gap-2">
                <span className="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse" />
                {featuredPost.published_at}
              </div>
              <h3 className="text-2xl md:text-3xl font-bold mb-4 leading-tight group-hover:text-blue-400 transition-colors uppercase">
                {featuredPost.title}
              </h3>
              <div className="text-gray-400 line-clamp-3 text-sm md:text-base font-light">
                {htmlToText(featuredPost.content)}
              </div>
            </Link>
          </motion.div>
        ) : (
          <div className="w-full lg:w-3/5 bg-[#ffffff05] rounded-lg animate-pulse h-[400px]" />
        )}

        {/* Right Small Posts */}
        <div className="w-full lg:w-2/5 flex flex-col gap-6">
          {sidePosts.length > 0 ? (
            sidePosts.map((post, index) => (
              <motion.div
                key={post.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: 0.45 + index * 0.1 }}
              >
                <Link
                  href={`/blog/${post.slug || post.id}`}
                  prefetch={false}
                  className="flex gap-4 group cursor-pointer items-center"
                >
                  <div className="relative w-1/3 h-[100px] md:h-[120px] rounded-lg overflow-hidden shrink-0 border border-white/5 group-hover:border-blue-500/30 transition-all duration-300 flex items-center justify-center">
                    <SafeImage
                      src={post.image}
                      fallback={siteLogo}
                      alt={post.title}
                      fill
                      sizes="(max-width: 768px) 33vw, 150px"
                      className="transition-transform duration-700 ease-out group-hover:scale-105"
                    />
                  </div>
                  <div className="w-2/3">
                    <div className="text-blue-400 text-xs font-semibold mb-2 tracking-wider uppercase">
                      {post.published_at}
                    </div>
                    <h4 className="text-sm md:text-base font-bold mb-2 leading-tight group-hover:text-blue-400 transition-colors line-clamp-2 uppercase">
                      {post.title}
                    </h4>
                    <div className="text-gray-400 text-xs line-clamp-2 font-light">
                      {htmlToText(post.content)}
                    </div>
                  </div>
                </Link>
              </motion.div>
            ))
          ) : (
            [1, 2, 3].map(i => <div key={i} className="h-[100px] bg-[#ffffff05] rounded-lg animate-pulse" />)
          )}
        </div>
      </div>
    </section>
  );
};

export default BlogHero;
