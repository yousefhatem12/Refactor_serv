"use client";
import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
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

  return (
    <motion.div
      className="relative flex items-center justify-center w-full h-screen will-change-transform"
      animate={{
        x: isPhaseTwo ? (isMobile ? "0%" : isRtl ? "-25%" : "25%") : "0%",
        y: isPhaseTwo ? (isMobile ? "-20%" : "0%") : "0%",
        scale: isPhaseTwo ? (isMobile ? 0.85 : 1.2) : isMobile ? 0.8 : 0.6,
      }}
      transition={{ duration: 1.5, ease: [0.25, 0.1, 0.25, 1.0] }}
    >
      <div className="relative w-[400px] h-[400px] lg:w-[400px] lg:h-[400px]">
        {/* Earth Background Glow */}
        <div className="absolute inset-0 rounded-full bg-blue-500/20 blur-[80px] animate-pulse overflow-hidden will-change-opacity hidden md:block" />

        {/* Earth Image - GPU Accelerated Continuous Rotation */}
        <div className="absolute overflow-hidden inset-0 z-10 will-change-transform hidden md:block animate-spin-slow">
          <img
            src="/Hero/earth.webp"
            alt="Earth"
            width={800}
            height={800}
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
            <img
              src="/logo.svg"
              alt="Serv5 Logo"
              width={300}
              height={300}
              className="w-full h-full object-contain 
                         mix-blend-luminosity md:mix-blend-normal 
                         drop-shadow-[0_0_40px_rgba(255,255,255,0.8)] md:drop-shadow-[0_0_20px_rgba(255,255,255,0.5)] 
                         brightness-150 md:brightness-100"
            />
          </div>
        </div>

        {/* Astronaut - Phase 2 Entrance */}
        <motion.div
          className={`absolute top-0 ${isRtl ? "left-0" : "right-0"} z-30 w-[100px] h-[100px] md:w-[130px] md:h-[130px] lg:w-[200px] lg:h-[200px] pointer-events-none hidden md:block`}
          initial={false}
          animate={
            isPhaseTwo && !isMobile
              ? {
                  x: isRtl ? "85%" : "-85%",
                  y: "85%",
                  opacity: 1,
                  rotate: isRtl ? 65 : 0,
                  scale: 1.5,
                }
              : {
                  opacity: 0,
                  x: isRtl ? "-300%" : "300%",
                  y: "50%",
                  scale: 0.5,
                  rotate: isRtl ? -45 : 45,
                }
          }
          transition={{
            duration: 1.5,
            delay: isPhaseTwo ? 0.3 : 0,
            ease: "easeOut",
          }}
        >
          <div
            style={{
              transform: isRtl ? "scaleX(-1) rotate(70deg)" : "none",
              width: "100%",
              height: "100%",
            }}
          >
            <img
              src="/Hero/astronaut.webp"
              alt="Astronaut"
              width={200}
              height={200}
              className="w-full h-full object-contain animate-float-astronaut"
            />
          </div>
        </motion.div>
      </div>
    </motion.div>
  );
}
