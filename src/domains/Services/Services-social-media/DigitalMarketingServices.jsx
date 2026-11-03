"use client";
import React from "react";
import { motion } from "framer-motion";
import {
  FiVolume2,
  FiBarChart2,
  FiCrosshair,
  FiVideo,
  FiTool,
  FiCamera,
} from "react-icons/fi";
import { useLanguage } from "@/app/context/LanguageContext";

const DigitalMarketingServices = ({ marketingTitle, marketingSubtitle, marketingList }) => {
  const { language, t } = useLanguage();
  const services = (marketingList && Array.isArray(marketingList) && marketingList.length > 0)
    ? marketingList.map((item, index) => {
        const icons = [
          <FiVolume2 className="text-2xl" key="1" />,
          <FiBarChart2 className="text-2xl" key="2" />,
          <FiCrosshair className="text-2xl" key="3" />,
          <FiVideo className="text-2xl" key="4" />,
          <FiTool className="text-2xl" key="5" />,
          <FiCamera className="text-2xl" key="6" />,
        ];
        return {
          id: index + 1,
          title: item.title?.[language] || item.title || "",
          desc: item.desc?.[language] || item.desc || item.content?.[language] || item.content || "",
          icon: icons[index % icons.length],
        };
      })
    : [];

  if (services.length === 0) return null;

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-16 max-w-4xl mx-auto">
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-[#D98B50] font-semibold uppercase tracking-wider mb-4"
          >
            {marketingSubtitle?.[language] || marketingSubtitle || t("digitalMarketingSubtitle")}
          </motion.p>
          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="text-white text-3xl md:text-5xl font-bold leading-tight"
          >
            {marketingTitle?.[language] || marketingTitle || t("digitalMarketingTitle")}
          </motion.h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {services.map((service, index) => (
            <motion.div
              key={service.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.1, duration: 0.5 }}
              className="bg-[#040922]/60 backdrop-blur-md rounded-2xl p-8 border border-white/5 hover:border-white/10 transition-colors flex flex-col items-center text-center group"
            >
              <div className="text-white mb-4">{service.icon}</div>
              <h3 className="text-white text-xl font-bold mb-4">
                {service.title}
              </h3>
              <p className="text-gray-400 text-sm leading-relaxed mb-6">
                {service.desc}
              </p>
              <button className="px-6 py-2 rounded-full border border-[#D98B50] text-[#D98B50] hover:bg-[#D98B50] hover:text-white transition-colors text-sm font-semibold">
                {t("learnMore")}
              </button>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default DigitalMarketingServices;
