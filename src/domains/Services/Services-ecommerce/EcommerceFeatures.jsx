"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import {
  FaInfinity,
  FaSlidersH,
  FaChartLine,
  FaCreditCard,
  FaShoppingCart,
  FaMobileAlt,
  FaShareAlt,
  FaSearch,
  FaGoogle,
  FaServer,
  FaPaintBrush,
  FaPenNib,
  FaLayerGroup,
  FaGlobe,
  FaEnvelope,
  FaLock,
  FaCommentDots,
  FaComments,
  FaBullhorn,
  FaAppStore,
  FaDraftingCompass,
  FaHeart,
  FaBalanceScale
} from "react-icons/fa";

const EcommerceFeatures = ({ title, subtitle, featuresList }) => {
  const { language, t } = useLanguage();

  const defaultFeatures = [
    { id: 1, title: t("ecFeature1Title"), desc: t("ecFeature1Desc"), icon: <FaInfinity size={24} /> },
    { id: 2, title: t("ecFeature2Title"), desc: t("ecFeature2Desc"), icon: <FaSlidersH size={24} /> },
    { id: 3, title: t("ecFeature3Title"), desc: t("ecFeature3Desc"), icon: <FaChartLine size={24} /> },
    { id: 4, title: t("ecFeature4Title"), desc: t("ecFeature4Desc"), icon: <FaCreditCard size={24} /> },
    { id: 5, title: t("ecFeature5Title"), desc: t("ecFeature5Desc"), icon: <FaShoppingCart size={24} /> },
    { id: 6, title: t("ecFeature6Title"), desc: t("ecFeature6Desc"), icon: <FaMobileAlt size={24} /> },
    { id: 7, title: t("ecFeature7Title"), desc: t("ecFeature7Desc"), icon: <FaShareAlt size={24} /> },
    { id: 8, title: t("ecFeature8Title"), desc: t("ecFeature8Desc"), icon: <FaSearch size={24} /> },
    { id: 9, title: t("ecFeature9Title"), desc: t("ecFeature9Desc"), icon: <FaGoogle size={24} /> },
    { id: 10, title: t("ecFeature10Title"), desc: t("ecFeature10Desc"), icon: <FaServer size={24} /> },
    { id: 11, title: t("ecFeature11Title"), desc: t("ecFeature11Desc"), icon: <FaPaintBrush size={24} /> },
    { id: 12, title: t("ecFeature12Title"), desc: t("ecFeature12Desc"), icon: <FaPenNib size={24} /> },
    { id: 13, title: t("ecFeature13Title"), desc: t("ecFeature13Desc"), icon: <FaLayerGroup size={24} /> },
    { id: 14, title: t("ecFeature14Title"), desc: t("ecFeature14Desc"), icon: <FaGlobe size={24} /> },
    { id: 15, title: t("ecFeature15Title"), desc: t("ecFeature15Desc"), icon: <FaEnvelope size={24} /> },
    { id: 16, title: t("ecFeature16Title"), desc: t("ecFeature16Desc"), icon: <FaLock size={24} /> },
    { id: 17, title: t("ecFeature17Title"), desc: t("ecFeature17Desc"), icon: <FaCommentDots size={24} /> },
    { id: 18, title: t("ecFeature18Title"), desc: t("ecFeature18Desc"), icon: <FaComments size={24} /> },
    { id: 19, title: t("ecFeature19Title"), desc: t("ecFeature19Desc"), icon: <FaBullhorn size={24} /> },
    { id: 20, title: t("ecFeature20Title"), desc: t("ecFeature20Desc"), icon: <FaAppStore size={24} /> },
    { id: 21, title: t("ecFeature21Title"), desc: t("ecFeature21Desc"), icon: <FaDraftingCompass size={24} /> },
    { id: 22, title: t("ecFeature22Title"), desc: t("ecFeature22Desc"), icon: <FaHeart size={24} /> },
    { id: 23, title: t("ecFeature23Title"), desc: t("ecFeature23Desc"), icon: <FaBalanceScale size={24} /> },
  ];

  const features = (featuresList && Array.isArray(featuresList) && featuresList.length > 0)
    ? featuresList.map((item, index) => {
        return {
          id: index + 1,
          title: item.title && typeof item.title === "object" ? (item.title[language] || item.title.en || "") : (item.title || ""),
          desc: item.desc && typeof item.desc === "object" ? (item.desc[language] || item.desc.en || "") : (item.desc || item.content && typeof item.content === "object" ? (item.content[language] || item.content.en || "") : (item.content || "")),
          icon: defaultFeatures[index % defaultFeatures.length]?.icon,
        };
      })
    : defaultFeatures;

  if (features.length === 0) return null;

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
            {title || t("ecommerceFeaturesTitle")}
          </motion.h2>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-gray-400 text-lg md:text-xl leading-relaxed"
          >
            {subtitle || t("ecommerceFeaturesDesc")}
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

export default EcommerceFeatures;
