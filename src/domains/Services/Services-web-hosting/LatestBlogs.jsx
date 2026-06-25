"use client";
import React, { useState, useEffect, useRef } from "react";
import { motion } from "framer-motion";
import Link from "next/link";
import Image from "next/image";
import { useLanguage } from "@/app/context/LanguageContext";
import { fetchAPI } from "@/lib/api";

const LatestBlogs = () => {
  const { language, t } = useLanguage();
  const [blogs, setBlogs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeIndex, setActiveIndex] = useState(0);
  const sliderRef = useRef(null);

  const handleScroll = () => {
    if (sliderRef.current) {
      const scrollLeft = sliderRef.current.scrollLeft;
      const width = sliderRef.current.offsetWidth;
      const index = Math.round(scrollLeft / width);
      setActiveIndex(index);
    }
  };

  useEffect(() => {
    fetchAPI(`/blog?lang=${language}&limit=3`)
      .then((data) => {
        if (data && Array.isArray(data)) {
          setBlogs(data.slice(0, 3));
        }
      })
      .catch((err) => console.error("Error fetching latest blogs:", err))
      .finally(() => setLoading(false));
  }, [language]);

  if (loading) {
    return (
      <div className="py-24 text-center">
        <div className="w-10 h-10 border-2 border-[#D98B50] border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>
    );
  }

  if (blogs.length === 0) return null;

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-16">
          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-white text-3xl md:text-5xl font-bold mb-6 tracking-wide uppercase"
          >
            {t("latestBlogsTitle")}
          </motion.h2>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-gray-400 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed"
          >
            {t("latestBlogsDesc")}
          </motion.p>
        </div>

        <div
          ref={sliderRef}
          onScroll={handleScroll}
          className="flex md:grid md:grid-cols-3 gap-8 overflow-x-auto md:overflow-x-visible scrollbar-hide snap-x snap-mandatory scroll-smooth flex-nowrap pb-6"
          style={{ msOverflowStyle: "none", scrollbarWidth: "none" }}
          dir="ltr"
        >
          {blogs.map((blog, index) => (
            <motion.div
              key={blog.id}
              initial={{ opacity: 0, y: 40 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.2, duration: 0.6 }}
              className="group cursor-pointer rounded-3xl overflow-hidden relative shrink-0 snap-center w-[85vw] md:w-auto"
            >
              <Link href={`/blog/${blog.id}`} prefetch={false} className="block relative h-[450px] w-full">
                {/* Background Image */}
                <Image
                  src={blog.image || "/placeholder.webp"}
                  alt={blog.title}
                  fill
                  sizes="(max-width: 768px) 100vw, 33vw"
                  className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />

                {/* Overlay gradient so text is readable */}
                <div className="absolute inset-0 bg-gradient-to-t from-[#020617] via-[#020617]/80 to-transparent opacity-90 group-hover:opacity-100 transition-opacity duration-300"></div>

                {/* Content at bottom */}
                <div 
                  className="absolute bottom-0 left-0 w-full p-8 translate-y-4 group-hover:translate-y-0 transition-transform duration-500 text-left rtl:text-right"
                  dir={language === "ar" ? "rtl" : "ltr"}
                >
                  <h3 className="text-white text-2xl font-bold mb-3">
                    {blog.title}
                  </h3>
                  <div 
                    className="text-gray-300 text-sm leading-relaxed mb-4 line-clamp-3"
                    dangerouslySetInnerHTML={{ __html: blog.content }}
                  />
                  <span
                    className="inline-block text-[#D98B50] font-semibold text-sm hover:text-orange-400 transition-colors"
                  >
                    {t("readMore")}
                  </span>
                </div>
              </Link>
            </motion.div>
          ))}
        </div>

        {blogs.length > 1 && (
          <div className="flex justify-center items-center gap-2 mt-12 md:hidden">
            {blogs.map((_, index) => (
              <button
                key={index}
                onClick={() => {
                  if (sliderRef.current) {
                    const width = sliderRef.current.offsetWidth;
                    sliderRef.current.scrollTo({
                      left: index * width,
                      behavior: "smooth",
                    });
                  }
                }}
                className={`transition-all duration-300 rounded-full border-none cursor-pointer ${
                  activeIndex === index
                    ? "w-12 h-1 bg-[#D98B50]"
                    : "w-2 h-2 bg-gray-600 hover:bg-gray-500"
                }`}
                aria-label={`Go to slide ${index + 1}`}
              />
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export default LatestBlogs;
