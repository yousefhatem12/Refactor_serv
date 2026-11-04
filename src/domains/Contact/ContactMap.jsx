"use client";
import React from "react";
import Image from "next/image";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";

const ContactMap = () => {
  const { t, language } = useLanguage();
  const contextSettings = useSettings();
  const settings = contextSettings || {};

  const searchQuery = settings.site_address
    ? encodeURIComponent(settings.site_address)
    : "Mansoura,%20Egypt";

  return (
    <div className="w-full mt-32 mb-20 flex flex-col lg:flex-row items-center gap-12 lg:gap-20 relative z-10 ">
      {/* Left side text */}
      <motion.div
        initial={{ opacity: 0, x: -30 }}
        whileInView={{ opacity: 1, x: 0 }}
        viewport={{ once: true }}
        className="w-full lg:w-1/3 relative flex items-center justify-center lg:justify-start "
      >
        {/* Background Image */}
        <div
          className={`absolute inset-0 z-0 flex items-center justify-center overflow-hidden scale-[1.8] md:scale-[2] pointer-events-none opacity-50 ${
            language === "ar"
              ? "translate-x-10 lg:translate-x-20"
              : "-translate-x-10 lg:-translate-x-20"
          }`}
        >
          <Image
            src="/contact-us/Element.webp"
            alt="Decorative Background Map Element"
            width={711}
            height={421}
            loading="lazy"
            sizes="(max-width: 1024px) 100vw, 33vw"
            className={`w-full h-auto object-cover mix-blend-luminosity ${
              language === "ar" ? "scale-x-[-1]" : ""
            }`}
          />
        </div>

        <div className="relative z-10 flex flex-col items-center lg:items-start text-center lg:text-left">
          <div className="flex items-center gap-2 md:gap-3">
            <h2 className="text-4xl sm:text-5xl md:text-[70px] font-bold leading-tight flex items-center gap-2.5 sm:gap-3 md:gap-4">
              <span className="text-[#FF8A00]">{t("contactFindUs")}</span>
              <span className="text-white">
                {t("contactUsMap")}
              </span>
            </h2>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="1.5"
              className="w-8 h-8 sm:w-9 sm:h-9 md:w-11 md:h-11 text-white/40 mt-1 md:mt-2 shrink-0"
            >
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
            </svg>
          </div>

          <div className="flex items-center gap-2 md:gap-3 mt-2 md:mt-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
              className="w-6 h-6 sm:w-7 sm:h-7 md:w-9 md:h-9 text-white/40 shrink-0"
            >
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            <h2 className="text-4xl sm:text-5xl md:text-[70px] font-bold leading-tight flex items-center gap-2.5 sm:gap-3 md:gap-4">
              {t("contactOnMap")
                .split(" ")
                .map((word, index) => (
                  <span
                    key={index}
                    className={index === 0 ? "text-[#FF8A00]" : "text-white"}
                  >
                    {word}
                  </span>
                ))}
            </h2>
          </div>
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
          title={language === "ar" ? "موقع شركة سيرف فايف على الخريطة" : "Serv5 Location on Google Maps"}
          src="https://maps.google.com/maps?q=%D8%B3%D9%8A%D8%B1%D9%81+%D9%81%D8%A7%D9%8A%D9%81+%D9%84%D8%AE%D8%AF%D9%85%D8%A7%D8%AA+%D8%A7%D9%84%D9%88%D9%8A%D8%A8&t=&z=16&ie=UTF8&iwloc=&output=embed"
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
