"use client";
import React, { useRef, useState, useEffect } from "react";
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
  const slide1Ref = useRef(null);
  const slide2Ref = useRef(null);
  const localPlanetRef = useRef(null);

  const { t, language } = useLanguage();
  const isRtl = language === "ar";
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);
  const [isContactModalOpen, setIsContactModalOpen] = useState(false);
  const contextSettings = useSettings();
  const settings = contextSettings || {};

  useEffect(() => {
    let ticking = false;

    const mapRange = (val, inMin, inMax, outMin, outMax) => {
      if (val <= inMin) return outMin;
      if (val >= inMax) return outMax;
      return outMin + ((val - inMin) / (inMax - inMin)) * (outMax - outMin);
    };

    const updateCTA = () => {
      const container = containerRef.current;
      if (!container) return;

      const rect = container.getBoundingClientRect();
      const totalDist = container.offsetHeight - window.innerHeight;
      const progress = totalDist > 0 ? Math.min(Math.max(-rect.top / totalDist, 0), 1) : 0;

      // Slide 1
      if (slide1Ref.current) {
        const s1Opacity = mapRange(progress, 0, 0.35, 1, 0);
        const s1X = mapRange(progress, 0, 0.45, 0, isRtl ? 400 : -400);
        slide1Ref.current.style.opacity = `${s1Opacity}`;
        slide1Ref.current.style.transform = `translate3d(${s1X}px, 0, 0)`;
        slide1Ref.current.style.pointerEvents = progress > 0.35 ? "none" : "auto";
        slide1Ref.current.style.display = s1Opacity <= 0 ? "none" : "flex";
      }

      // Slide 2
      if (slide2Ref.current) {
        const s2Opacity = mapRange(progress, 0.35, 0.65, 0, 1);
        const s2Scale = mapRange(progress, 0.35, 0.65, 0.92, 1);
        const s2Y = mapRange(progress, 0.35, 0.65, 40, 0);
        slide2Ref.current.style.opacity = `${s2Opacity}`;
        slide2Ref.current.style.transform = `translate3d(0, ${s2Y}px, 0) scale(${s2Scale})`;
        slide2Ref.current.style.pointerEvents = progress < 0.35 ? "none" : "auto";
        slide2Ref.current.style.display = s2Opacity <= 0 ? "none" : "flex";
      }

      // Non-home local planet
      if (localPlanetRef.current) {
        const pX = isRtl ? mapRange(progress, 0.2, 0.75, 25, -45) : mapRange(progress, 0.2, 0.75, -25, 45);
        const pScale = mapRange(progress, 0.2, 0.75, 0.8, 1);
        const pRotate = mapRange(progress, 0.2, 0.75, 0, 10);
        let pOpacity = 0;
        if (progress <= 0.4) {
          pOpacity = mapRange(progress, 0.2, 0.4, 0.8, 1);
        } else if (progress <= 0.8) {
          pOpacity = 1;
        } else {
          pOpacity = mapRange(progress, 0.8, 1.0, 0.8, 0.35);
        }
        localPlanetRef.current.style.opacity = `${pOpacity}`;
        localPlanetRef.current.style.transform = `translate3d(${pX}%, 0, 0) scale(${pScale}) rotate(${pRotate}deg)`;
      }

      ticking = false;
    };

    const onScroll = () => {
      if (!ticking) {
        requestAnimationFrame(updateCTA);
        ticking = true;
      }
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll, { passive: true });
    updateCTA();

    return () => {
      window.removeEventListener("scroll", onScroll);
      window.removeEventListener("resize", onScroll);
    };
  }, [isRtl]);

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
        <div
          ref={slide1Ref}
          className="hidden md:flex relative md:absolute md:inset-0 flex-col md:flex-row items-center justify-center gap-8 md:gap-16 px-4 will-change-transform"
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
        </div>

        {/* Slide 2: Unlock Innovation (Desktop Only) */}
        <div
          ref={slide2Ref}
          className="hidden md:flex relative md:absolute z-20 md:inset-0 flex-col lg:flex-row items-center justify-center gap-0 px-6 md:px-16 lg:px-24 will-change-transform"
          style={{ opacity: 0, display: "none" }}
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
        </div>

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
          <div
            ref={localPlanetRef}
            className="absolute inset-0 pointer-events-none hidden lg:flex items-center justify-center h-screen z-10 will-change-transform"
            style={{ opacity: 0 }}
          >
            <div className="w-[300px] md:w-[450px] lg:w-[550px] aspect-square">
              <PlanetAstronaut />
            </div>
          </div>
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
