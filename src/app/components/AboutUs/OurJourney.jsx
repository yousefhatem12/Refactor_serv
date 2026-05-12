"use client";

import React, { useLayoutEffect, useRef } from "react";
import Image from "next/image";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/dist/ScrollTrigger";
import { FaMapMarkerAlt, FaUsers, FaTrophy } from "react-icons/fa";
import { useLanguage } from "../../context/LanguageContext";
import { getMediaUrl } from "@/lib/api";

const OurJourney = ({ data }) => {
  const { t } = useLanguage();
  const containerRef = useRef(null);
  const timelineRef = useRef(null);
  const cardsRef = useRef([]);

  // Default hardcoded items in case data is not provided or empty
  const defaultItems = [
    { year: "2009", planet: "planet1.png", location: t("locationEgypt"), users: "10", description: t("journeyDummyText") },
    { year: "2011", planet: "planet-2.png", location: t("locationEgypt"), users: "20", description: t("journeyDummyText") },
    { year: "2013", planet: "planet-3.png", location: t("locationEgyptSaudi"), users: "35", description: t("journeyDummyText") },
    { year: "2015", planet: "planet-4.png", location: t("locationEgyptSaudi"), users: "40", partnership: t("partnershipEtihad"), description: t("journeyDummyText") },
    { year: "2017", planet: "planet-5.png", location: t("locationEgyptSaudi"), users: "35", partnership: t("partnershipEtihad"), description: t("journeyDummyText") },
    { year: "2020", planet: "planet-6.png", location: t("locationEgyptSaudi"), users: "35", partnership: t("partnershipEtihad"), description: t("journeyDummyText") },
    { year: "2024", planet: "planet-7.png", location: t("locationEgyptSaudi"), users: "35", partnership: t("partnershipEtihad"), description: t("journeyDummyText") },
  ];

  const dbItems = data?.items?.length > 0 ? data.items : defaultItems;

  const timelineStyles = [
    { yOffset: "translate-y-[40px] md:translate-y-[80px]", size: "w-[40px] h-[40px] md:w-[60px] md:h-[60px]" },
    { yOffset: "translate-y-[20px] md:translate-y-[40px]", size: "w-[50px] h-[50px] md:w-[80px] md:h-[80px]" },
    { yOffset: "translate-y-0", size: "w-[60px] h-[60px] md:w-[100px] md:h-[100px]" },
    { yOffset: "-translate-y-[20px] md:-translate-y-[40px]", size: "w-[80px] h-[80px] md:w-[120px] md:h-[120px]" },
    { yOffset: "-translate-y-[40px] md:-translate-y-[80px]", size: "w-[110px] h-[70px] md:w-[170px] md:h-[110px]", mix: true },
    { yOffset: "-translate-y-[60px] md:-translate-y-[120px]", size: "w-[90px] h-[90px] md:w-[140px] md:h-[140px]" },
    { yOffset: "-translate-y-[80px] md:-translate-y-[160px]", size: "w-[110px] h-[110px] md:w-[160px] md:h-[160px]" },
  ];

  const timeline = dbItems.map((item, idx) => {
    const style = timelineStyles[idx % timelineStyles.length];
    return {
      year: item.year,
      planet: item.planet ? getMediaUrl(item.planet) : `/about-us/planet${(idx % 7) + 1}.png`,
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

    const ctx = gsap.context(() => {
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

      // Animate planets arrival first (optional but looks nice)
      // For now let's focus on the cards appearance as requested

      cardsRef.current.forEach((card, index) => {
        if (card) {
          tl.fromTo(
            card,
            {
              opacity: 0,
              y: 80,
              scale: 0.8,
              filter: "blur(15px)",
            },
            {
              opacity: 1,
              y: 0,
              scale: 1,
              filter: "blur(0px)",
              duration: 1.5,
              ease: "back.out(1.7)",
            },
            index * 0.5, // Slower stagger for more control
          );
        }
      });
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={containerRef}
      className="py-24 w-full relative min-h-screen bg-transparent"
    >
      <div className="max-w-7xl mx-auto flex flex-col items-center px-6 md:px-16 lg:px-24 z-30 relative text-center mb-16">
        <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-[#FF8A00] mb-6 tracking-wide uppercase">
          {data?.title || t("aboutJourneyTag")}
        </h2>
        <p className="text-gray-300 text-sm md:text-base font-normal max-w-4xl mx-auto leading-relaxed whitespace-pre-line">
          {data?.description || t("aboutJourneyDesc")}
        </p>
      </div>

      {/* Timeline Visualization */}
      
      <div
        ref={timelineRef}
        className="relative z-10 w-full h-[800px] md:h-[1000px] lg:h-[1100px]  flex justify-center items-center"
      >
        {/* Large Left Planet Background Decoration */}
        <div className="absolute left-[-150px] md:left-[-300px] z-20 lg:left-[-400px] top-1/2 -translate-y-1/2 w-[300px] h-[300px] md:w-[600px] md:h-[600px] lg:w-[800px] lg:h-[800px] z-10 pointer-events-none">
          <Image
            src="/Hero/orange-planet.png"
            alt="Decoration"
            fill
            className="object-contain"
          />
        </div>

        {/* Orbit Line */}
        <div className="absolute right-0 left-0 top-[-15%] w-[1500px] h-[1500px] z-5 hidden md:block opacity-40">
          <Image
            src="/about-us/orbit.png"
            alt="Orbit"
            fill
            className="object-contain object-center"
          />
        </div>

        {/* Timeline Nodes */}
        <div className="relative z-40 w-full flex justify-between items-center px-4 md:px-12 lg:px-24 xl:px-48 max-w-[2400px] mx-auto">
          {timeline.map((item, index) => (
            <div
              key={index}
              className={`flex flex-col items-center relative ${item.yOffset} transition-transform duration-700`}
            >
              <div className="text-gray-200 font-bold text-sm md:text-lg mb-4 md:mb-6 tracking-widest bg-black/60 px-4 py-1.5 rounded-full backdrop-blur-md border border-white/5">
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

              {/* Card Animation Container */}
              <div
                ref={(el) => (cardsRef.current[index] = el)}
                className="absolute top-[115%] left-1/2 -translate-x-1/2 w-[180px] md:w-[220px] lg:w-[260px] xl:w-[180px] z-50"
                style={{ opacity: 0 }}
              >
                <div className="relative bg-[#0A0A0C]/95 backdrop-blur-3xl border border-white/10 p-4 md:p-5 rounded-[1.5rem] md:rounded-[2rem] shadow-[0_40px_80px_rgba(0,0,0,0.9)] border-t-white/20">
                  {/* Luxury Glow */}
                  <div className="absolute top-0 left-1/4 w-1/2 h-[2px] bg-gradient-to-r from-transparent via-[#FF8A00] to-transparent shadow-[0_0_15px_#FF8A00]"></div>

                  {/* Triangle Pointer */}
                  <div className="absolute -top-[10px] left-1/2 -translate-x-1/2 w-0 h-0 border-l-[12px] border-l-transparent border-r-[12px] border-r-transparent border-b-[12px] border-b-white/10"></div>
                  <div className="absolute -top-[8px] left-1/2 -translate-x-1/2 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-b-[10px] border-b-[#0A0A0C]"></div>

                  <div className="flex flex-col gap-3 md:gap-4 text-left">
                    <div className="flex items-center gap-2 md:gap-3">
                      <div className="w-8 h-8 md:w-10 md:h-10 rounded-xl md:rounded-2xl bg-[#FF8A00]/10 flex items-center justify-center border border-[#FF8A00]/20 shadow-inner shrink-0">
                        <FaMapMarkerAlt className="text-[#FF8A00] text-sm md:text-base" />
                      </div>
                      <span className="text-white text-xs md:text-sm font-bold leading-tight">
                        {item.card.location}
                      </span>
                    </div>

                    <div className="flex items-center gap-2 md:gap-3">
                      <div className="w-8 h-8 md:w-10 md:h-10 rounded-xl md:rounded-2xl bg-[#FF8A00]/10 flex items-center justify-center border border-[#FF8A00]/20 shadow-inner shrink-0">
                        <FaUsers className="text-[#FF8A00] text-sm md:text-base" />
                      </div>
                      <span className="text-white text-xs md:text-sm font-bold leading-tight">
                        {item.card.users}
                      </span>
                    </div>

                    {item.card.partnership && (
                      <div className="flex items-start gap-2 md:gap-3 p-2 md:p-2.5 bg-white/5 rounded-xl md:rounded-2xl border border-white/5">
                        <div className="w-7 h-7 md:w-8 md:h-8 rounded-lg md:rounded-xl bg-[#FF8A00] flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(255,138,0,0.4)]">
                          <FaTrophy className="text-white text-xs md:text-sm" />
                        </div>
                        <span className="text-gray-200 text-[10px] md:text-xs font-bold pt-0.5 uppercase tracking-wider leading-tight">
                          {item.card.partnership}
                        </span>
                      </div>
                    )}

                    <div className="mt-1 pt-3 border-t border-white/10">
                      <p className="text-gray-400 text-[10px] md:text-xs leading-relaxed font-normal italic">
                        "{item.card.description}"
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default OurJourney;
