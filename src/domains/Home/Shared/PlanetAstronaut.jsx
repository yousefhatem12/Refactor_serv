"use client";
import React from "react";
import Image from "next/image";
import { useLanguage } from "@/app/context/LanguageContext";

const PlanetAstronaut = ({ className = "", ...props }) => {
  const { language } = useLanguage();
  const isRtl = language === "ar";

  return (
    <div
      className={`relative flex items-center justify-center ${className}`}
      {...props}
    >
      {/* The Planet & Astronaut Wrapper */}
      <div className="relative w-full aspect-square flex items-center justify-center">
        <div className="relative w-full aspect-square">
          <Image
            src="/Hero/orange-planet.webp"
            alt="Planet"
            width={550}
            height={550}
            priority
            sizes="(max-width: 768px) 100vw, 550px"
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
          <div className="w-full h-full animate-astronaut-float-subtle">
            <Image
              src="/Hero/astronaut.webp"
              alt="Astronaut"
              width={440}
              height={440}
              loading="lazy"
              sizes="(max-width: 768px) 80vw, 440px"
              className="w-full h-full object-contain"
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default PlanetAstronaut;
