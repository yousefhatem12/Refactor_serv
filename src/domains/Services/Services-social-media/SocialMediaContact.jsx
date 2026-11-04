"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const SocialMediaContact = () => {
  const { t } = useLanguage();
  return (
    <section className="relative w-full py-32 px-6 lg:px-20 z-20">
      {/* Background image with screen blend mode to hide black pixels */}
      <div 
        className="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat opacity-60 mix-blend-screen pointer-events-none"
        style={{ backgroundImage: "url('/services-social-media/section-5.webp')" }}
      ></div>
      
      <div className="max-w-4xl mx-auto text-center flex flex-col items-center relative z-10">
        <motion.h2
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-white text-2xl md:text-4xl font-bold leading-tight mb-8"
        >
          {t("smContactTitle1")}
          <br className="hidden md:block" />
          {t("smContactTitle2")} <span className="text-[#D98B50]">{t("smContactTitle3")}</span>
        </motion.h2>

        <motion.button
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ delay: 0.1 }}
          className="px-10 py-4 rounded-full bg-[#D98B50] text-white font-bold text-lg hover:bg-white hover:text-[#D98B50] transition-colors"
        >
          {t("contactUs")}
        </motion.button>
      </div>
    </section>
  );
};

export default SocialMediaContact;
