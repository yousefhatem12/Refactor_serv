"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const WebDesignFeatures = ({ title, description, featuresList }) => {
  const { language, t } = useLanguage();
  const defaultFeatures = [
    {
      id: 1,
      title: t("wdFeature1Title") || "Professional Design",
      desc:
        t("wdFeature1Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <path d="M12 2l4.5 2.5v5L12 12l-4.5-2.5v-5L12 2z" />
          <path d="M16.5 9.5l4.5 2.5v5L16.5 19.5l-4.5-2.5v-5l4.5-2.5z" />
          <path d="M7.5 9.5l4.5 2.5v5L7.5 19.5l-4.5-2.5v-5l4.5-2.5z" />
        </svg>
      ),
    },
    {
      id: 2,
      title: t("wdFeature2Title") || "Modern Colors",
      desc:
        t("wdFeature2Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <circle cx="13.5" cy="6.5" r=".5" />
          <circle cx="17.5" cy="10.5" r=".5" />
          <circle cx="8.5" cy="7.5" r=".5" />
          <circle cx="6.5" cy="12.5" r=".5" />
          <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z" />
        </svg>
      ),
    },
    {
      id: 3,
      title: t("wdFeature3Title") || "Free Design",
      desc:
        t("wdFeature3Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <path d="M12 19l7-7 3 3-7 7-3-3z" />
          <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z" />
          <path d="M2 2l7.586 7.586" />
          <circle cx="11" cy="11" r="2" />
        </svg>
      ),
    },
    {
      id: 4,
      title: t("wdFeature4Title") || "Responsive Design",
      desc:
        t("wdFeature4Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
          <line x1="12" y1="18" x2="12.01" y2="18" />
        </svg>
      ),
    },
    {
      id: 5,
      title: t("wdFeature5Title") || "Multi-lingual",
      desc:
        t("wdFeature5Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <circle cx="12" cy="12" r="10" />
          <line x1="2" y1="12" x2="22" y2="12" />
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
        </svg>
      ),
    },
    {
      id: 6,
      title: t("wdFeature6Title") || "UI Components",
      desc:
        t("wdFeature6Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
          <line x1="3" y1="9" x2="21" y2="9" />
          <line x1="9" y1="21" x2="9" y2="9" />
        </svg>
      ),
    },
    {
      id: 7,
      title: t("wdFeature7Title") || "Unity Complete Markets",
      desc:
        t("wdFeature7Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
        </svg>
      ),
    },
    {
      id: 8,
      title: t("wdFeature8Title") || "Unique Features",
      desc:
        t("wdFeature8Desc") ||
        "A web design that by a simple modern and professional design with the latest web dev platforms that addresses you and your identity.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
        </svg>
      ),
    },
  ];

  const displayFeatures = featuresList && featuresList.length > 0 
    ? featuresList.map((f, i) => {
        const itemTitle = f.title && typeof f.title === "object"
          ? (f.title[language] || f.title.en || "")
          : (f.title || "");
        const itemDesc = f.desc && typeof f.desc === "object"
          ? (f.desc[language] || f.desc.en || "")
          : (f.desc || (f.content && typeof f.content === "object" ? (f.content[language] || f.content.en || "") : (f.content || "")));
        return {
          id: i,
          title: itemTitle,
          desc: itemDesc,
          icon: defaultFeatures[i % defaultFeatures.length]?.icon
        };
      })
    : [];

  if (displayFeatures.length === 0) return null;

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-20 max-w-4xl mx-auto">
          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-white text-3xl md:text-5xl font-bold mb-6 leading-tight"
          >
            {title || t("wdFeaturesTitle")}
          </motion.h2>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-gray-400 text-lg md:text-xl leading-relaxed"
          >
            {description || t("wdFeaturesDesc")}
          </motion.p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {displayFeatures.map((feature, index) => (
            <motion.div
              key={feature.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.1, duration: 0.6 }}
              className="bg-[#03071E]/60 backdrop-blur-md rounded-2xl p-8 border border-white/5 hover:border-white/10 transition-colors flex flex-col items-center text-center group"
            >
              <div className="w-16 h-16 rounded-full border border-white/10 flex items-center justify-center bg-white/5 mb-6 group-hover:bg-[#D98B50]/10 group-hover:border-[#D98B50]/50 transition-all duration-300">
                {feature.icon}
              </div>
              <h3 className="text-white text-lg font-bold mb-4 group-hover:text-[#D98B50] transition-colors">
                {feature.title}
              </h3>
              <p className="text-gray-400 text-sm leading-relaxed">
                {feature.desc}
              </p>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default WebDesignFeatures;
