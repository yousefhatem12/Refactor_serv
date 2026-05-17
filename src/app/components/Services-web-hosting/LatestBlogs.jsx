"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const LatestBlogs = () => {
  const { t } = useLanguage();
  const blogs = [
    {
      id: 1,
      title: t("blog1Title"),
      desc: t("blog1Desc"),
      img: "/services-page/card-1.png",
      link: "#",
    },
    {
      id: 2,
      title: t("blog2Title"),
      desc: t("blog2Desc"),
      img: "/services-page/card-2.png",
      link: "#",
    },
    {
      id: 3,
      title: t("blog3Title"),
      desc: t("blog3Desc"),
      img: "/services-page/card-3.png",
      link: "#",
    },
  ];

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

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {blogs.map((blog, index) => (
            <motion.div
              key={blog.id}
              initial={{ opacity: 0, y: 40 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.2, duration: 0.6 }}
              className="group cursor-pointer rounded-3xl overflow-hidden relative"
            >
              <div className="relative h-[450px] w-full">
                {/* Background Image */}
                <img
                  src={blog.img}
                  alt={blog.title}
                  className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />

                {/* Overlay gradient so text is readable */}
                <div className="absolute inset-0 bg-gradient-to-t from-[#020617] via-[#020617]/80 to-transparent opacity-90 group-hover:opacity-100 transition-opacity duration-300"></div>

                {/* Content at bottom */}
                <div className="absolute bottom-0 left-0 w-full p-8 translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                  <h3 className="text-white text-2xl font-bold mb-3">
                    {blog.title}
                  </h3>
                  <p className="text-gray-300 text-sm leading-relaxed mb-4 line-clamp-3">
                    {blog.desc}
                  </p>
                  <a
                    href={blog.link}
                    className="inline-block text-[#D98B50] font-semibold text-sm hover:text-orange-400 transition-colors"
                  >
                    {t("readMore")}
                  </a>
                </div>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Carousel indicators (dots) - visual only as per design */}
        <div className="flex justify-center items-center gap-2 mt-12">
          <div className="w-12 h-1 rounded-full bg-[#D98B50]"></div>
          <div className="w-2 h-2 rounded-full bg-gray-600"></div>
          <div className="w-2 h-2 rounded-full bg-gray-600"></div>
        </div>
      </div>
    </section>
  );
};

export default LatestBlogs;
