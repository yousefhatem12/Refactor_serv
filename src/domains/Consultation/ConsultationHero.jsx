"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";

const ConsultationHero = () => {
  const { t, language } = useLanguage();
  const settings = useSettings();

  return (
    <section className="relative w-full min-h-[50vh] flex items-center justify-center px-6 lg:px-20 pt-32 pb-10 overflow-hidden">
      {/* Background Glows */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none">
        <div className="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/20 blur-[120px] rounded-full" />
        <div className="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/20 blur-[120px] rounded-full" />
      </div>

      <div className="relative z-20 max-w-4xl w-full text-center">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="inline-block px-4 py-1 rounded-full border border-white/10 bg-white/5 text-blue-400 text-sm font-medium mb-6 backdrop-blur-sm"
        >
          {t("services")} • {t("consultationHeroTitle")}
        </motion.div>

        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.1 }}
          className={`text-white text-4xl md:text-5xl font-bold mb-6 tracking-tight ${
            language === "ar" ? "font-arabic" : ""
          }`}
        >
          {t("consultationHeroTitle")}
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="text-gray-400 text-lg md:text-xl leading-relaxed max-w-2xl mx-auto"
        >
          {t("consultationHeroDesc")}
        </motion.p>
      </div>

      {/* Decorative Planet */}
      <motion.div
        initial={{ opacity: 0, scale: 0.8 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 1, ease: "easeOut" }}
        className="absolute -right-20 top-0 w-64 h-64 pointer-events-none z-10 opacity-30 blur-sm lg:blur-none lg:opacity-50"
      >
        <img
          src="/Services/blue.webp"
          alt="Decorative Planet"
          className="w-full h-auto object-contain animate-pulse-slow"
        />
      </motion.div>
    </section>
  );
};

export default ConsultationHero;
