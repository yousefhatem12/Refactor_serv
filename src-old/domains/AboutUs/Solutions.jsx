"use client";
import React from "react";
import Image from "next/image";
import { useLanguage } from "@/app/context/LanguageContext";

const Solutions = () => {
  const { t } = useLanguage();
  return (
    <section className="py-20 px-6 md:px-16 lg:px-24 w-full relative">
      <div className="max-w-7xl mx-auto flex flex-col lg:flex-row items-stretch gap-12 lg:gap-20">
        
        {/* Left Side: Image */}
        <div className="w-full lg:w-1/2 relative flex justify-center lg:justify-start">
          <div className="relative w-full h-[300px] lg:h-auto">
            <div className="absolute inset-0 bg-purple-600/20 blur-[100px] rounded-full -z-10"></div>
            <Image
              src="/about-us/section-3-image.webp"
              alt="Innovative Solutions"
              fill
              className="object-cover [mask-image:radial-gradient(circle,black_0%,transparent_100%)]
    [-webkit-mask-image:radial-gradient(circle,black_0%,transparent_100%)]  mix-blend-screen  brightness-[130%] contrast-[120%] saturate-[130%]"
            />
          </div>
        </div>

        {/* Right Side: Text */}
        <div className="w-full lg:w-1/2 flex flex-col">
          <div className="flex items-center gap-4 mb-4">
            <div className="w-12 h-1 bg-[#FF8A00] rounded-full"></div>
            <span className="text-[#FF8A00] font-semibold tracking-wider uppercase text-sm">{t("aboutSolutionsTag")}</span>
          </div>
          <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
            {t("aboutSolutionsTitle")}
          </h2>
          <p className="text-gray-300 text-lg leading-relaxed mb-6">
            {t("aboutSolutionsDesc1")}
          </p>
          <p className="text-gray-400 text-base leading-relaxed">
            {t("aboutSolutionsDesc2")}
          </p>
        </div>

      </div>
    </section>
  );
};

export default Solutions;
