"use client";
import React, { useRef, useState, useEffect } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import { fetchAPI, getMediaUrl } from "@/lib/api";

const partners = [
  // "/Features/2-300x300-1.png.svg",
  // "/Features/5-300x300-1.png.svg",
  // "/Features/6-300x300-1.png.svg",
  // "/Features/clients-ksa-20-300x300-1.png.svg",
  // "/Features/clients-ksa-25-300x300-1.png.svg",
];

const checkFeaturesKeys = [
  // "techSolutions",
  // "digitalMarketing",
  // "webSolutions",
  // "businessGrowth",
  // "productDesign",
];

const Star = ({ className }) => (
  <svg viewBox="0 0 24 24" className={className}>
    <defs>
      <linearGradient id="star-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stopColor="#D98B50" />
        <stop offset="100%" stopColor="#f3a871" />
      </linearGradient>
    </defs>
    <path
      fill="url(#star-gradient)"
      d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
    />
  </svg>
);

export default function Features() {
  const sectionRef = useRef(null);
  const partnersRef = useRef(null);
  const { language, t } = useLanguage();
  // Use global settings from SSR context — no re-fetch needed
  const settings = useSettings();

  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(true);

  const checkScroll = () => {
    if (partnersRef.current) {
      const { scrollLeft, scrollWidth, clientWidth } = partnersRef.current;
      const maxScroll = scrollWidth - clientWidth;
      setCanScrollLeft(scrollLeft > 10);
      setCanScrollRight(scrollLeft < maxScroll - 10);
    }
  };

  const scrollLeft = () => {
    if (partnersRef.current) {
      partnersRef.current.scrollBy({ left: -200, behavior: "smooth" });
      setTimeout(checkScroll, 300);
    }
  };

  const scrollRight = () => {
    if (partnersRef.current) {
      partnersRef.current.scrollBy({ left: 200, behavior: "smooth" });
      setTimeout(checkScroll, 300);
    }
  };

  useEffect(() => {
    setTimeout(checkScroll, 300);
    window.addEventListener("resize", checkScroll);
    return () => window.removeEventListener("resize", checkScroll);
  }, [language, settings]);

  const [features, setFeatures] = useState([]);
  const fetchedRef = useRef(false);

  // Lazy-load /features only when section enters viewport (once per language change)
  useEffect(() => {
    fetchedRef.current = false; // reset on language change
  }, [language]);

  useEffect(() => {
    const el = sectionRef.current;
    if (!el) return;

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting && !fetchedRef.current) {
          fetchedRef.current = true;
          fetchAPI(`/features?lang=${language}`)
            .then((data) => {
              if (data && data.length > 0) setFeatures(data);
            })
            .catch(console.error);
          observer.disconnect();
        }
      },
      { rootMargin: "200px" }
    );

    observer.observe(el);
    return () => observer.disconnect();
  }, [language]);

  const displayFeatures =
    features.length > 0
      ? features.map((f) => f.title)
      : checkFeaturesKeys.map((k) => t(k));

  const displayPartners =
    settings?.features_partners && settings.features_partners.length > 0
      ? settings.features_partners.map((p) => getMediaUrl(p))
      : [];


  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : "/logo.svg";

  const [experienceImgSrc, setExperienceImgSrc] = useState(
    settings?.features_image ? getMediaUrl(settings.features_image) : "/Features/15Y.webp"
  );

  useEffect(() => {
    setExperienceImgSrc(
      settings?.features_image ? getMediaUrl(settings.features_image) : "/Features/15Y.webp"
    );
  }, [settings?.features_image, siteLogo]);

  return (
    <section
      ref={sectionRef}
      className="relative w-full min-h-screen  pt-20 md:py-24 overflow-hidden flex flex-col justify-center bg-transparent"
    >
      {/* Background Glowing Orbs for Creative Effect */}
      {/* <div className="absolute top-[10%] -left-[10%] w-[300px] md:w-[500px] h-[300px] md:h-[500px] bg-[#D98B50] rounded-full mix-blend-screen filter blur-[120px] md:blur-[150px] opacity-20 animate-pulse pointer-events-none"></div> */}
      <div className="absolute bottom-[10%] -right-[10%] w-[300px] md:w-[400px] h-[300px] md:h-[400px] bg-cyan-600 rounded-full mix-blend-screen filter blur-[120px] md:blur-[150px] opacity-10 pointer-events-none"></div>

      <div className="w-full max-w-7xl mx-auto px-6 relative z-20 flex-1 flex flex-col justify-center">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center w-full">
          {/* Left Side: Content */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, ease: "easeOut" }}
            viewport={{ once: true, margin: "-100px" }}
            className="flex flex-col relative z-10"
          >
            {/* 15Y Image for Mobile (Hidden on Desktop) */}
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              whileInView={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              viewport={{ once: true }}
              className="lg:hidden w-full max-w-[200px] sm:max-w-[250px] mx-auto mb-10 relative"
            >
              <div className="absolute inset-0 bg-[#D98B50] blur-[50px] opacity-20 rounded-full animate-pulse"></div>

              {/* Golden Stars for Royalty Effect */}
              <motion.div
                animate={{ rotate: 360, scale: [1, 1.2, 1] }}
                transition={{ duration: 4, repeat: Infinity, ease: "linear" }}
                className="absolute -left-4 top-4 z-20 drop-shadow-[0_0_10px_rgba(217,139,80,0.8)]"
              >
                <Star className="w-6 h-6" />
              </motion.div>
              <motion.div
                animate={{ rotate: -360, scale: [1, 1.3, 1] }}
                transition={{ duration: 5, repeat: Infinity, ease: "linear" }}
                className="absolute -right-2 top-1/2 z-20 drop-shadow-[0_0_10px_rgba(217,139,80,0.8)]"
              >
                <Star className="w-8 h-8" />
              </motion.div>
              <motion.div
                animate={{ rotate: 360, scale: [1, 1.1, 1] }}
                transition={{ duration: 3, repeat: Infinity, ease: "linear" }}
                className="absolute left-2 bottom-4 z-20 drop-shadow-[0_0_8px_rgba(243,168,113,0.8)]"
              >
                <Star className="w-4 h-4" />
              </motion.div>

              <img
                src={experienceImgSrc}
                alt="Who We Are"
                className={`w-full h-auto relative z-10 drop-shadow-[0_0_25px_rgba(217,139,80,0.4)] ${experienceImgSrc === siteLogo ? "object-contain max-h-[200px]" : "object-contain max-h-[220px] mx-auto"}`}
                onError={() => setExperienceImgSrc("/Features/15Y.webp")}
              />
            </motion.div>

            {/* Subtitle */}
            <div className="flex  items-center gap-3 mb-4 md:mb-6">
              <motion.span
                initial={{ width: 0 }}
                whileInView={{ width: "40px" }}
                transition={{ duration: 0.8, delay: 0.3 }}
                viewport={{ once: true }}
                className="h-[2px] bg-[#D98B50]"
              ></motion.span>
              <span className="text-[#D98B50] font-bold tracking-[0.2em] uppercase text-xs md:text-sm lg:text-3xl">
                {settings.features_subtitle || t("whoWeAre")}
              </span>
            </div>

            {/* Main Title */}
            <h2 className="text-3xl sm:text-4xl lg:text-5xl xl:text-5xl font-extrabold mb-6 leading-[1.2] md:leading-[1.1] text-white">
              {settings.features_title ? (
                <span
                  dangerouslySetInnerHTML={{ __html: settings.features_title }}
                />
              ) : (
                <>
                  {t("featuresTitlePart1")}
                  <span> </span>
                  {t("featuresTitlePart2")}
                  <span> </span>
                  <span className="text-transparent bg-clip-text bg-gradient-to-r py-4 from-[#D98B50] to-[#f3a871]">
                    {t("featuresTitlePart3")}
                  </span>
                  <span className="mt-4"> {t("featuresTitlePart4")}</span>
                </>
              )}
            </h2>

            {/* Description */}
            <p className="text-gray-400 text-sm md:text-base lg:text-lg mb-8 max-w-xl leading-relaxed font-light">
              {settings.features_desc || t("featuresDesc")}
            </p>

            {/* Features Grid/Flex */}
            <div className="grid grid-cols-2 sm:grid-cols-2   gap-3 md:gap-4">
              {displayFeatures.map((featureText, index) => (
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  key={index}
                  className="flex items-center gap-3 px-2 md:px-4 py-2.5 md:px-5 md:py-3 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 hover:border-[#D98B50]/50 hover:bg-white/10 transition-all duration-300 group shadow-lg"
                >
                  <div className="w-4 h-4 md:w-6 md:h-6 rounded-full bg-[#D98B50]/20 flex items-center justify-center group-hover:bg-[#D98B50] group-hover:shadow-[0_0_15px_rgba(217,139,80,0.6)] transition-all duration-300 shrink-0">
                    <svg
                      width="12"
                      height="12"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      className="text-[#D98B50] group-hover:text-white transition-colors duration-300"
                      strokeWidth="4"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </div>
                  <span className="text-gray-300 font-medium text-xs md:text-base group-hover:text-white transition-colors">
                    {featureText}
                  </span>
                </motion.div>
              ))}
            </div>
          </motion.div>

          {/* Right Side: 15Y Image (Desktop Only) */}
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            transition={{ duration: 1, ease: "easeOut" }}
            viewport={{ once: true, margin: "-100px" }}
            className="hidden lg:flex justify-center lg:justify-end relative"
          >
            <motion.div
              animate={{
                y: [-15, 15, -15],
              }}
              transition={{
                duration: 6,
                repeat: Infinity,
                ease: "easeInOut",
              }}
              className="relative w-full max-w-[400px] xl:max-w-[500px]"
            >
              {/* <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-3/4 bg-[#D98B50] blur-[80px] opacity-30 rounded-full animate-pulse"></div> */}

              {/* Golden Stars for Royalty Effect */}
              <motion.div
                animate={{ rotate: 360, scale: [1, 1.2, 1] }}
                transition={{ duration: 4, repeat: Infinity, ease: "linear" }}
                className="absolute -left-10 top-10 z-20 drop-shadow-[0_0_15px_rgba(217,139,80,0.8)]"
              >
                <Star className="w-10 h-10" />
              </motion.div>
              <motion.div
                animate={{ rotate: -360, scale: [1, 1.3, 1] }}
                transition={{ duration: 5, repeat: Infinity, ease: "linear" }}
                className="absolute -right-6 top-1/3 z-20 drop-shadow-[0_0_15px_rgba(217,139,80,0.8)]"
              >
                <Star className="w-12 h-12" />
              </motion.div>
              <motion.div
                animate={{ rotate: 360, scale: [1, 1.1, 1] }}
                transition={{ duration: 3.5, repeat: Infinity, ease: "linear" }}
                className="absolute left-10 bottom-10 z-20 drop-shadow-[0_0_10px_rgba(243,168,113,0.8)]"
              >
                <Star className="w-6 h-6" />
              </motion.div>
              <motion.div
                animate={{ rotate: -360, scale: [1, 1.2, 1] }}
                transition={{ duration: 4.5, repeat: Infinity, ease: "linear" }}
                className="absolute right-10 bottom-4 z-20 drop-shadow-[0_0_10px_rgba(217,139,80,0.8)]"
              >
                <Star className="w-8 h-8" />
              </motion.div>

              <img
                src={experienceImgSrc}
                alt="Who We Are"
                className={`w-full h-auto relative z-10 drop-shadow-[0_20px_50px_rgba(217,139,80,0.4)] ${experienceImgSrc === siteLogo ? "object-contain max-h-[350px] p-6 mx-auto" : "object-contain max-h-[420px] mx-auto"}`}
                onError={() => setExperienceImgSrc("/Features/15Y.webp")}
              />
            </motion.div>
          </motion.div>
        </div>

        {/* Partners Section */}
        {displayPartners && displayPartners.length > 0 && (
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.4 }}
            viewport={{ once: true }}
            className="w-full mt-20 lg:mt-32 pt-8 md:pt-12 border-t border-white/5 relative"
          >
          {/* Creative Top Border Glow */}
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 max-w-md h-[1px] bg-gradient-to-r from-transparent via-[#D98B50] to-transparent opacity-50"></div>

          <p className="text-center text-gray-500 mb-8 md:mb-12 font-medium tracking-widest uppercase text-[10px] md:text-xs">
            {t("trustedBy")}
          </p>

          <div className="flex items-center justify-between gap-4 max-w-6xl mx-auto px-2" dir="ltr">
            <button
              onClick={scrollLeft}
              disabled={!canScrollLeft}
              className={`hidden sm:flex w-10 h-10 md:w-12 md:h-12 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm items-center justify-center text-gray-400 transition-all duration-300 shrink-0 group ${
                canScrollLeft
                  ? "hover:text-white hover:bg-[#D98B50] hover:border-[#D98B50] hover:shadow-[0_0_20px_rgba(217,139,80,0.4)] cursor-pointer"
                  : "opacity-30 cursor-not-allowed"
              }`}
            >
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                className={canScrollLeft ? "group-hover:-translate-x-1 transition-transform duration-300" : ""}
              >
                <polyline points="15 18 9 12 15 6" />
              </svg>
            </button>

            <div className="flex-1 overflow-hidden">
              <div
                ref={partnersRef}
                onScroll={checkScroll}
                className="flex items-center justify-start lg:justify-center gap-6 sm:gap-8 md:gap-12 lg:gap-16 overflow-x-auto scrollbar-hide scroll-smooth snap-x snap-mandatory flex-nowrap"
                style={{ msOverflowStyle: "none", scrollbarWidth: "none" }}
              >
                {
                  displayPartners.map((partner, index) => (
                    <motion.div
                      key={index}
                      whileHover={{ scale: 1.1, y: -5 }}
                      transition={{ type: "spring", stiffness: 300 }}
                      className="relative group cursor-pointer p-2 shrink-0 snap-center"
                    >
                      <img
                        src={partner}
                        alt={`Partner ${index + 1}`}
                        className="h-12 sm:h-14 md:h-20 lg:h-24 w-auto object-contain opacity-60 group-hover:opacity-100 transition-all duration-300"
                        style={
                          partner.includes("/Features/")
                            ? { filter: "brightness(0) invert(1)" }
                            : {}
                        }
                      />
                      <div className="absolute -bottom-2 left-1/2 -translate-x-1/2 w-1/3 h-[2px] bg-[#D98B50] opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-[1px]"></div>
                    </motion.div>
                  ))
                }
              </div>
            </div>

            <button
              onClick={scrollRight}
              disabled={!canScrollRight}
              className={`hidden sm:flex w-10 h-10 md:w-12 md:h-12 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm items-center justify-center text-gray-400 transition-all duration-300 shrink-0 group ${
                canScrollRight
                  ? "hover:text-white hover:bg-[#D98B50] hover:border-[#D98B50] hover:shadow-[0_0_20px_rgba(217,139,80,0.4)] cursor-pointer"
                  : "opacity-30 cursor-not-allowed"
              }`}
            >
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                className={canScrollRight ? "group-hover:translate-x-1 transition-transform duration-300" : ""}
              >
                <polyline points="9 18 15 12 9 6" />
              </svg>
            </button>
          </div>
          </motion.div>
        )}
      </div>
      <style
        dangerouslySetInnerHTML={{
          __html: `
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
      `,
        }}
      />
    </section>
  );
}
