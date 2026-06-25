"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const ServicesFeatures = ({ title, featuresData, featuresList }) => {
  const { language, t } = useLanguage();
  const defaultFeatures = [
    {
      id: 1,
      title:
        t("whFeature1Title") ||
        "The fastest efficiency and speed of connection",
      desc:
        t("whFeatureDesc") ||
        "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible. The site’s components from pictures, texts, etc., require the greatest possible speed in reading data and executing commands directly without any delay. For this we are working on providing the fastest servers that guarantee you excellent speed.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9" />
          <polyline points="13 11 9 17 15 17 11 23" />
        </svg>
      ),
    },
    {
      id: 2,
      title: t("whFeature2Title") || "Protecting the server and sites 24/7",
      desc:
        t("whFeatureDesc") ||
        "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible. The site’s components from pictures, texts, etc., require the greatest possible speed in reading data and executing commands directly without any delay. For this we are working on providing the fastest servers that guarantee you excellent speed.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <rect x="2" y="4" width="20" height="16" rx="2" />
          <path d="M6 8h.01" />
          <path d="M10 8h.01" />
          <path d="M14 8h.01" />
          <path d="M2 12h20" />
          <path d="M12 16v.01" />
          <path d="M16 16v.01" />
          <path d="M8 16v.01" />
        </svg>
      ),
    },
    {
      id: 3,
      title: "Distinctive efficiency and speed of connection",
      desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible. The site’s components from pictures, texts, etc., require the greatest possible speed in reading data and executing commands directly without any delay. For this we are working on providing the fastest servers that guarantee you excellent speed.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
          <path d="M3.27 6.96L12 12.01l8.73-5.05" />
          <path d="M12 22.08V12" />
          <circle cx="8" cy="10" r="1" fill="currentColor" />
          <circle cx="16" cy="14" r="1" fill="currentColor" />
        </svg>
      ),
    },
    {
      id: 4,
      title: "Distinctive efficiency and speed of connection",
      desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible. The site’s components from pictures, texts, etc., require the greatest possible speed in reading data and executing commands directly without any delay. For this we are working on providing the fastest servers that guarantee you excellent speed.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="48"
          height="48"
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
      id: 5,
      title: "Distinctive efficiency and speed of connection",
      desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible. The site’s components from pictures, texts, etc., require the greatest possible speed in reading data and executing commands directly without any delay. For this we are working on providing the fastest servers that guarantee you excellent speed.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="text-[#D98B50]"
        >
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          <path d="m9 12 2 2 4-4" />
        </svg>
      ),
    },
    {
      id: 6,
      title: "Distinctive efficiency and speed of connection",
      desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible. The site’s components from pictures, texts, etc., require the greatest possible speed in reading data and executing commands directly without any delay. For this we are working on providing the fastest servers that guarantee you excellent speed.",
      icon: (
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="48"
          height="48"
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
  ];

  const list = featuresData || featuresList;
  const displayFeatures = (list && Array.isArray(list) && list.length > 0)
    ? list.map((item, index) => {
        return {
          id: index + 1,
          title: item.title && typeof item.title === "object" ? (item.title[language] || item.title.en || "") : (item.title || ""),
          desc: item.desc && typeof item.desc === "object" ? (item.desc[language] || item.desc.en || "") : (item.desc || item.content && typeof item.content === "object" ? (item.content[language] || item.content.en || "") : (item.content || "")),
          icon: defaultFeatures[index % defaultFeatures.length]?.icon,
        };
      })
    : [];

  if (displayFeatures.length === 0) return null;

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      <div className="max-w-7xl mx-auto">
        <motion.h2
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-white text-3xl md:text-5xl font-bold text-center mb-20 max-w-4xl mx-auto leading-tight"
        >
          {title ||
            t("whFeaturesTitle") ||
            "Let us host your website with the most powerful features"}
        </motion.h2>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-16">
          {displayFeatures.map((feature, index) => (
            <motion.div
              key={feature.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.1, duration: 0.6 }}
              className="flex items-start gap-6 group cursor-pointer"
            >
              <div className="flex-shrink-0 flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:drop-shadow-[0_0_15px_rgba(217,139,80,0.5)]">
                {feature.icon}
              </div>
              <div>
                <h3 className="text-white text-xl font-bold mb-3 group-hover:text-[#D98B50] transition-colors">
                  {feature.title}
                </h3>
                <p className="text-gray-400 text-sm leading-relaxed">
                  {feature.desc}
                </p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ServicesFeatures;
