"use client";
import React from "react";
import { motion, useTransform, useMotionValue } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const PlanetAstronaut = ({ className = "", progress, ...props }) => {
  const { language } = useLanguage();
  const isRtl = language === "ar";

  const fallbackProgress = useMotionValue(0);
  const p = progress || fallbackProgress;

  return (
    <div
      className={`relative flex items-center justify-center ${className}`}
      {...props}
    >
      {/* The Planet & Astronaut Wrapper */}
      <motion.div className="relative w-full aspect-square flex items-center justify-center">
        <div className="relative w-full aspect-square">
          <img
            src="/Hero/orange-planet.webp"
            alt="Planet"
            width={550}
            height={550}
            className="w-full h-full object-contain drop-shadow-[0_0_80px_rgba(255,138,0,0.4)] animate-[spin_60s_linear_infinite]"
          />
          {/* Subtle Glow */}
          <div className="absolute inset-0 bg-orange-500/10 blur-[100px] rounded-full -z-10"></div>
        </div>

        {/* The Astronaut */}
        <div
          className="absolute top-1/2 left-0 w-[80%] h-auto z-10"
          style={{ transform: isRtl ? "scaleX(-1)" : "none" }}
        >
          <motion.img
            src="/Hero/astronaut.webp"
            alt="Astronaut"
            width={440}
            height={440}
            className="w-full h-full object-contain"
            animate={{
              x: ["-50%", "-52%", "-50%"],
              y: ["-50%", "-55%", "-50%"],
              rotate: [0, 5, 0],
            }}
            transition={{
              duration: 6,
              repeat: Infinity,
              ease: "easeInOut",
            }}
          />
        </div>
      </motion.div>
    </div>
  );
};

export default PlanetAstronaut;
