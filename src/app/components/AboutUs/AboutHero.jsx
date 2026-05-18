"use client";
import React from "react";
import Image from "next/image";
import { useLanguage } from "../../context/LanguageContext";
import { HiOutlineCheckCircle, HiOutlineUserGroup, HiOutlineTrophy, HiOutlineClock, HiOutlineStar, HiOutlineLightBulb, HiOutlineRocketLaunch } from "react-icons/hi2";

const getIcon = (iconName) => {
  switch (iconName) {
    case 'heroicon-o-check-circle': return <HiOutlineCheckCircle className="w-12 h-12 text-[#FF8A00]" />;
    case 'heroicon-o-user-group': return <HiOutlineUserGroup className="w-12 h-12 text-[#FF8A00]" />;
    case 'heroicon-o-trophy': return <HiOutlineTrophy className="w-12 h-12 text-[#FF8A00]" />;
    case 'heroicon-o-clock': return <HiOutlineClock className="w-12 h-12 text-[#FF8A00]" />;
    case 'heroicon-o-light-bulb': return <HiOutlineLightBulb className="w-12 h-12 text-[#FF8A00]" />;
    case 'heroicon-o-rocket-launch': return <HiOutlineRocketLaunch className="w-12 h-12 text-[#FF8A00]" />;
    default: return <HiOutlineStar className="w-12 h-12 text-[#FF8A00]" />;
  }
}

const AboutHero = ({ data }) => {
  const { t } = useLanguage();
  const statsSection = data?.sections?.find(s => s.type === 'stats');
  const statsItems = statsSection?.data?.items && statsSection.data.items.length > 0
    ? statsSection.data.items 
    : [
        { value: t("aboutStats1Title"), label: t("aboutStats1Desc") },
        { value: t("aboutStats2Title"), label: t("aboutStats2Desc") },
        { value: t("aboutStats3Title"), label: t("aboutStats3Desc") }
      ];

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
          {statsItems.slice(0, 3).map((item, idx) => (
            <div key={idx} className={`bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 flex flex-col items-center text-center transform hover:-translate-y-2 transition-all duration-300 shadow-[0_8px_32px_rgba(0,0,0,0.3)] ${idx === 1 ? 'md:-translate-y-8' : ''}`}>
              {item.icon ? (
                <div className="relative mb-6 flex items-center justify-center bg-[#FF8A00]/20 p-4 rounded-full">
                  {getIcon(item.icon)}
                </div>
              ) : (
                <div className="w-16 h-16 relative mb-6">
                  <Image src={`/about-us/logo-${(idx % 3) + 1}.png`} alt={item.value} fill className="object-contain" />
                </div>
              )}
              <h3 className="text-xl font-bold text-white mb-3">{item.value}</h3>
              <p className="text-gray-400 text-sm leading-relaxed">
                {item.label}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default AboutHero;
