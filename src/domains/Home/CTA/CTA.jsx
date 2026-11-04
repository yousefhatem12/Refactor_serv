"use client";
import React, { useRef, useState } from "react";
import { motion, useScroll, useTransform } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import dynamic from "next/dynamic";
import { useRouter, usePathname } from "next/navigation";

const PricingModal = dynamic(() => import("@/app/components/Shared/PricingModal"), { ssr: false });
const ContactModal = dynamic(() => import("@/app/components/Shared/ContactModal"), { ssr: false });
const PlanetAstronaut = dynamic(() => import("@/app/components/Home/Shared/PlanetAstronaut"), { ssr: false });

const CTA = () => {
  const router = useRouter();
  const pathname = usePathname();
  const isHomePage =
    pathname === "/" ||
    pathname === "/ar" ||
    pathname === "/en" ||
    pathname === "/ar/" ||
    pathname === "/en/";

  const containerRef = useRef(null);
  const { t, language } = useLanguage();
  const isRtl = language === "ar";
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);
  const [isContactModalOpen, setIsContactModalOpen] = useState(false);
  const contextSettings = useSettings();
  const settings = contextSettings || {};

  // Hardware-accelerated scroll scrubbing via Framer Motion
  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start start", "end end"],
  });

  // Slide 1 scroll transformations (fade out and slide away)
  const slide1Opacity = useTransform(scrollYProgress, [0, 0.35], [1, 0]);
  const text1X = useTransform(
    scrollYProgress,
    [0, 0.45],
    [0, isRtl ? 400 : -400]
  );
  const slide1PointerEvents = useTransform(scrollYProgress, (v) =>
    v > 0.35 ? "none" : "auto"
  );

  // Slide 2 scroll transformations (fade in, scale up, slide into place)
  const slide2Opacity = useTransform(scrollYProgress, [0.35, 0.65], [0, 1]);
  const slide2Scale = useTransform(scrollYProgress, [0.35, 0.65], [0.92, 1]);
  const slide2Y = useTransform(scrollYProgress, [0.35, 0.65], [40, 0]);
  const slide2PointerEvents = useTransform(scrollYProgress, (v) =>
    v < 0.35 ? "none" : "auto"
  );

  // Non-home local planet scroll transformations
  const planetX = useTransform(
    scrollYProgress,
    [0.2, 0.75],
    [isRtl ? "25%" : "-25%", isRtl ? "-45%" : "45%"]
  );
  const planetScale = useTransform(scrollYProgress, [0.2, 0.75], [0.8, 1]);
  const planetRotate = useTransform(scrollYProgress, [0.2, 0.75], [0, 10]);
  const planetOpacity = useTransform(
    scrollYProgress,
    [0.2, 0.4, 0.8, 1],
    [0.8, 1, 0.8, 0.35]
  );

  return (
    <section
      ref={containerRef}
      className="relative md:h-[220vh] w-full bg-transparent z-10"
    >
      <div className="md:sticky md:top-0 md:h-screen w-full overflow-hidden flex flex-col md:flex-row md:items-center md:justify-center bg-transparent py-16 md:py-0">
        {/* Background Decorative Elements (Static) */}
        <div className="absolute top-1/4 right-0 w-[500px] h-[500px] bg-blue-600/5 blur-[150px] rounded-full -z-10 hidden md:block"></div>
        <div className="absolute bottom-1/4 left-0 w-[500px] h-[500px] bg-orange-600/5 blur-[150px] rounded-full -z-10 hidden md:block"></div>

        {/* Slide 1: Ready For Liftoff (Desktop Only) */}
        <motion.div
          style={{
            opacity: slide1Opacity,
            x: text1X,
            pointerEvents: slide1PointerEvents,
          }}
          className="hidden md:flex relative md:absolute md:inset-0 flex-col md:flex-row items-center justify-center gap-8 md:gap-16 px-4"
        >
          {/* Planet Side (Placeholder for layout) */}
          <div className="relative w-[280px] md:w-[450px] lg:w-[550px] aspect-square hidden md:flex items-center justify-center">
            <div className="w-full h-full rounded-full bg-orange-500/5 blur-[80px]"></div>
          </div>

          {/* Text Side */}
          <div
            className={`text-center flex flex-col items-center w-full md:w-auto ${
              isRtl ? "md:items-end md:text-right" : "md:items-start md:text-left"
            }`}
          >
            <h2 className="w-full text-2xl md:text-3xl lg:text-4xl xl:text-4xl font-black text-white mb-4 md:mb-6 leading-[1.1] tracking-tighter">
              {settings.cta_ready_for || t("readyFor")} <br />
              <span className="inline-block relative">
                {settings.cta_liftoff || t("liftoff")}
                <div className="absolute -bottom-2 left-0 w-full h-2 bg-orange-500/50 blur-sm rounded-full"></div>
              </span>
            </h2>
            <div className="w-full max-w-xl">
              <p className="text-gray-300 text-sm md:text-base lg:text-lg font-medium leading-relaxed mb-2">
                {settings.cta_desc_1 || t("ctaDesc1")}
              </p>
              <p className="text-gray-400 text-xs md:text-sm lg:text-base">
                {settings.cta_desc_2 || t("ctaDesc2")}
              </p>
            </div>
          </div>
        </motion.div>

        {/* Slide 2: Unlock Innovation (Desktop Only) */}
        <motion.div
          style={{
            opacity: slide2Opacity,
            scale: slide2Scale,
            y: slide2Y,
            pointerEvents: slide2PointerEvents,
          }}
          className="hidden md:flex relative md:absolute z-20 md:inset-0 flex-col lg:flex-row items-center justify-center gap-0 px-6 md:px-16 lg:px-24"
        >
          {/* Left Side: Content */}
          <div
            dir={isRtl ? "rtl" : "ltr"}
            className="w-full lg:w-1/2 flex flex-col z-20"
          >
            <h2 className="w-full text-2xl md:text-lg lg:text-3xl! xl:text-3xl! font-bold text-white mb-6 md:mb-8 leading-tight">
              {settings.cta_unlock_power || t("unlockPower")}{" "}
              <span className="text-[#FF8A00]">
                {settings.cta_innovation || t("innovation")}
              </span>
              , <br />
              {settings.cta_get_started || t("getStartedToday")}
            </h2>
            <p className="w-full max-w-2xl text-gray-300 text-sm md:text-base lg:text-lg font-medium mb-10">
              {settings.cta_long_desc || t("ctaLongDesc")}
            </p>
            <div className="flex flex-row gap-4 sm:gap-6">
              <button
                onClick={() => router.push("/contact-us")}
                className="bg-[#FF8A00] hover:bg-[#e67e00] text-white px-6 md:px-8 py-2 md:py-3 rounded-lg font-bold text-sm md:text-base transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(255,138,0,0.3)] cursor-pointer"
              >
                {t("getAQuote")}
              </button>
              <button
                onClick={() => setIsContactModalOpen(true)}
                className="border border-white/20 hover:border-white/40 bg-white/5 backdrop-blur-sm text-white px-6 md:px-8 py-2 md:py-3 rounded-lg font-bold text-sm md:text-base transition-all transform hover:scale-105"
              >
                {t("callUs")}
              </button>
            </div>
          </div>

          {/* Right Side: Space for global astronaut */}
          <div className="w-full lg:w-1/2 relative hidden md:flex items-center justify-center">
            <div className="relative w-[300px] md:w-[450px] lg:w-[600px] aspect-square z-20">
              <div className="absolute inset-0 bg-orange-500/10 blur-[120px] rounded-full -z-10"></div>
            </div>
          </div>
        </motion.div>

        {/* Mobile CTA Layout (Visible only on < 768px) */}
        <div className="md:hidden w-full px-4 sm:px-6 relative z-20 mt-4">
          <div className="w-full relative p-6 sm:p-10 rounded-[2rem] bg-gradient-to-br from-white/[0.08] to-transparent border border-white/[0.05] backdrop-blur-xl shadow-2xl flex flex-col items-center text-center overflow-hidden">
            {/* Inner Glow */}
            <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-1/2 bg-[#FF8A00]/20 blur-[70px] -z-10 rounded-full"></div>

            <h2 className="text-2xl min-[400px]:text-3xl sm:text-4xl font-black text-white mb-4 leading-tight tracking-tight">
              {settings.cta_ready_for || t("readyFor")} <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#FF8A00] to-[#FFD200]">
                {settings.cta_liftoff || t("liftoff")}
              </span>
            </h2>

            <p className="text-gray-300 text-xs min-[400px]:text-sm sm:text-base mb-8 sm:mb-10 leading-relaxed font-medium">
              {settings.cta_desc_1 || t("ctaDesc1")}{" "}
              {settings.cta_desc_2 || t("ctaDesc2")}
            </p>

            <div className="flex flex-col w-full gap-3 sm:gap-4">
              <button
                onClick={() => router.push("/contact-us")}
                className="w-full bg-gradient-to-r from-[#FF8A00] to-[#E1804E] hover:from-[#e67e00] hover:to-[#c96d3f] text-white py-3 sm:py-3.5 rounded-xl font-bold text-xs sm:text-sm shadow-[0_0_20px_rgba(255,138,0,0.4)] active:scale-95 transition-all uppercase tracking-wider cursor-pointer"
              >
                {t("getAQuote")}
              </button>
              <button
                onClick={() => setIsContactModalOpen(true)}
                className="w-full bg-white/5 border border-white/10 hover:bg-white/10 text-white py-3 sm:py-3.5 rounded-xl font-bold text-xs sm:text-sm active:scale-95 transition-all uppercase tracking-wider"
              >
                {t("callUs")}
              </button>
            </div>
          </div>
        </div>

        {!isHomePage && (
          <motion.div
            style={{
              x: planetX,
              scale: planetScale,
              rotate: planetRotate,
              opacity: planetOpacity,
            }}
            className="absolute inset-0 pointer-events-none hidden lg:flex items-center justify-center h-screen z-10"
          >
            <div className="w-[300px] md:w-[450px] lg:w-[550px] aspect-square">
              <PlanetAstronaut />
            </div>
          </motion.div>
        )}

        <PricingModal
          isOpen={isPricingModalOpen}
          onClose={() => setIsPricingModalOpen(false)}
        />
        <ContactModal
          isOpen={isContactModalOpen}
          onClose={() => setIsContactModalOpen(false)}
          settings={settings}
        />
      </div>
    </section>
  );
};

export default CTA;
