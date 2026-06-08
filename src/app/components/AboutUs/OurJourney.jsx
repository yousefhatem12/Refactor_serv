"use client";

import React, { useLayoutEffect, useRef } from "react";
import Image from "next/image";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/dist/ScrollTrigger";
import { useLanguage } from "../../context/LanguageContext";
import { getMediaUrl } from "@/lib/api";

const OurJourney = ({ data }) => {
  const { t, language } = useLanguage();
  const containerRef = useRef(null);
  const timelineRef = useRef(null);
  const cardsRef = useRef([]);
  const mobileCardsRef = useRef([]);

  // Default hardcoded items
  const defaultItems = [
    {
      year: "2009",
      planet: "planet1.webp",
      location: t("locationEgypt"),
      users: "10",
      description: t("journeyDummyText"),
    },
    {
      year: "2011",
      planet: "planet-2.webp",
      location: t("locationEgypt"),
      users: "20",
      description: t("journeyDummyText"),
    },
    {
      year: "2013",
      planet: "planet-3.webp",
      location: t("locationEgyptSaudi"),
      users: "35",
      description: t("journeyDummyText"),
    },
    {
      year: "2015",
      planet: "planet-4.webp",
      location: t("locationEgyptSaudi"),
      users: "40",
      partnership: t("partnershipEtihad") || "Partnership",
      description: t("journeyDummyText"),
    },
    {
      year: "2017",
      planet: "planet-5.webp",
      location: t("locationEgyptSaudi"),
      users: "35",
      partnership: t("partnershipEtihad") || "Partnership",
      description: t("journeyDummyText"),
    },
    {
      year: "2020",
      planet: "planet-6.webp",
      location: t("locationEgyptSaudi"),
      users: "35",
      partnership: t("partnershipEtihad") || "Partnership",
      description: t("journeyDummyText"),
    },
    {
      year: "2024",
      planet: "planet-7.webp",
      location: t("locationEgyptSaudi"),
      users: "35",
      partnership: t("partnershipEtihad") || "Partnership",
      description: t("journeyDummyText"),
    },
  ];

  const isUsingDefaultData = !(data?.items?.length > 0);
  const dbItems = isUsingDefaultData ? defaultItems : data.items;

  const timelineStyles = [
    {
      yOffset: "translate-y-[60px] md:translate-y-[100px]",
      size: "w-[40px] h-[40px] md:w-[70px] md:h-[70px]",
    },
    {
      yOffset: "translate-y-[40px] md:translate-y-[60px]",
      size: "w-[50px] h-[50px] md:w-[90px] md:h-[90px]",
    },
    {
      yOffset: "translate-y-[20px] md:translate-y-[30px]",
      size: "w-[60px] h-[60px] md:w-[110px] md:h-[110px]",
    },
    {
      yOffset: "translate-y-0",
      size: "w-[80px] h-[80px] md:w-[130px] md:h-[130px]",
    },
    {
      yOffset: "-translate-y-[20px] md:-translate-y-[30px]",
      size: "w-[100px] h-[65px] md:w-[160px] md:h-[110px]",
      mix: true,
    },
    {
      yOffset: "-translate-y-[40px] md:-translate-y-[60px]",
      size: "w-[90px] h-[90px] md:w-[140px] md:h-[140px]",
    },
    {
      yOffset: "-translate-y-[60px] md:-translate-y-[100px]",
      size: "w-[110px] h-[110px] md:w-[160px] md:h-[160px]",
    },
  ];

  const timeline = dbItems.map((item, idx) => {
    const style = timelineStyles[idx % timelineStyles.length];
    const isCustomPlanet = item.planet && typeof item.planet === 'string' && (item.planet.startsWith('pages/') || item.planet.startsWith('http'));

    return {
      year: item.year,
      planet: isCustomPlanet ? getMediaUrl(item.planet) : `/about-us/planet${((idx % 7) + 1) === 1 ? '1' : `-${(idx % 7) + 1}`}.webp`,
      yOffset: style.yOffset,
      size: style.size,
      mix: style.mix,
      card: {
        location: item.location,
        users: item.users,
        partnership: item.partnership,
        description: item.description,
      },
    };
  });

  useLayoutEffect(() => {
    gsap.registerPlugin(ScrollTrigger);
    let mm = gsap.matchMedia();

    mm.add("(min-width: 768px)", () => {
      // Desktop Animation
      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: timelineRef.current,
          start: "center center",
          end: "+=1500",
          pin: true,
          scrub: 1,
          invalidateOnRefresh: true,
        },
      });

      cardsRef.current.forEach((card, index) => {
        if (card) {
          tl.fromTo(
            card,
            { opacity: 0, y: 80, scale: 0.8, filter: "blur(15px)" },
            {
              opacity: 1,
              y: 0,
              scale: 1,
              filter: "blur(0px)",
              duration: 1.5,
              ease: "back.out(1.7)",
            },
            index * 0.5,
          );
        }
      });
    });

    mm.add("(max-width: 767px)", () => {
      // Mobile Animation
      mobileCardsRef.current.forEach((card, index) => {
        if (card) {
          gsap.fromTo(
            card,
            { opacity: 0, x: index % 2 === 0 ? -50 : 50, scale: 0.9 },
            {
              opacity: 1,
              x: 0,
              scale: 1,
              duration: 1,
              scrollTrigger: {
                trigger: card,
                start: "top bottom-=100",
                toggleActions: "play none none reverse",
              },
            },
          );
        }
      });
    });

    return () => mm.revert();
  }, []);

  return (
    <section
      ref={containerRef}
      className="pt-16 md:pt-24 pb-24 w-full relative min-h-screen bg-transparent overflow-hidden"
    >
      <div className="max-w-7xl mx-auto flex flex-col items-center px-6 md:px-16 lg:px-24 z-30 relative text-center mb-8">
        <h2 className="text-4xl md:text-5xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-b from-[#FF8A00] to-[#FF5C00] mb-8 tracking-tighter uppercase text-cinematic animate-fade-in">
          {data?.title || t("aboutJourneyTag")}
        </h2>
        <p className="text-gray-400 text-base md:text-lg lg:text-xl font-light max-w-4xl mx-auto leading-relaxed whitespace-pre-line text-balance opacity-80">
          {data?.description || t("aboutJourneyDesc")}
        </p>
      </div>

      {/* Desktop Timeline */}
      <div
        ref={timelineRef}
        className="hidden md:flex relative z-10 w-full h-[1000px] lg:h-[1100px] justify-center items-center -mt-40 lg:-mt-56"
      >
        <div className="absolute left-[-300px] lg:left-[-400px] top-1/2 -translate-y-1/2 w-[600px] h-[600px] lg:w-[800px] lg:h-[800px] z-10 pointer-events-none opacity-50">
          <Image
            src="/Hero/orange-planet.webp"
            alt="Decoration"
            fill
            className="object-contain"
          />
        </div>
        <div className="absolute right-0 left-0 top-[-15%] w-[1500px] h-[1500px] z-5 opacity-40">
          <Image
            src="/about-us/orbit.webp"
            alt="Orbit"
            fill
            className="object-contain object-center"
          />
        </div>
        <div
          dir="ltr"
          className="relative z-40 w-full flex justify-between items-center px-12 md:px-32 lg:px-48 xl:px-64 max-w-[1200px] mx-auto"
        >
          {timeline.map((item, index) => (
            <div
              key={index}
              className={`flex flex-col items-center relative ${item.yOffset} transition-all duration-700 hover:scale-105`}
            >
              <div className="text-white font-black text-lg md:text-2xl mb-4 md:mb-6 tracking-tighter bg-white/5 px-6 py-2 rounded-2xl backdrop-blur-2xl border border-white/10 shadow-[0_0_20px_rgba(255,255,255,0.05)]">
                {item.year}
              </div>
              <div
                className={`relative ${item.size} ${item.mix ? "mix-blend-lighten" : ""} drop-shadow-[0_0_40px_rgba(255,255,255,0.2)]`}
              >
                <Image
                  src={item.planet}
                  alt={`Planet ${item.year}`}
                  fill
                  className="object-contain"
                />
              </div>
              <div
                ref={(el) => (cardsRef.current[index] = el)}
                className="absolute top-[110%] left-1/2 -translate-x-1/2 w-[140px] lg:w-[160px] z-50"
                style={{ opacity: 0 }}
              >
                <div
                  dir={language === "ar" ? "rtl" : "ltr"}
                  className="relative bg-[#0A0A0C]/95 backdrop-blur-3xl border border-white/10 p-3 rounded-2xl shadow-[0_40px_80px_rgba(0,0,0,0.9)] border-t-white/20 text-start"
                >
                  <div className="absolute -top-[8px] left-1/2 -translate-x-1/2 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-b-[10px] border-b-[#0A0A0C]"></div>
                  <div className="flex flex-col gap-1">
                    <span className="text-white text-xs font-bold">
                      {item.card.location}
                    </span>
                    <span className="text-white text-xs font-bold">
                      {item.card.users}
                    </span>
                    {item.card.partnership && (
                      <div className="p-1 bg-white/5 rounded-lg border border-white/5">
                        <span className="text-gray-200 text-[10px] font-bold uppercase tracking-wider">
                          {item.card.partnership}
                        </span>
                      </div>
                    )}
                    <div className="mt-0.5 pt-1 border-t border-white/10">
                      <p className="text-gray-400 text-[10px] italic line-clamp-3">
                        &quot;{item.card.description}&quot;
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Mobile Timeline (Vertical) */}
      <div
        dir="ltr"
        className="flex md:hidden flex-col items-center relative z-10 px-6 mt-12"
      >
        {/* Vertical Line */}
        <div className="absolute left-1/2 -translate-x-1/2 top-0 bottom-0 w-[2px] bg-gradient-to-b from-[#FF8A00] via-[#FF8A00]/20 to-transparent"></div>

        {timeline.map((item, index) => (
          <div
            key={index}
            ref={(el) => (mobileCardsRef.current[index] = el)}
            className={`flex flex-col w-full mb-16 relative ${index % 2 === 0 ? "items-end pr-[50%]" : "items-start pl-[50%]"}`}
          >
            {/* Year Badge on the Line */}
            <div className="absolute left-1/2 -translate-x-1/2 -top-4 z-20 bg-[#FF8A00] text-black text-[10px] font-bold px-3 py-1 rounded-full shadow-[0_0_15px_rgba(255,138,0,0.5)]">
              {item.year}
            </div>

            {/* Planet Decoration */}
            <div
              className={`absolute ${index % 2 === 0 ? "right-[55%]" : "left-[55%]"} top-4 w-12 h-12 opacity-80`}
            >
              <Image
                src={item.planet}
                alt={`Planet ${item.year}`}
                fill
                className="object-contain"
              />
            </div>

            {/* Info Card */}
            <div
              dir={language === "ar" ? "rtl" : "ltr"}
              className={`w-[160px] bg-[#0A0A0C]/80 backdrop-blur-xl border border-white/10 p-4 rounded-2xl shadow-2xl ${index % 2 === 0 ? "mr-4" : "ml-4"}`}
            >
              <div className="flex flex-col gap-2">
                <div className="flex items-center gap-2">
                  <div className="w-1.5 h-1.5 rounded-full bg-[#FF8A00]"></div>
                  <span className="text-white text-xs font-bold leading-tight">
                    {item.card.location}
                  </span>
                </div>
                <div className="flex items-center gap-2">
                  <div className="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                  <span className="text-white text-xs font-bold leading-tight">
                    {item.card.users}
                  </span>
                </div>
                {item.card.partnership && (
                  <div className="p-1.5 bg-white/5 rounded-lg border border-white/10">
                    <span className="text-[#FF8A00] text-[9px] font-extrabold uppercase tracking-widest">
                      {item.card.partnership}
                    </span>
                  </div>
                )}
                <div className="pt-2 border-t border-white/5">
                  <p className="text-gray-400 text-[10px] leading-relaxed italic">
                    &quot;{item.card.description}&quot;
                  </p>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
};

export default OurJourney;
