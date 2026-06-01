"use client";
import React from "react";
import { motion } from "framer-motion";
import { FaWhatsapp } from "react-icons/fa";
import { useLanguage } from "../../context/LanguageContext";
import { useSettings } from "../../context/SettingsContext";
import { getMediaUrl } from "@/lib/api";

const MobileAppsHero = ({ data }) => {
  const { language, t } = useLanguage();
  const isRtl = language === "ar";
  const settings = useSettings();

  const heroImage = data?.hero_image
    ? getMediaUrl(data.hero_image)
    : "/services-web-dev/hero.png";

  const whatsappNumber = settings?.site_whatsapp
    ? settings.site_whatsapp.replace(/\D/g, "")
    : "201288863631";
  const whatsappUrl = `https://api.whatsapp.com/send/?phone=${whatsappNumber}&text&type=phone_number&app_absent=0`;

  return (
    <section className="relative w-full h-[calc(100dvh-80px)] min-h-[550px] lg:h-[calc(100vh-80px)] flex items-center justify-between px-6 lg:px-20 pt-8 pb-12 lg:py-0 overflow-hidden">
      {/* Background Image behind everything */}
      <div
        className={`absolute inset-0 w-full h-full z-0 bg-no-repeat bg-cover bg-center mix-blend-screen ${isRtl ? "scale-x-[-1]" : ""}`}
        style={{ backgroundImage: `url('${heroImage}')` }}
      />

      {/* Content */}
      <div className="relative z-20 max-w-2xl w-full lg:w-1/2 flex flex-col justify-center">
        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 tracking-tight uppercase leading-tight md:leading-[1.1]"
        >
          {data?.title || t("mobileAppsHeroTitle")}
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="text-gray-300 text-sm sm:text-base md:text-lg lg:text-xl leading-relaxed mb-6 md:mb-8 lg:mb-10 max-h-[30vh] overflow-y-auto custom-scrollbar pr-2"
        >
          {data?.subtitle || t("mobileAppsHeroDesc")}
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="flex flex-wrap items-center gap-4 md:gap-6"
        >
          <a
            href={whatsappUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="flex items-center gap-2 px-6 py-2.5 md:px-8 md:py-3 rounded-full bg-[#25D366] text-white font-semibold text-base md:text-lg hover:bg-[#128C7E] transition-colors cursor-pointer border-none no-underline"
          >
            <FaWhatsapp size={24} />
            {t("mobileAppsBuildNow")}
          </a>
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
    </section>
  );
};

export default MobileAppsHero;
