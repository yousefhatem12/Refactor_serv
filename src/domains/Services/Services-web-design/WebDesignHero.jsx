"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { FaPlay } from "react-icons/fa";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import VideoModal from "@/app/components/Shared/VideoModal";
import PricingModal from "@/app/components/Shared/PricingModal";
import { useRouter } from "next/navigation";

const getPlanetColors = (planetImg, propGradient, propDotColor) => {
  if (propGradient && propDotColor) {
    return { gradient: propGradient, dotColor: propDotColor };
  }
  const img = (planetImg || "").toLowerCase();
  if (img.includes("blue") || img.includes("planet4") || img.includes("earth")) {
    return { gradient: "linear-gradient(to bottom, #00f2fe, #4facfe)", dotColor: "#38bdf8" };
  }
  if (img.includes("purple") || img.includes("planet2")) {
    return { gradient: "linear-gradient(to bottom, #a855f7, #6b21a8)", dotColor: "#a855f7" };
  }
  if (img.includes("green") || img.includes("planet3")) {
    return { gradient: "linear-gradient(to bottom, #22c55e, #15803d)", dotColor: "#22c55e" };
  }
  if (img.includes("yellow") || img.includes("planet1") || img.includes("orange")) {
    return { gradient: "linear-gradient(to bottom, #d4a373, #5c4033)", dotColor: "#84694f" };
  }
  return {
    gradient: propGradient || "linear-gradient(to bottom, #d4a373, #5c4033)",
    dotColor: propDotColor || "#84694f",
  };
};

const WebDesignHero = ({ data, serviceId = "ui-ux-design", gradient, dotColor, planetImage }) => {
  const router = useRouter();
  const { language, t } = useLanguage();
  const isRtl = language === "ar";
  const settings = useSettings();
  const [isVideoModalOpen, setIsVideoModalOpen] = useState(false);
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);

  const resolved = getPlanetColors(planetImage, gradient, dotColor);
  const resolvedDotColor = resolved.dotColor;

  return (
    <section 
      dir={isRtl ? "rtl" : "ltr"}
      className="relative w-full h-[100dvh] min-h-[600px] flex items-center px-6 lg:px-20 pt-24 pb-12 overflow-hidden"
    >
      {/* Masked Background Container to fade out the hard edge seamlessly */}
      <div 
        className="absolute inset-0 w-full h-full z-0 pointer-events-none"
        style={{
          maskImage: 'linear-gradient(to bottom, black 60%, transparent 100%)',
          WebkitMaskImage: 'linear-gradient(to bottom, black 60%, transparent 100%)'
        }}
      >
        {/* Background Image behind everything */}
        <div
          className={`absolute inset-0 w-full h-full bg-no-repeat bg-cover bg-center mix-blend-screen ${isRtl ? "scale-x-[-1]" : ""}`}
          style={{ backgroundImage: "url('/services-web-design/hero.webp')" }}
        />

        {/* Dark overlay for heavier look */}
        <div className="absolute inset-0 w-full h-full bg-[#040A37]/50 mix-blend-multiply" />

        {/* Full-screen Tint (Solid) */}
        <div
          className="absolute inset-0 w-full h-full opacity-20 mix-blend-screen"
          style={{
            backgroundColor: resolvedDotColor
          }}
        />
      </div>

      {/* Radial glow behind planet side */}
      <div
        className={`absolute w-[700px] h-[700px] rounded-full pointer-events-none z-[3] blur-[120px] opacity-35 top-1/2 -translate-y-1/2 ${isRtl ? "left-[-200px]" : "right-[-200px]"}`}
        style={{ background: `radial-gradient(circle, ${resolvedDotColor} 0%, transparent 65%)` }}
      />

      {/* Content */}
      <div className="relative z-20 flex w-full flex-col lg:flex-row items-center justify-between gap-12">
        {/* Left Content */}
        <div className="relative z-20 max-w-2xl w-full lg:w-[50%] flex flex-col justify-center text-start items-start">
          <motion.h1
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 md:mb-6 tracking-tight uppercase leading-tight max-w-full"
            style={{
              background: `linear-gradient(to bottom, #ffffff 0%, ${resolvedDotColor} 100%)`,
              WebkitBackgroundClip: "text",
              WebkitTextFillColor: "transparent",
              backgroundClip: "text",
              filter: `drop-shadow(0 2px 8px rgba(0,0,0,0.6))`,
            }}
          >
            {data?.hero_title || data?.title || t("webDesignHeroTitle")}
          </motion.h1>

          <motion.p
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="text-gray-300 text-sm sm:text-base md:text-lg leading-relaxed mb-8 md:mb-10 max-h-[25vh] overflow-y-auto"
          >
            {data?.hero_subtitle || data?.subtitle || data?.description || t("webDesignHeroDesc")}
          </motion.p>

          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.4 }}
            className="flex flex-wrap w-full items-center gap-4 justify-start"
          >
            <button
              onClick={() => router.push("/contact-us")}
              className="px-6 py-2.5 md:px-8 md:py-3 rounded-full bg-white text-[#040A37] font-semibold text-base md:text-lg hover:bg-gray-200 transition-colors border-none cursor-pointer"
            >
              {settings?.hero_cta_text || t("getStarted")}
            </button>

            <button
              onClick={() => setIsVideoModalOpen(true)}
              className="flex items-center gap-2 md:gap-3 text-white hover:text-gray-300 transition-colors group bg-transparent border-none cursor-pointer"
            >
              <div className="w-10 h-10 md:w-12 md:h-12 rounded-full border border-white/40 flex items-center justify-center group-hover:border-white transition-colors">
                <FaPlay className="text-white ml-1 text-xs md:text-sm" />
              </div>
              <span className="font-semibold text-base md:text-lg">
                {settings?.hero_video_button_text || t("knowMore")}
              </span>
            </button>
          </motion.div>
        </div>

        {/* Right Image (Planet) */}
        <motion.div
          initial={{ opacity: 0, scale: 0.8 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 1, ease: "easeOut" }}
          className="hidden lg:flex relative w-full lg:w-[45%] max-w-[800px] pointer-events-none z-10 items-center justify-center overflow-visible"
        >
          <motion.div
            animate={{ y: [0, -18, 0] }}
            transition={{ duration: 6, repeat: Infinity, ease: "easeInOut" }}
            className="w-full flex items-center justify-center"
          >
            <img
              src={planetImage || "/Services/yellow.webp"}
              alt="Planet"
              className="w-[100%] h-auto object-contain scale-110"
              style={{
                filter: `drop-shadow(0 0 40px ${resolvedDotColor}) drop-shadow(0 0 80px ${resolvedDotColor}aa) drop-shadow(0 0 120px ${resolvedDotColor}55)`,
              }}
            />
          </motion.div>
        </motion.div>
      </div>

      <VideoModal isOpen={isVideoModalOpen} onClose={() => setIsVideoModalOpen(false)} videoUrl={settings?.company_video_url} />
      <PricingModal isOpen={isPricingModalOpen} onClose={() => setIsPricingModalOpen(false)} serviceId={serviceId} />
    </section>
  );
};

export default WebDesignHero;
