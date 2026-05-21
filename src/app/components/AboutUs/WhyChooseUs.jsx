"use client";
import React from "react";
import Image from "next/image";
import { useLanguage } from "../../context/LanguageContext";

const WhyChooseUs = ({ data }) => {
  const { t } = useLanguage();
  
  const isUsingDefaultData = !(data?.items?.length > 0);
  const cards = isUsingDefaultData 
    ? [
        { num: "01", title: t("whyCard1Title") || "Trusted Partner", desc: t("whyCard1Desc") },
        { num: "02", title: t("whyCard2Title") || "Innovative Solutions", desc: t("whyCard2Desc") },
        { num: "03", title: t("whyCard3Title") || "Expert Team", desc: t("whyCard3Desc") },
        { num: "04", title: t("whyCard4Title") || "Client-Centric", desc: t("whyCard4Desc") },
        { num: "05", title: t("whyCard5Title") || "Proven Methodology", desc: t("whyCard5Desc") },
        { num: "06", title: t("whyCard6Title") || "Continuous Support", desc: t("whyCard6Desc") },
      ]
    : data.items.map((item, idx) => ({
        num: String(idx + 1).padStart(2, '0'),
        title: item.title,
        desc: item.description,
      }));

  const sectionTitle = data?.title || t("aboutWhyTitle") || "Why Choose Us?";

  return (
    <section className="relative w-full min-h-screen py-24 px-6 md:px-16 lg:px-24">
      {/* Background Image */}
      <div className="absolute inset-0 z-0">
        <Image
          src="/about-us/sectiom-5-bg.png"
          alt="Why Choose Us Background"
          fill
          className="object-cover object-center opacity-80"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-[#0A0A1A] via-transparent to-[#0A0A1A] z-10" />
      </div>

      <div className="relative z-20 max-w-7xl mx-auto">
        <div className="mb-16">
          <h2 className="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
            {sectionTitle}
          </h2>
        </div>

        {/* Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl">
          {cards.map((card, index) => (
            <div 
              key={index}
              className="bg-[#050510]/80 backdrop-blur-sm border border-[#FF8A00]/20 rounded-xl p-8 hover:border-[#FF8A00] transition-colors duration-300 group"
            >
              <div className="text-[#FF8A00] text-3xl font-bold mb-4 font-mono">
                {card.num}
              </div>
              <h3 className="text-xl font-bold text-white mb-3 group-hover:text-[#FF8A00] transition-colors duration-300">
                {card.title}
              </h3>
              <p className="text-gray-400 text-sm leading-relaxed">
                {card.desc}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default WhyChooseUs;
