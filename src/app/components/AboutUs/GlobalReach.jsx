"use client";
import React from "react";
import Image from "next/image";
import { useLanguage } from "../../context/LanguageContext";

const GlobalReach = () => {
  const { t } = useLanguage();
  return (
    <section className="py-20 px-6 md:px-16 lg:px-24 w-full relative overflow-hidden">
      <div className="max-w-7xl mx-auto flex flex-col items-center">
        
        {/* Header Content */}
        <div className="text-center max-w-3xl mb-16">
          <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-[#FF8A00] mb-6 tracking-wide">
            {t("aboutGlobalTag")}
          </h2>
          <p className="text-gray-300 text-lg md:text-xl font-medium leading-relaxed">
            {t("aboutGlobalDesc")}
          </p>
        </div>

        {/* Map Image */}
        <div className="relative w-full max-w-6xl aspect-[2/1] mt-8">
          <div className="absolute inset-0 bg-blue-500/10 blur-[120px] rounded-full -z-10"></div>
          <Image
            src="/about-us/WorldMap.png"
            alt="Global Reach Map"
            fill
            className="object-contain"
          />
        </div>

      </div>
    </section>
  );
};

export default GlobalReach;
