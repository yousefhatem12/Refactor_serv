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
import VideoModal from "../../Shared/VideoModal";
import PricingModal from "../../Shared/PricingModal";
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

  let title1 = "";
  let title2 = "";

  if (data?.title || data?.subtitle) {
    title1 = data?.title || "";
    title2 = data?.subtitle || "";
  } else if (heroTitle1 || heroTitle2) {
    title1 = heroTitle1;
    title2 = heroTitle2;
  } else {
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
    <section className="relative w-full   shadow-lg h-screen overflow-hidden flex items-center pt-10 lg:pt-28 scroll-mt-20 md:scroll-mt-28">
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
                "/Hero/orange-planet.png"
              }
              alt="Orange Planet"
              className="w-[400px] md:w-[600px] lg:w-[750px] opacity-80"
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
              className="w-full md:w-[60%] lg:w-[50%] max-w-2xl text-white text-center md:text-start flex flex-col items-center md:items-start"
            >
              <motion.h1
                variants={itemVariants}
                className="text-4xl md:text-5xl lg:text-5xl font-bold text-white mb-6 leading-tight"
              >
                {isLoadingSettings ? (
                  <span className="opacity-0 text-white">Loading...</span>
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
                className="text-base sm:text-lg md:text-xl lg:text-2xl text-gray-400 mb-10 max-w-xl leading-relaxed font-light text-balance opacity-90"
              >
                {isLoadingSettings ? (
                  <span className="opacity-0 text-white">Loading...</span>
                ) : (
                  subtitle
                )}
              </motion.p>

              <motion.div
                variants={itemVariants}
                className="flex flex-row flex-wrap items-center justify-center md:justify-start gap-4 sm:gap-6 w-full"
              >
                <button
                  onClick={() => router.push("/contact-us")}
                  className="bg-gradient-to-r from-[#FF8A00] to-[#FF5C00] hover:from-[#FF5C00] hover:to-[#FF8A00] text-white text-sm sm:text-base px-8 py-4 rounded-2xl font-black uppercase tracking-widest flex items-center gap-3 transition-all transform hover:scale-105 shadow-[0_20px_40px_-10px_rgba(255,138,0,0.5)] active:scale-95 cursor-pointer border-none"
                >
                  <span className="bg-white/20 p-1 sm:p-1.5 rounded-full">
                    <svg
                      width="16"
                      height="16"
                      className="sm:w-[18px] sm:h-[18px]"
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
                    <span className="opacity-0">Loading...</span>
                  ) : (
                    settings?.hero_cta_text || t("getAQuote")
                  )}
                </button>

                <div className="flex items-center gap-4 group">
                  <button
                    onClick={() => setIsVideoModalOpen(true)}
                    className="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-full bg-white flex items-center justify-center hover:scale-110 transition-transform shadow-lg active:scale-95 group cursor-pointer border-none"
                  >
                    <svg
                      width="20"
                      height="20"
                      className="sm:w-[24px] sm:h-[24px] text-black ml-1 group-hover:text-[#D98B50] transition-colors"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                    >
                      <polygon points="5 3 19 12 5 21 5 3" />
                    </svg>
                  </button>
                  <span
                    onClick={() => setIsVideoModalOpen(true)}
                    className="text-white font-bold text-sm sm:text-base cursor-pointer hover:text-[#D98B50] transition-colors"
                  >
                    {isLoadingSettings ? (
                      <span className="opacity-0">Loading...</span>
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
