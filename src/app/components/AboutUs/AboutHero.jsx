"use client";
import React from "react";
import Image from "next/image";
import { useLanguage } from "../../context/LanguageContext";

const AboutHero = ({ data }) => {
  const { t } = useLanguage();
  return (
    <section className="relative w-full min-h-screen pt-32 pb-20 px-6 md:px-16 lg:px-24 flex flex-col justify-center">
      {/* Background Image */}
      <div className="absolute inset-0 z-0">
        <Image
          src={data?.hero_image || "/about-us/hero.png"}
          alt="Moon Surface Background"
          fill
          className="object-cover object-top opacity-80"
          priority
        />
        {/* Gradient Overlay for better text readability */}
        <div className="absolute inset-0 bg-gradient-to-b from-[#0A0A1A]/50 via-transparent to-[#0A0A1A] z-10" />
      </div>
 
      <div className="relative z-20 max-w-7xl mx-auto w-full">
        <div className="max-w-3xl mb-16">
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
            {data?.title || t("aboutHeroTitle")}
          </h1>
          <p className="text-gray-300 text-lg md:text-xl font-medium mb-4">
            {data?.subtitle || t("aboutHeroDesc")}
          </p>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-10">
          {/* Card 1 */}
          <div className="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 flex flex-col items-center text-center transform hover:-translate-y-2 transition-all duration-300 shadow-[0_8px_32px_rgba(0,0,0,0.3)]">
            <div className="w-16 h-16 relative mb-6">
              <Image src="/about-us/logo-1.png" alt="Experience" fill className="object-contain" />
            </div>
            <h3 className="text-xl font-bold text-white mb-3">{t("aboutStats1Title")}</h3>
            <p className="text-gray-400 text-sm leading-relaxed">
              {t("aboutStats1Desc")}
            </p>
          </div>

          {/* Card 2 */}
          <div className="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 flex flex-col items-center text-center transform hover:-translate-y-2 transition-all duration-300 shadow-[0_8px_32px_rgba(0,0,0,0.3)] md:-translate-y-8">
            <div className="w-16 h-16 relative mb-6">
              <Image src="/about-us/logo-2.png" alt="Satisfaction" fill className="object-contain" />
            </div>
            <h3 className="text-xl font-bold text-white mb-3">{t("aboutStats2Title")}</h3>
            <p className="text-gray-400 text-sm leading-relaxed">
              {t("aboutStats2Desc")}
            </p>
          </div>

          {/* Card 3 */}
          <div className="bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 flex flex-col items-center text-center transform hover:-translate-y-2 transition-all duration-300 shadow-[0_8px_32px_rgba(0,0,0,0.3)]">
            <div className="w-16 h-16 relative mb-6">
              <Image src="/about-us/logo-3.png" alt="Projects" fill className="object-contain" />
            </div>
            <h3 className="text-xl font-bold text-white mb-3">{t("aboutStats3Title")}</h3>
            <p className="text-gray-400 text-sm leading-relaxed">
              {t("aboutStats3Desc")}
            </p>
          </div>
        </div>
      </div>
    </section>
  );
};

export default AboutHero;
