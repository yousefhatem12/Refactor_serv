"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { FaPlay } from "react-icons/fa";
import { useLanguage } from "../../context/LanguageContext";
import { useSettings } from "../../context/SettingsContext";
import { getMediaUrl } from "@/lib/api";
import VideoModal from "../Shared/VideoModal";
import PricingModal from "../Shared/PricingModal";

const WebDevHero = ({ data, serviceId = "web-development" }) => {
  const { language, t } = useLanguage();
  const isRtl = language === "ar";
  const settings = useSettings();
  const [isVideoModalOpen, setIsVideoModalOpen] = useState(false);
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);

  const heroImage = data?.hero_image ? getMediaUrl(data.hero_image) : "/services-web-dev/hero.png";
  
  return (
    <section className="relative w-full min-h-[80vh] flex items-center justify-between px-6 lg:px-20 pt-20">
      {/* Background Image behind everything */}
      <div
        className="absolute inset-[-100px] h-screen z-0 bg-no-repeat bg-cover bg-center mix-blend-screen"
        style={{ backgroundImage: `url('${heroImage}')` }}
      />

      {/* Content */}
      <div className="relative z-20 max-w-2xl w-full lg:w-1/2">
        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-white text-5xl md:text-7xl font-bold mb-6 tracking-tight uppercase"
        >
          {data?.title || t("webDevHeroTitle")}
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="text-gray-300 text-lg md:text-xl leading-relaxed mb-10"
        >
          {data?.subtitle || t("webDevHeroDesc")}
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="flex flex-wrap items-center gap-6"
        >
          <button 
            onClick={() => setIsPricingModalOpen(true)}
            className="px-8 py-3 rounded-full bg-white text-[#040A37] font-semibold text-lg hover:bg-gray-200 transition-colors cursor-pointer border-none"
          >
            {settings?.hero_cta_text || t("getStarted")}
          </button>

          <button 
            onClick={() => setIsVideoModalOpen(true)}
            className="flex items-center gap-3 text-white hover:text-gray-300 transition-colors group bg-transparent border-none cursor-pointer"
          >
            <div className="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center group-hover:border-white transition-colors">
              <FaPlay className="text-white ml-1 text-sm" />
            </div>
            <span className="font-semibold text-lg">
              {settings?.hero_video_button_text || t("knowMore")}
            </span>
          </button>
        </motion.div>
      </div>

      {/* Right/Left Image (Planet) */}
      <motion.div
        initial={{ opacity: 0, scale: 0.8 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 1, ease: "easeOut" }}
        className={`hidden lg:block absolute ${isRtl ? "left-0" : "right-0"} top-1/2 -translate-y-1/2 w-1/2 max-w-[800px] pointer-events-none z-10`}
      >
        <motion.div
          animate={{ y: [0, -20, 0] }}
          transition={{ duration: 6, repeat: Infinity, ease: "easeInOut" }}
        >
          <img
            src="/Services/new/green.png"
            alt="Green Planet"
            className={`w-full h-auto object-contain drop-shadow-[0_0_50px_rgba(34,197,94,0.3)] scale-110 ${isRtl ? "-translate-x-[10%]" : "translate-x-[10%]"}`}
          />
        </motion.div>
      </motion.div>

      <VideoModal
        isOpen={isVideoModalOpen}
        onClose={() => setIsVideoModalOpen(false)}
        videoUrl={settings?.company_video_url}
      />
      <PricingModal
        isOpen={isPricingModalOpen}
        onClose={() => setIsPricingModalOpen(false)}
        serviceId={serviceId}
      />
    </section>
  );
};

export default WebDevHero;
