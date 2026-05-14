"use client";
import React, { useState } from "react";
import { useLanguage } from "../../context/LanguageContext";
import { useSettings } from "../../context/SettingsContext";
import VideoModal from "../Shared/VideoModal";
import PricingModal from "../Shared/PricingModal";

const AboutCTA = ({ data }) => {
  const { t } = useLanguage();
  const settings = useSettings();
  const [isVideoModalOpen, setIsVideoModalOpen] = useState(false);
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);

  return (
    <section className="py-24 px-6 md:px-16 lg:px-24 w-full relative z-20">
      <div className="max-w-5xl mx-auto text-center flex flex-col items-center">
        
        {/* Glow Effect */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-blue-600/20 blur-[120px] rounded-full -z-10"></div>

        <h2 className="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-8 leading-tight">
          {data?.title || t("aboutCTATitle")}
        </h2>
        
        <p className="text-gray-300 text-lg md:text-xl font-medium mb-12 max-w-3xl leading-relaxed whitespace-pre-line">
          {data?.description || t("aboutCTADesc")}
        </p>

        <div className="flex flex-col sm:flex-row items-center justify-center gap-6">
          <button 
            onClick={() => setIsPricingModalOpen(true)}
            className="bg-white text-black px-10 py-4 rounded-md font-bold text-lg hover:bg-gray-200 transition-colors shadow-[0_0_20px_rgba(255,255,255,0.2)]"
          >
            {data?.button_text || t("getAQuote")}
          </button>
          <button 
            onClick={() => setIsVideoModalOpen(true)}
            className="border border-white/30 text-white px-10 py-4 rounded-md font-bold text-lg hover:bg-white/10 backdrop-blur-sm transition-colors"
          >
            {t("callUs")}
          </button>
        </div>

      </div>
      
      <VideoModal
        isOpen={isVideoModalOpen}
        onClose={() => setIsVideoModalOpen(false)}
        videoUrl={settings?.company_video_url}
      />
      <PricingModal
        isOpen={isPricingModalOpen}
        onClose={() => setIsPricingModalOpen(false)}
      />
    </section>
  );
};

export default AboutCTA;
