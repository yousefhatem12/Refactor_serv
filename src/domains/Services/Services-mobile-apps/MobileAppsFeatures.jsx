"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import {
  FaPalette,
  FaMobileAlt,
  FaStar,
  FaShieldAlt,
  FaHeadset,
  FaSyncAlt,
  FaAppStore,
  FaChalkboardTeacher
} from "react-icons/fa";

const MobileAppsFeatures = () => {
  const { t } = useLanguage();

  const features = [
    { id: 1, title: t("maFeature1Title"), desc: t("maFeature1Desc"), icon: <FaPalette size={24} /> },
    { id: 2, title: t("maFeature2Title"), desc: t("maFeature2Desc"), icon: <FaMobileAlt size={24} /> },
    { id: 3, title: t("maFeature3Title"), desc: t("maFeature3Desc"), icon: <FaStar size={24} /> },
    { id: 4, title: t("maFeature4Title"), desc: t("maFeature4Desc"), icon: <FaShieldAlt size={24} /> },
    { id: 5, title: t("maFeature5Title"), desc: t("maFeature5Desc"), icon: <FaHeadset size={24} /> },
    { id: 6, title: t("maFeature6Title"), desc: t("maFeature6Desc"), icon: <FaSyncAlt size={24} /> },
    { id: 7, title: t("maFeature7Title"), desc: t("maFeature7Desc"), icon: <FaAppStore size={24} /> },
    { id: 8, title: t("maFeature8Title"), desc: t("maFeature8Desc"), icon: <FaChalkboardTeacher size={24} /> },
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
            {t("mobileAppsFeaturesTitle")}
          </motion.h2>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-gray-400 text-lg md:text-xl leading-relaxed"
          >
            {t("mobileAppsFeaturesDesc")}
          </motion.p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map((feature, index) => (
            <motion.div
              key={feature.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: (index % 4) * 0.1, duration: 0.6 }}
              className="bg-[#03071E]/60 backdrop-blur-md rounded-2xl p-8 border border-white/5 hover:border-white/10 transition-colors flex flex-col items-center text-center group"
            >
              <div className="w-16 h-16 rounded-full border border-white/10 flex items-center justify-center bg-white/5 mb-6 group-hover:bg-[#25D366]/10 group-hover:border-[#25D366]/50 transition-all duration-300 text-[#25D366]">
                {feature.icon}
              </div>
              <h3 className="text-white text-lg font-bold mb-4 group-hover:text-[#25D366] transition-colors">
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

export default MobileAppsFeatures;
