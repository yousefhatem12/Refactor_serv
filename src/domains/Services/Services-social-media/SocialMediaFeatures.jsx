"use client";
import React from "react";
import Image from "next/image";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const SocialMediaFeatures = () => {
  const { t } = useLanguage();
  const features = [
    {
      id: 1,
      title: t("smFeature1Title") || "Content Creation",
      desc: t("smFeature1Desc") || "Creating attractive and engaging content that speaks to your audience.",
      icon: "/services-social-media/icon-1.webp",
    },
    {
      id: 2,
      title: t("smFeature2Title") || "Account Management",
      desc: t("smFeature2Desc") || "Professional management of all your social media platforms.",
      icon: "/services-social-media/icon-2.webp",
    },
    {
      id: 3,
      title: t("smFeature3Title") || "Ad Campaigns",
      desc: t("smFeature3Desc") || "Targeted advertising campaigns that bring real results.",
      icon: "/services-social-media/icon-3.webp",
    },
    {
      id: 4,
      title: t("smFeature4Title") || "Performance Analysis",
      desc: t("smFeature4Desc") || "Detailed reports and analysis to track your success.",
      icon: "/services-social-media/icon-4.webp",
    },
  ];

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
            {t("socialMediaFeaturesTitle")}
          </motion.h2>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-gray-400 text-lg md:text-xl leading-relaxed"
          >
            {t("socialMediaFeaturesDesc")}
          </motion.p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map((feature, index) => (
            <motion.div
              key={feature.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.1, duration: 0.6 }}
              className="bg-[#03071E]/60 backdrop-blur-md rounded-2xl p-8 border border-white/5 hover:border-white/10 transition-colors flex flex-col items-center text-center group"
            >
              <div className="w-16 h-16 rounded-full border border-white/10 flex items-center justify-center bg-white/5 mb-6 group-hover:bg-[#D98B50]/10 group-hover:border-[#D98B50]/50 transition-all duration-300">
                <Image
                  src={feature.icon}
                  alt={feature.title}
                  width={32}
                  height={32}
                  loading="lazy"
                  className="w-8 h-8 object-contain"
                />
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

export default SocialMediaFeatures;
