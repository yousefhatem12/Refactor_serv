"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import { useLanguage } from "@/app/context/LanguageContext";

export default function Earth({ isPhaseTwo }) {
  const { language } = useLanguage();
  const isRtl = language === "ar";
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    const checkMobile = () => setIsMobile(window.innerWidth < 768);
    checkMobile();
    window.addEventListener("resize", checkMobile);
    return () => window.removeEventListener("resize", checkMobile);
  }, []);

  const earthTransform = isPhaseTwo
    ? isMobile
      ? "translate3d(0, -20%, 0) scale(0.85)"
      : isRtl
      ? "translate3d(-25%, 0, 0) scale(1.2)"
      : "translate3d(25%, 0, 0) scale(1.2)"
    : isMobile
    ? "translate3d(0, 0, 0) scale(0.8)"
    : "translate3d(0, 0, 0) scale(0.6)";

  const astronautTransform = isPhaseTwo && !isMobile
    ? isRtl
      ? "translate3d(85%, 85%, 0) rotate(65deg) scale(1.5)"
      : "translate3d(-85%, 85%, 0) rotate(0deg) scale(1.5)"
    : isRtl
    ? "translate3d(-300%, 50%, 0) rotate(-45deg) scale(0.5)"
    : "translate3d(300%, 50%, 0) rotate(45deg) scale(0.5)";

  const astronautOpacity = isPhaseTwo && !isMobile ? 1 : 0;

  return (
    <div
      className="relative flex items-center justify-center w-full h-screen will-change-transform"
      style={{
        transform: earthTransform,
        transition: "transform 1.5s cubic-bezier(0.25, 0.1, 0.25, 1.0)",
      }}
    >
      <div className="relative w-[400px] h-[400px] lg:w-[400px] lg:h-[400px]">
        {/* Earth Background Glow */}
        <div className="absolute inset-0 rounded-full bg-blue-500/20 blur-[80px] animate-pulse overflow-hidden will-change-opacity hidden md:block" />

        {/* Earth Image - GPU Accelerated Continuous Rotation */}
        <div className="absolute overflow-hidden inset-0 z-10 will-change-transform hidden md:block animate-spin-slow">
          <Image
            src="/Hero/earth.webp"
            alt="Earth"
            width={400}
            height={400}
            priority
            sizes="(max-width: 768px) 100vw, 400px"
            className="w-full h-full object-contain"
          />
        </div>

        {/* Sunlight Layer - GPU Accelerated Continuous Sweep */}
        <div className="absolute inset-[5px] lg:inset-[5px] z-10 rounded-full overflow-hidden pointer-events-none hidden md:block">
          <div className="absolute inset-y-0 left-0 w-[200%] bg-[linear-gradient(to_right,rgba(0,0,0,0.8)_0%,rgba(0,0,0,0.8)_50%,transparent_65%)] mix-blend-multiply will-change-transform animate-sunlight" />
        </div>

        {/* Center Logo Overlay - GPU Accelerated Ambient Float */}
        <div className="absolute inset-0 flex items-center justify-center z-20 pointer-events-none">
          <div className="w-[100%] h-[100%] sm:w-[110%] sm:h-[110%] md:w-[40%] md:h-[40%] relative transition-all duration-700 animate-float-gentle">
            <Image
              src="/logo.svg"
              alt="Serv5 Logo"
              width={300}
              height={300}
              priority
              className="w-full h-full object-contain 
                         mix-blend-luminosity md:mix-blend-normal 
                         drop-shadow-[0_0_40px_rgba(255,255,255,0.8)] md:drop-shadow-[0_0_20px_rgba(255,255,255,0.5)] 
                         brightness-150 md:brightness-100"
            />
          </div>
        </div>

        {/* Astronaut - Phase 2 Entrance */}
        <div
          className={`absolute top-0 ${isRtl ? "left-0" : "right-0"} z-30 w-[100px] h-[100px] md:w-[130px] md:h-[130px] lg:w-[200px] lg:h-[200px] pointer-events-none hidden md:block will-change-transform`}
          style={{
            transform: astronautTransform,
            opacity: astronautOpacity,
            transition: "transform 1.5s cubic-bezier(0, 0, 0.2, 1) 0.3s, opacity 1.5s ease-out 0.3s",
          }}
        >
          <div
            style={{
              transform: isRtl ? "scaleX(-1) rotate(70deg)" : "none",
              width: "100%",
              height: "100%",
            }}
          >
            <Image
              src="/Hero/astronaut.webp"
              alt="Astronaut"
              width={200}
              height={200}
              loading="lazy"
              className="w-full h-full object-contain animate-float-astronaut"
            />
          </div>
        </div>
      </div>
    </div>
  );
}
