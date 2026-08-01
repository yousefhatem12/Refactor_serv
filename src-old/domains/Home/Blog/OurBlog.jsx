"use client";
import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";

const OurBlog = () => {
  const { language, t } = useLanguage();
  const [showAllPosts, setShowAllPosts] = useState(false);

  const [blogPosts, setBlogPosts] = useState({
    featured: null,
    gridPosts: [],
  });
  const [trendingItems, setTrendingItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchBlog = async () => {
      try {
        setIsLoading(true);
        const [postsData, trendingData] = await Promise.all([
          fetchAPI(`/blog?lang=${language}&limit=5`),
          fetchAPI(`/blog/trending?lang=${language}`),
        ]);

        if (postsData && postsData.length > 0) {
          setBlogPosts({
            featured: postsData[0],
            gridPosts: postsData.slice(1, 5),
          });
        }

        if (trendingData) {
          setTrendingItems(trendingData);
        }
      } catch (error) {
        console.error("Failed to fetch blog data:", error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchBlog();
  }, [language]);

  return (
    <section className="py-10 md:py-20 bg-transparent text-white relative z-0">
      <div className="max-w-[1400px] mx-auto px-5">
        <motion.div
          className="text-center mb-[60px]"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
        >
          <h2 className="text-[2.2rem] md:text-[2.8rem] lg:text-[3.5rem] font-black text-white relative inline-block after:content-[''] after:absolute after:-bottom-2.5 after:left-0 after:w-[40%] after:h-[6px] after:bg-[#ff8a00] after:rounded-[10px] after:shadow-[0_0_15px_rgba(255,138,0,0.5)]">
            <span className="text-[#ff8a00]">{t("our")}</span> {t("blog")}
          </h2>
        </motion.div>

        <div className="relative">
          <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-[1.6fr_2.4fr_1fr] gap-[40px] items-start">
            {/* Featured Post */}
            {blogPosts.featured && (
              <motion.div
                className="flex flex-col"
                initial={{ opacity: 0, x: -30 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.8 }}
              >
                <div className="relative rounded-xl  overflow-hidden mb-5 bg-white/5 group aspect-[16/10]">
                  <img
                    src={
                      blogPosts.featured.image ||
                      "/our-blog/Link → post-landscape-1.jpg.webp"
                    }
                    alt={blogPosts.featured.title}
                    className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                  />
                </div>
                <div className="flex items-center gap-2.5 mb-3 text-[0.85rem] font-bold tracking-[1px]">
                  <span className="text-[#a0a0a0]">
                    {blogPosts.featured.category || t("news")}
                  </span>
                  <span className="w-1 h-1 bg-[#ff8a00] rounded-full"></span>
                  <span className="text-[#666]">
                    {blogPosts.featured.published_at}
                  </span>
                </div>
                <h3 className="font-extrabold leading-[1.3] mb-[15px] text-white transition-colors duration-300 hover:text-[#ff8a00] text-[1.4rem] md:text-[2rem]">
                  {blogPosts.featured.title}
                </h3>
                <p className="text-[#a0a0a0] text-[0.95rem] leading-[1.6] mb-5 line-clamp-4">
                  {blogPosts.featured.content
                    .replace(/<[^>]*>?/gm, "")
                    .substring(0, 300)}
                  ...
                </p>
                <a
                  href={`/blog/${blogPosts.featured.id}`}
                  className="text-[#ff8a00] text-[0.8rem] font-extrabold underline underline-offset-4 tracking-[0.5px]"
                >
                  {t("seeMore")}
                </a>
              </motion.div>
            )}
            {/* لابلابللب */}
            {/* Grid Posts Area */}
            <div
              className={`grid grid-cols-1 md:grid-cols-2 gap-[30px] relative ${!showAllPosts ? "max-md:max-h-[300px] max-md:overflow-hidden max-md:rounded-b-[25px]" : ""}`}
            >
              {blogPosts.gridPosts.map((post, index) => (
                <motion.div
                  key={post.id}
                  className={`flex flex-col ${!showAllPosts && index > 0 ? "max-md:hidden" : ""}`}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                >
                  <div className="relative rounded-xl overflow-hidden mb-5 bg-white/5 group aspect-[16/11]">
                    <img
                      src={
                        post.image ||
                        "/our-blog/Link → post-landscape-2.jpg.webp"
                      }
                      alt={post.title}
                      className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                    />
                  </div>
                  <div className="flex items-center gap-2.5 mb-3 text-[0.85rem] font-bold tracking-[1px]">
                    <span className="text-[#a0a0a0]">
                      {post.category || t("news")}
                    </span>
                    <span className="w-1 h-1 bg-[#ff8a00] rounded-full"></span>
                    <span className="text-[#666]">{post.published_at}</span>
                  </div>
                  <h3 className="font-extrabold leading-[1.3] mb-[15px] text-white transition-colors duration-300 hover:text-[#ff8a00] text-[1.1rem]">
                    {post.title}
                  </h3>
                  <a
                    href={`/blog/${post.id}`}
                    className="text-[#ff8a00] text-[0.8rem] font-extrabold underline underline-offset-4 tracking-[0.5px]"
                  >
                    {t("seeMore")}
                  </a>
                </motion.div>
              ))}

              {!showAllPosts && (
                <div className="hidden max-md:flex absolute top-0 left-0 w-full h-full bg-gradient-to-t from-[#0ea5e9]/90 via-[#0ea5e9]/40 to-transparent backdrop-blur-[2px] justify-center items-end pb-[20px] z-10 pointer-events-none rounded-b-[25px]">
                  <button
                    className="bg-[#ff8a00] text-white border-none py-3 px-[35px] text-[1rem] font-bold rounded-[30px] cursor-pointer shadow-[0_4px_15px_rgba(255,138,0,0.4)] transition-all duration-300 ease-in-out uppercase tracking-[1px] pointer-events-auto hover:bg-[#e07a00] hover:-translate-y-[2px] hover:shadow-[0_6px_20px_rgba(255,138,0,0.6)]"
                    onClick={() => setShowAllPosts(true)}
                  >
                    {t("showMore")}
                  </button>
                </div>
              )}
            </div>

            {/* Trending Sidebar */}
            <motion.aside
              className={`border border-white/10 p-[30px] rounded h-full flex flex-col lg:col-span-2 xl:col-span-1 ${!showAllPosts ? "max-md:hidden" : ""}`}
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
            >
              <div className="flex justify-between items-center mb-[30px] pb-[15px] border-b border-white/10">
                <h3 className="text-[1.5rem] font-bold">{t("trending")}</h3>
                <div className="flex gap-3 text-[#a0a0a0] text-[0.9rem]">
                  <svg
                    className="cursor-pointer transition-colors duration-300 hover:text-[#ff8a00]"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                  </svg>
                  <svg
                    className="cursor-pointer transition-colors duration-300 hover:text-[#ff8a00]"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  >
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                  </svg>
                  <svg
                    className="cursor-pointer transition-colors duration-300 hover:text-[#ff8a00]"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" />
                  </svg>
                  <svg
                    className="cursor-pointer transition-colors duration-300 hover:text-[#ff8a00]"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                    <rect x="2" y="9" width="4" height="12" />
                    <circle cx="4" cy="4" r="2" />
                  </svg>
                </div>
              </div>

              <div className="flex flex-col gap-[25px] flex-grow">
                {trendingItems.map((item) => (
                  <div
                    key={item.id}
                    className="pb-[20px] border-b border-white/5 last:border-b-0"
                  >
                    <a href={`/blog/${item.id}`}>
                      <h4 className="text-[1rem] font-semibold leading-[1.4] mb-2 text-[#e0e0e0] cursor-pointer transition-colors duration-300 hover:text-[#ff8a00]">
                        {item.title}
                      </h4>
                    </a>
                    <p className="text-[0.85rem] text-[#666]">{item.author}</p>
                  </div>
                ))}
              </div>

              <a
                href="/blog"
                className="mt-[30px] flex items-center gap-[10px] text-[#e0e0e0] text-[0.9rem] font-semibold underline underline-offset-4 group/viewall"
              >
                {t("viewAllArticles")}{" "}
                <span className="transition-transform duration-300 group-hover/viewall:translate-x-[5px]">
                  &rarr;
                </span>
              </a>
            </motion.aside>
          </div>
        </div>
      </div>
    </section>
  );
};

export default OurBlog;
