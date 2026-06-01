"use client";
import React, { useEffect, useRef, useState } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useLanguage } from "../../../context/LanguageContext";
import { fetchAPI } from "@/lib/api";
import PricingModal from "../../Shared/PricingModal";
import ContactModal from "../../Shared/ContactModal";
import { useRouter, usePathname } from "next/navigation";
import PlanetAstronaut from "../Shared/PlanetAstronaut";

gsap.registerPlugin(ScrollTrigger);

const CTA = () => {
  const router = useRouter();
  const pathname = usePathname();
  const isHomePage = pathname === "/" || pathname === "/Home";
  const sectionRef = useRef(null);
  const { t, language } = useLanguage();
  const slide1Ref = useRef(null);
  const slide2Ref = useRef(null);
  const planet1Ref = useRef(null);
  const text1Ref = useRef(null);
  const localPlanetRef = useRef(null);
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);
  const [isContactModalOpen, setIsContactModalOpen] = useState(false);

  const [settings, setSettings] = useState({});
  useEffect(() => {
    fetchAPI("/settings")
      .then((data) => {
        if (data) setSettings(data);
      })
      .catch(console.error);
  }, []);

  useEffect(() => {
    let ctx;

    const initGsap = () => {
      ctx = gsap.context(() => {
        const mm = gsap.matchMedia();
        const isRtl = language === "ar";

        mm.add("(min-width: 768px)", () => {
          const tl = gsap.timeline({
            scrollTrigger: {
              trigger: sectionRef.current,
              start: "top top",
              end: "+=150%", // How long the animation lasts relative to scroll
              scrub: 1,
              pin: true,
              anticipatePin: 1,
              invalidateOnRefresh: true, // Re-calculate triggers on page refreshes or updates
            },
          });

          // Split Slide 1
          tl.to(text1Ref.current, { x: -500, opacity: 0, duration: 1 }, 0)
            // Show Slide 2
            .fromTo(
              slide2Ref.current,
              { opacity: 0, scale: 0.9, y: 50 },
              { opacity: 1, scale: 1, y: 0, duration: 1 },
              0.5,
            );

          // Local planet animation on non-home pages
          if (!isHomePage && localPlanetRef.current) {
            gsap.set(localPlanetRef.current, {
              x: isRtl ? "25%" : "-25%",
              opacity: 0.8,
              scale: 0.8,
              rotation: 0,
            });

            tl.to(
              localPlanetRef.current,
              {
                x: isRtl ? "-45%" : "45%",
                scale: 1,
                rotation: 10,
                duration: 1,
              },
              0.2,
            ).to(
              localPlanetRef.current,
              {
                opacity: 0.35,
                duration: 0.5,
              },
              1.0,
            );
          }
        });
      }, sectionRef);

      // Force refresh to map the newly created scroll triggers correctly
      ScrollTrigger.refresh();
    };

    // Delay initialization to allow the page layout and dynamic data to fully load/render
    initGsap();

    // Ensure ScrollTrigger recalculates if the body height changes
    // (e.g. when the initial h-screen loading lock is removed)
    const resizeObserver = new ResizeObserver(() => {
      ScrollTrigger.refresh();
    });
    resizeObserver.observe(document.body);

    return () => {
      resizeObserver.disconnect();
      if (ctx) ctx.revert();
    };
  }, [language, isHomePage]);

  return (
    <section
      ref={sectionRef}
      className="relative md:min-h-screen w-full overflow-hidden flex flex-col md:flex-row md:items-center md:justify-center bg-transparent z-10 py-16 md:py-0"
    >
      {/* Background Decorative Elements (Static) */}
      <div className="absolute top-1/4 right-0 w-[500px] h-[500px] bg-blue-600/5 blur-[150px] rounded-full -z-10 hidden md:block"></div>
      <div className="absolute bottom-1/4 left-0 w-[500px] h-[500px] bg-orange-600/5 blur-[150px] rounded-full -z-10 hidden md:block"></div>

      {/* Slide 1: Ready For Liftoff (Desktop Only) */}
      <div
        ref={slide1Ref}
        className="hidden md:flex relative md:absolute md:inset-0 flex-col md:flex-row items-center justify-center gap-8 md:gap-16 px-4"
      >
        {/* Planet Side (Placeholder for layout) */}
        <div
          ref={planet1Ref}
          className="relative w-[280px] md:w-[450px] lg:w-[550px] aspect-square hidden md:flex items-center justify-center"
        >
          {/* We keep this div for flexbox spacing but remove the image */}
          <div className="w-full h-full rounded-full bg-orange-500/5 blur-[80px]"></div>
        </div>

        {/* Text Side */}
        <div
          ref={text1Ref}
          className={`text-center flex flex-col items-center w-full md:w-auto ${language === "ar"
              ? "md:items-end md:text-right"
              : "md:items-start md:text-left"
            }`}
        >
          <h2 className="w-full text-3xl md:text-5xl lg:text-6xl xl:text-7xl font-black text-white mb-4 md:mb-6 leading-[1.1] tracking-tighter">
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

      {/* Slide 2: Unlock Innovation (Screenshot Design - Desktop Only) */}
      <div
        ref={slide2Ref}
        className="hidden md:flex relative md:absolute z-20 md:inset-0 opacity-100 md:opacity-0 flex-col lg:flex-row items-center justify-center gap-12 px-6 md:px-16 lg:px-24"
      >
        {/* Left Side: Content */}
        <div
          className={`w-full lg:w-1/2 flex flex-col items-center ${language === "ar"
              ? "lg:items-end lg:text-right"
              : "lg:items-start lg:text-left"
            } text-center z-20`}
        >
          <h2 className="w-full text-2xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-6 md:mb-8 leading-tight">
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
          <div className="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
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
            {/* Subtle Glow - kept to maintain appearance during landing */}
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
          className="absolute inset-0 pointer-events-none hidden lg:flex items-center justify-center h-screen z-10"
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
    </section>
  );
};

export default CTA;
