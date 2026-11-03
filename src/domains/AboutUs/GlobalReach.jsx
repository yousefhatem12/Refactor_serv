"use client";
import React, { useState, useEffect, useRef } from "react";
import Image from "next/image";
import { motion, useInView } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const locations = [
  {
    id: "egypt",
    name: "Egypt",
    city: "Mansoura",
    details: "Suez canal above Dr.Rami pharmacy",
    x: 53.5,
    y: 45,
  },
];

const GlobalReach = ({ data, minimal = false }) => {
  const { t, language } = useLanguage();
  const sectionRef = useRef(null);
  const isInView = useInView(sectionRef, { margin: "-50px" });
  const [activeDotIndex, setActiveDotIndex] = useState(0);

  const taglineText = data?.title || t("aboutGlobalTag") || "WE ARE HERE FOR YOU";
  const descriptionText = data?.description || t("aboutGlobalDesc") || "";

  const hasDbItems = data?.items && data.items.length > 0;
  const dbLocations = hasDbItems
    ? data.items.map((item, idx) => {
        const defaultLoc = locations[idx % locations.length] || { x: 40, y: 40 };
        return {
          id: item.id || `loc-${idx}`,
          name: item.name || item.country,
          city: item.city,
          details: item.details,
          x: item.x || defaultLoc.x,
          y: item.y || defaultLoc.y,
        };
      })
    : locations.map((loc) => ({
        ...loc,
        name: language === "ar" ? t("locationEgypt") : loc.name,
        city: language === "ar" ? "المنصورة" : loc.city,
        details:
          language === "ar"
            ? "شارع قناة السويس - أعلى صيدلية د. رامي"
            : loc.details,
      }));

  useEffect(() => {
    if (!isInView || dbLocations.length === 0) return;

    const interval = setInterval(() => {
      setActiveDotIndex((prev) => (prev + 1) % dbLocations.length);
    }, 3500);

    return () => clearInterval(interval);
  }, [isInView, dbLocations.length]);

  return (
    <section
      ref={sectionRef}
      className={
        minimal
          ? "w-full relative"
          : "py-20 px-6 md:px-16 lg:px-24 w-full relative overflow-hidden"
      }
    >
      <div
        className={
          minimal
            ? "w-full flex flex-col items-center"
            : "max-w-7xl mx-auto flex flex-col items-center"
        }
      >
        {/* Header Content */}
        {!minimal && (
          <div className="text-center max-w-3xl mb-16">
            <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-[#FF8A00] mb-6 tracking-wide">
              {taglineText}
            </h2>
            {descriptionText && (
              <p className="text-gray-300 text-lg md:text-xl font-medium leading-relaxed">
                {descriptionText}
              </p>
            )}
          </div>
        )}

        {/* Map Container */}
        <div
          dir="ltr"
          className={
            minimal
              ? "relative w-full aspect-[4/3] md:aspect-[2/1]"
              : "relative w-full max-w-6xl aspect-[4/3] md:aspect-[2/1] mt-8"
          }
        >
          <div className="absolute inset-0 bg-blue-500/5 blur-[120px] rounded-full -z-10"></div>
          <Image
            src="/about-us/Contents.webp"
            alt="Global Reach Map"
            fill
            sizes="(max-width: 1152px) 100vw, 1152px"
            className="object-contain opacity-80"
          />

          {/* Sequentially Animated Dots via Framer Motion */}
          {dbLocations.map((loc, index) => {
            const isActive = activeDotIndex === index;
            return (
              <motion.div
                key={loc.id}
                className="absolute z-10"
                style={{ left: `${loc.x}%`, top: `${loc.y}%` }}
                initial={{ opacity: 0, scale: 0 }}
                animate={
                  isActive
                    ? { opacity: 1, scale: 1.2 }
                    : { opacity: 0, scale: 0 }
                }
                transition={{ duration: 0.5, ease: "easeOut" }}
              >
                {/* Pulse Animation */}
                <div className="dot-pulse relative flex items-center justify-center">
                  <div className="absolute w-4 h-4 md:w-6 md:h-6 bg-[#FF8A00] rounded-full animate-ping opacity-75"></div>
                  <div className="relative w-3 h-3 md:w-4 md:h-4 bg-[#FF8A00] rounded-full shadow-[0_0_20px_rgba(255,138,0,0.9)] border-2 border-white/40"></div>
                </div>

                {/* Tooltip / Label */}
                <motion.div
                  className="tooltip-container absolute bottom-full left-1/2 -translate-x-1/2 mb-2 md:mb-4 pointer-events-none whitespace-nowrap"
                  initial={{ opacity: 0, y: 10 }}
                  animate={
                    isActive ? { opacity: 1, y: 0 } : { opacity: 0, y: 10 }
                  }
                  transition={{
                    duration: 0.4,
                    delay: isActive ? 0.2 : 0,
                    ease: "easeOut",
                  }}
                >
                  <div
                    dir={language === "ar" ? "rtl" : "ltr"}
                    className="bg-white text-[#0A0F1A] px-3 py-2 md:px-5 md:py-3 rounded-xl md:rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.3)] relative border border-gray-100"
                  >
                    <div className="flex items-center gap-2 md:gap-4">
                      <div className="w-2 h-2 md:w-3 md:h-3 bg-[#FF8A00] rounded-full shadow-inner"></div>
                      <div>
                        <p className="text-[10px] md:text-sm font-extrabold leading-tight tracking-tight">
                          {loc.city}, {loc.name}
                        </p>
                        <p className="text-[8px] md:text-[11px] text-gray-500 mt-0.5 md:mt-1 font-medium">
                          {loc.details}
                        </p>
                      </div>
                    </div>
                    {/* Decorative Arrow */}
                    <div className="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-[6px] md:border-l-[10px] border-l-transparent border-r-[6px] md:border-r-[10px] border-r-transparent border-t-[6px] md:border-t-[10px] border-t-white"></div>
                  </div>
                </motion.div>
              </motion.div>
            );
          })}
        </div>
      </div>

      <style jsx>{`
        @keyframes ping {
          75%,
          100% {
            transform: scale(3.5);
            opacity: 0;
          }
        }
        .animate-ping {
          animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
      `}</style>
    </section>
  );
};

export default GlobalReach;
