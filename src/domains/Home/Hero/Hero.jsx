"use client";
import React, { useState, useEffect } from "react";
import {
  motion,
  AnimatePresence,
  useScroll,
  useTransform,
} from "framer-motion";
import Stars from "./Stars";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import { fetchAPI } from "@/lib/api";
import VideoModal from "@/app/components/Shared/VideoModal";
import PricingModal from "@/app/components/Shared/PricingModal";
import { useRouter } from "next/navigation";

// Staggered animation variants
const containerVariants = {
  hidden: { opacity: 0 },
  show: {
    opacity: 1,
    transition: {
      staggerChildren: 0.2,
      delayChildren: 0.4,
    },
  },
};

const itemVariants = {
  hidden: { y: 30, opacity: 0 },
  show: { y: 0, opacity: 1, transition: { duration: 0.8, ease: "easeOut" } },
};

export default function Hero({ isPhaseTwo, data }) {
  const router = useRouter();
  const { scrollY } = useScroll();
  const { t, language } = useLanguage();
  const isRtl = language === "ar";

  const [isVideoModalOpen, setIsVideoModalOpen] = useState(false);
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);
  const settings = useSettings();

  // Settings are always populated instantly from layout.js
  const isLoadingSettings = !settings || Object.keys(settings).length === 0;

  const heroTitle1 = settings?.hero_title_1 || "";
  const heroTitle2 = settings?.hero_title_2 || "";

  // Priority: 1. settings.hero_title_1 (locale-specific from backend settings)
  //           2. data.hero_title / data.title (from home page API)
  //           3. translation fallback (only after settings loaded)
  let title1 = "";
  let title2 = "";

  if (heroTitle1 || heroTitle2) {
    // Settings hero titles take highest priority (locale-correct)
    title1 = heroTitle1;
    title2 = heroTitle2;
  } else if (data?.hero_title || data?.title || data?.subtitle) {
    title1 = data?.hero_title || data?.title || "";
    title2 = data?.hero_subtitle || data?.subtitle || "";
  } else if (!isLoadingSettings) {
    // Only show translation fallback after settings have loaded (avoids English flash)
    title1 = t("innovativeSolutions") || "";
    title2 = t("tailoredToYourNeeds") || "";
  }

  const subtitle = data?.content || settings?.hero_desc || t("heroDesc");

  // Phase 1 (Hero) disappearance on scroll
  // Changed from horizontal scroll (x) to vertical fade/parallax (y)
  const textY = useTransform(scrollY, [0, 400], ["0%", "-50%"]);
  const textOpacity = useTransform(scrollY, [0, 300], [1, 0]);

  const planetY = useTransform(scrollY, [0, 400], ["-30%", "-60%"]);
  const planetOpacity = useTransform(scrollY, [0, 300], [1, 0]);

  return (
    <section className="relative w-full h-screen overflow-hidden flex items-center pt-10 lg:pt-28 scroll-mt-20 md:scroll-mt-28">
      {/* Background Layer with Stars */}
      <Stars />

      {/* Orange Planet */}
      <AnimatePresence mode="wait">
        {isPhaseTwo && (
          <motion.div
            key={`planet-${language}`}
            initial={{
              x: isRtl ? "-100%" : "100%",
              y: "-20%",
              opacity: 0,
              scale: 0.8,
            }}
            animate={{
              x: isRtl ? "-20%" : "20%",
              y: "-30%",
              opacity: 1,
              scale: 1,
            }}
            transition={{ duration: 1.0, ease: "easeOut" }}
            style={{ y: planetY, opacity: planetOpacity }}
            className={`absolute top-0 ${isRtl ? "left-0 md:-left-20 lg:-left-32" : "right-0 md:-right-20 lg:-right-32"} z-0 pointer-events-none hidden md:block`}
          >
            <img
              src={
                data?.hero_image ||
                settings?.hero_planet ||
                "/Hero/orange-planet.webp"
              }
              alt="Orange Planet"
              className="w-[320px] md:w-[480px] lg:w-[580px] opacity-80"
            />
          </motion.div>
        )}
      </AnimatePresence>

      {/* Main Content Container */}
      <div className="relative z-500 w-full max-w-7xl mx-auto px-6 sm:px-10 md:px-10 lg:px-10  flex flex-col md:flex-row items-center justify-center md:justify-start">
        {/* Left Side Content */}
        <AnimatePresence>
          {isPhaseTwo && (
            <motion.div
              variants={containerVariants}
              initial="hidden"
              animate="show"
              style={{ y: textY, opacity: textOpacity }}
              className="w-full md:w-[55%] lg:w-[48%] max-w-xl text-white text-center md:text-start flex flex-col items-center md:items-start"
            >
              <motion.h1
                variants={itemVariants}
                className="text-3xl md:text-4xl lg:text-[38px] lg:leading-[1.2] font-bold text-white mb-4 leading-tight"
              >
                {!title1 ? (
                  <span className="opacity-0 select-none" aria-hidden="true">&nbsp;</span>
                ) : (
                  <>
                    {title1}
                    {title2 && (
                      <>
                        <br />
                        {title2}
                      </>
                    )}
                  </>
                )}
              </motion.h1>

              <motion.p
                variants={itemVariants}
                className="text-sm sm:text-base md:text-lg lg:text-lg text-gray-400 mb-8 max-w-lg leading-relaxed font-light text-balance opacity-90"
              >
                {!subtitle ? (
                  <span className="opacity-0 select-none" aria-hidden="true">&nbsp;</span>
                ) : (
                  subtitle
                )}
              </motion.p>

              <motion.div
                variants={itemVariants}
                className="flex flex-row flex-wrap items-center justify-center md:justify-start gap-3.5 sm:gap-4 w-full"
              >
                <button
                  onClick={() => router.push("/contact-us")}
                  className="bg-gradient-to-r from-[#FF8A00] to-[#FF5C00] hover:from-[#FF5C00] hover:to-[#FF8A00] text-white text-xs sm:text-sm px-6 py-3 rounded-xl font-black uppercase tracking-widest flex items-center gap-2.5 transition-all transform hover:scale-105 shadow-[0_15px_30px_-10px_rgba(255,138,0,0.5)] active:scale-95 cursor-pointer border-none"
                >
                  <span className="bg-white/20 p-1 sm:p-1.5 rounded-full">
                    <svg
                      width="14"
                      height="14"
                      className="sm:w-[16px] sm:h-[16px]"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2.5"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </span>
                  {isLoadingSettings ? (
                    <span className="opacity-0 select-none" aria-hidden="true">&nbsp;</span>
                  ) : (
                    settings?.hero_cta_text || t("getAQuote")
                  )}
                </button>

                <div className="flex items-center gap-3.5 group">
                  <button
                    onClick={() => setIsVideoModalOpen(true)}
                    className="w-10 h-10 sm:w-11 sm:h-11 md:w-12 md:h-12 rounded-full bg-white flex items-center justify-center hover:scale-110 transition-transform shadow-lg active:scale-95 group cursor-pointer border-none"
                  >
                    <svg
                      width="16"
                      height="16"
                      className="sm:w-[18px] sm:h-[18px] text-black ml-0.5 group-hover:text-[#D98B50] transition-colors"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                    >
                      <polygon points="5 3 19 12 5 21 5 3" />
                    </svg>
                  </button>
                  <span
                    onClick={() => setIsVideoModalOpen(true)}
                    className="text-white font-bold text-xs sm:text-sm cursor-pointer hover:text-[#D98B50] transition-colors"
                  >
                    {isLoadingSettings ? (
                      <span className="opacity-0 select-none" aria-hidden="true">&nbsp;</span>
                    ) : (
                      settings?.hero_video_button_text || t("knowMore")
                    )}
                  </span>
                </div>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
      <VideoModal
        isOpen={isVideoModalOpen}
        onClose={() => setIsVideoModalOpen(false)}
        videoUrl={settings?.company_video_url}
      />
      <PricingModal
        isOpen={isPricingModalOpen}
        onClose={() => setIsPricingModalOpen(false)}
      />
    </section>
  );
}
