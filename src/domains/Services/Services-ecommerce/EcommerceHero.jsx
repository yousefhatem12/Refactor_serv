"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { FaWhatsapp } from "react-icons/fa";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import { getMediaUrl } from "@/lib/api";

const getPlanetColors = (planetImg, propGradient, propDotColor) => {
  if (propGradient && propDotColor) {
    return {
      gradient: propGradient,
      dotColor: propDotColor
    };
  }

  const img = (planetImg || "").toLowerCase();
  
  if (img.includes("blue") || img.includes("planet4") || img.includes("earth")) {
    return {
      gradient: "linear-gradient(to bottom, #00f2fe, #4facfe)",
      dotColor: "#38bdf8"
    };
  }
  if (img.includes("purple") || img.includes("planet2")) {
    return {
      gradient: "linear-gradient(to bottom, #a855f7, #6b21a8)",
      dotColor: "#a855f7"
    };
  }
  if (img.includes("green") || img.includes("planet3")) {
    return {
      gradient: "linear-gradient(to bottom, #22c55e, #15803d)",
      dotColor: "#22c55e"
    };
  }
  if (img.includes("yellow") || img.includes("planet1") || img.includes("orange")) {
    return {
      gradient: "linear-gradient(to bottom, #d4a373, #5c4033)",
      dotColor: "#84694f"
    };
  }
  // Default fallback (Green for ecommerce)
  return {
    gradient: propGradient || "linear-gradient(to bottom, #22c55e, #15803d)",
    dotColor: propDotColor || "#22c55e"
  };
};

const EcommerceHero = ({
  data,
  serviceId = "ecommerce",
  gradient,
  dotColor,
  planetImage,
}) => {
  const { language, t } = useLanguage();
  const isRtl = language === "ar";
  const settings = useSettings();

  const heroImage = data?.hero_image
    ? getMediaUrl(data.hero_image)
    : "/services-web-dev/hero.webp";

  const whatsappNumber = settings?.site_whatsapp
    ? settings.site_whatsapp.replace(/\D/g, "")
    : "201288863631";
  const whatsappUrl = `https://api.whatsapp.com/send/?phone=${whatsappNumber}&text&type=phone_number&app_absent=0`;

  const resolved = getPlanetColors(planetImage, gradient, dotColor);
  const resolvedGradient = resolved.gradient;
  const resolvedDotColor = resolved.dotColor;

  return (
    <section className="relative w-full h-[calc(100dvh-80px)] min-h-[750px] lg:h-[calc(100vh-80px)] flex items-center justify-between px-6 lg:px-20 pt-8 pb-12 lg:py-0 overflow-hidden">
      {/* Background Image behind everything */}
      <div
        className={`absolute inset-0 w-full h-full z-0 bg-no-repeat bg-cover bg-center mix-blend-screen ${isRtl ? "scale-x-[-1]" : ""}`}
        style={{ backgroundImage: `url('${heroImage}')` }}
      />

      {/* Dynamic Overlay */}
      {resolvedGradient && (
        <div
          className="absolute inset-0 w-full h-full z-[5] pointer-events-none opacity-[0.12] mix-blend-screen"
          style={{
            background: resolvedGradient.replace(/\b\d+deg|to right|to left\b/g, 'to bottom')
          }}
        />
      )}

      {/* Content */}
      <div className="relative z-20 max-w-2xl w-full lg:w-1/2 flex flex-col justify-center">
        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-bold mb-4 md:mb-6 tracking-tight uppercase leading-tight md:leading-[1.1] pb-2"
          style={{
            background: resolvedGradient.replace(/\b\d+deg|to right|to left\b/g, 'to bottom'),
            WebkitBackgroundClip: "text",
            WebkitTextFillColor: "transparent",
            filter: `brightness(1.2) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5)) drop-shadow(0 0 10px ${resolvedDotColor}66)`,
          }}
        >
          {data?.title || t("ecommerceHeroTitle")}
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="text-gray-300 text-sm sm:text-base md:text-lg lg:text-xl leading-relaxed mb-6 md:mb-8 lg:mb-10 max-h-[30vh] overflow-y-auto custom-scrollbar pr-2"
        >
          {data?.subtitle || t("ecommerceHeroDesc")}
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="flex flex-wrap items-center gap-4 md:gap-6"
        >
          <a
            href={whatsappUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="flex items-center gap-2 px-6 py-2.5 md:px-8 md:py-3 rounded-full bg-[#25D366] text-white font-semibold text-base md:text-lg hover:bg-[#128C7E] transition-colors cursor-pointer border-none no-underline"
          >
            <FaWhatsapp size={24} />
            {t("ecommerceBuildNow")}
          </a>
        </motion.div>
      </div>

      {/* Right/Left Image (Planet) */}
      <motion.div
        initial={{ opacity: 0, scale: 0.8 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 1, ease: "easeOut" }}
        className={`hidden lg:block absolute ${isRtl ? "left-0" : "right-0"} top-1/2 -translate-y-1/2 w-1/2 max-w-[800px] pointer-events-none z-10`}
      >
        <motion.div
          animate={{ y: [0, -20, 0] }}
          transition={{ duration: 6, repeat: Infinity, ease: "easeInOut" }}
        >
          <img
            src={planetImage || "Services/green.webp"}
            alt="Planet"
            className={`w-full h-auto object-contain drop-shadow-[0_0_50px_rgba(34,197,94,0.3)] scale-[1.35] ${isRtl ? "-translate-x-[10%]" : "translate-x-[10%]"}`}
          />
        </motion.div>
      </motion.div>
    </section>
  );
};

export default EcommerceHero;
