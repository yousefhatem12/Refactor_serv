"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { FaPlay } from "react-icons/fa";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import VideoModal from "@/app/components/Shared/VideoModal";
import PricingModal from "@/app/components/Shared/PricingModal";
import { useRouter } from "next/navigation";

const ServicesHero = ({ serviceId = "cloud-hosting", title, desc }) => {
  const router = useRouter();
  const { language, t } = useLanguage();
  const isRtl = language === "ar";
  const settings = useSettings();
  const [isVideoModalOpen, setIsVideoModalOpen] = useState(false);
  const [isPricingModalOpen, setIsPricingModalOpen] = useState(false);

  return (
    <section className="relative w-full h-[calc(100dvh-80px)] min-h-[550px] lg:h-[calc(100vh-80px)] flex items-center justify-between px-6 lg:px-20 pt-8 pb-12 lg:py-0 overflow-hidden">
      {/* Background Image behind everything */}
      <div
        className={`absolute inset-0 w-full h-full z-0 bg-no-repeat bg-cover bg-center mix-blend-screen ${isRtl ? "scale-x-[-1]" : ""}`}
        style={{ backgroundImage: "url('/services-page/hero.webp')" }}
      />

      {/* Left Content */}
      <div className="relative z-20 max-w-2xl w-full lg:w-1/2 flex flex-col justify-center">
        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-white text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-bold mb-4 md:mb-6 tracking-tight uppercase leading-tight"
        >
          {title || t("webHostingHeroTitle")}
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="text-gray-300 text-sm sm:text-base md:text-lg lg:text-xl leading-relaxed mb-6 md:mb-8 lg:mb-10 max-h-[30vh] overflow-y-auto custom-scrollbar pr-2"
        >
          {desc || t("webHostingHeroDesc")}
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="flex flex-wrap items-center gap-4 md:gap-6"
        >
          <button
            onClick={() => router.push("/contact-us")}
            className="px-6 py-2.5 md:px-8 md:py-3 rounded-full bg-white text-[#040A37] font-semibold text-base md:text-lg hover:bg-gray-200 transition-colors border-none cursor-pointer"
          >
            {settings?.hero_cta_text || t("getStarted")}
          </button>

          <button
            onClick={() => setIsVideoModalOpen(true)}
            className="flex items-center gap-2 md:gap-3 text-white hover:text-gray-300 transition-colors group bg-transparent border-none cursor-pointer"
          >
            <div className="w-10 h-10 md:w-12 md:h-12 rounded-full border border-white/30 flex items-center justify-center group-hover:border-white transition-colors">
              <FaPlay className="text-white ml-1 text-xs md:text-sm" />
            </div>
            <span className="font-semibold text-base md:text-lg">
              {settings?.hero_video_button_text || t("knowMore")}
            </span>
          </button>
        </motion.div>
      </div>

      {/* Right Image (Planet) */}
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
            src="/Services/Purple Planet.webp"
            alt="Purple Planet"
            className={`w-full h-auto object-contain drop-shadow-[0_0_50px_rgba(168,85,247,0.3)] scale-110 ${isRtl ? "-translate-x-[10%]" : "translate-x-[10%]"}`}
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

export default ServicesHero;
