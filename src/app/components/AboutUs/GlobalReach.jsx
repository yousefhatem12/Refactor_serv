"use client";
import React, { useEffect, useRef } from "react";
import Image from "next/image";
import { useLanguage } from "../../context/LanguageContext";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/dist/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

const locations = [
  {
    id: "egypt",
    name: "Egypt",
    city: "Mansoura",
    details: "Suez canal above Dr.Rami pharmacy",
    x: 59,
    y: 42,
  },
  {
    id: "saudi",
    name: "Saudi Arabia",
    city: "Riyadh",
    details: "Headquarters",
    x: 63,
    y: 50,
  },
  {
    id: "uae",
    name: "UAE",
    city: "Dubai",
    details: "Innovation Hub",
    x: 66,
    y: 51,
  },
  {
    id: "india",
    name: "India",
    city: "Mumbai",
    details: "Tech Center",
    x: 76,
    y: 58,
  },
  {
    id: "australia",
    name: "Australia",
    city: "Sydney",
    details: "Growth Center",
    x: 89,
    y: 81,
  },
  {
    id: "usa",
    name: "USA",
    city: "New York",
    details: "Business Office",
    x: 18,
    y: 38,
  },
  {
    id: "brazil",
    name: "Brazil",
    city: "São Paulo",
    details: "Regional Center",
    x: 31,
    y: 74,
  },
  {
    id: "south-africa",
    name: "South Africa",
    city: "Cape Town",
    details: "Operations Hub",
    x: 55,
    y: 82,
  },
];

const GlobalReach = ({ data, minimal = false }) => {
  const { t, language } = useLanguage();
  const sectionRef = useRef(null);
  const dotsRef = useRef([]);

  const taglineText = data?.title || t("aboutGlobalTag") || "WE ARE HERE FOR YOU";
  const descriptionText = data?.description || t("aboutGlobalDesc") || "";

  const hasDbItems = data?.items && data.items.length > 0;
  const dbLocations = hasDbItems 
    ? data.items.map((item, idx) => {
        const defaultLoc = locations[idx % locations.length] || { x: 50, y: 50 };
        return {
          id: item.id || `loc-${idx}`,
          name: item.name || item.country,
          city: item.city,
          details: item.details,
          x: item.x || defaultLoc.x,
          y: item.y || defaultLoc.y,
        };
      })
    : locations;

  useEffect(() => {
    const ctx = gsap.context(() => {
      // Main timeline for the sequence
      const tl = gsap.timeline({
        repeat: -1, // Repeat infinitely
        scrollTrigger: {
          trigger: sectionRef.current,
          start: "top center+=100",
          toggleActions: "play pause resume pause",
        },
      });

      dotsRef.current.forEach((dotContainer, index) => {
        if (!dotContainer) return;

        const pulse = dotContainer.querySelector(".dot-pulse");
        const tooltip = dotContainer.querySelector(".tooltip-container");

        // Sequence for each dot
        tl.to(dotContainer, {
          opacity: 1,
          scale: 1.2,
          duration: 0.5,
          ease: "back.out(1.7)",
        })
          .to(
            tooltip,
            {
              opacity: 1,
              y: 0,
              duration: 0.4,
              ease: "power2.out",
            },
            "-=0.2",
          )
          // Pause and show
          .to({}, { duration: 2 })
          // Hide
          .to(tooltip, {
            opacity: 0,
            y: 10,
            duration: 0.4,
            ease: "power2.in",
          })
          .to(
            dotContainer,
            {
              opacity: 0,
              scale: 0,
              duration: 0.5,
              ease: "power2.in",
            },
            "-=0.2",
          );
      });
    }, sectionRef);

    return () => ctx.revert();
  }, [dbLocations]); // Re-run effect when locations change

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
            src="/about-us/Contents.png"
            alt="Global Reach Map"
            fill
            className="object-contain opacity-80"
          />

          {/* Sequentially Animated Dots */}
          {dbLocations.map((loc, index) => (
            <div
              key={loc.id}
              ref={(el) => (dotsRef.current[index] = el)}
              className="absolute z-10 opacity-0 scale-0"
              style={{ left: `${loc.x}%`, top: `${loc.y}%` }}
            >
              {/* Pulse Animation */}
              <div className="dot-pulse relative flex items-center justify-center">
                <div className="absolute w-4 h-4 md:w-6 md:h-6 bg-[#FF8A00] rounded-full animate-ping opacity-75"></div>
                <div className="relative w-3 h-3 md:w-4 md:h-4 bg-[#FF8A00] rounded-full shadow-[0_0_20px_rgba(255,138,0,0.9)] border-2 border-white/40"></div>
              </div>

              {/* Tooltip / Label */}
              <div className="tooltip-container absolute bottom-full left-1/2 -translate-x-1/2 mb-2 md:mb-4 opacity-0 translate-y-4 pointer-events-none whitespace-nowrap">
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
              </div>
            </div>
          ))}
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
