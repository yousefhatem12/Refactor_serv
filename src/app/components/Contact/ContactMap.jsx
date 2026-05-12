"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const ContactMap = () => {
  const { t } = useLanguage();
  return (
    <div className="w-full mt-32 mb-20 flex flex-col lg:flex-row items-center gap-12 lg:gap-20 relative z-10">
      {/* Left side text */}
      <motion.div
        initial={{ opacity: 0, x: -30 }}
        whileInView={{ opacity: 1, x: 0 }}
        viewport={{ once: true }}
        className="w-full lg:w-1/3 relative"
      >
        <h2 className="text-5xl md:text-6xl font-bold text-white leading-tight">
          {t("contactFindUs")} <span className="text-[#FF8A00]">{t("contactUsMap")}</span> <br />
          {t("contactOnMap")}
        </h2>
        {/* Subtle decorative elements */}
        <div className="absolute top-0 right-10 text-white/10 opacity-50">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="80"
            height="80"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1"
          >
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
          </svg>
        </div>
        <div className="absolute top-1/2 -left-8 text-white/20">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
          >
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </div>
      </motion.div>

      {/* Right side Map */}
      <motion.div
        initial={{ opacity: 0, x: 30 }}
        whileInView={{ opacity: 1, x: 0 }}
        viewport={{ once: true }}
        className="w-full lg:w-2/3 h-[250px] md:h-[300px] bg-white rounded-lg overflow-hidden border-2 border-white/20 shadow-2xl relative"
      >
        <iframe
          src="https://maps.google.com/maps?q=Mansoura,%20Egypt&t=&z=13&ie=UTF8&iwloc=&output=embed"
          width="100%"
          height="100%"
          style={{ border: 0, filter: "opacity(0.9)" }}
          allowFullScreen=""
          loading="lazy"
          referrerPolicy="no-referrer-when-downgrade"
        ></iframe>
      </motion.div>
    </div>
  );
};

export default ContactMap;
