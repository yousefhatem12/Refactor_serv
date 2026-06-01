"use client";
import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI } from "@/lib/api";

const ContactMap = () => {
  const { t, language } = useLanguage();
  const [settings, setSettings] = useState({});

  useEffect(() => {
    fetchAPI(`/settings?lang=${language}`)
      .then((data) => {
        if (data) setSettings(data);
      })
      .catch(console.error);
  }, [language]);

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
          <img
            src="/contact-us/Element.webp"
            alt=""
            className={`w-full h-auto object-cover mix-blend-luminosity ${
              language === "ar" ? "scale-x-[-1]" : ""
            }`}
          />
        </div>

        <div className="relative z-10 flex flex-col items-center lg:items-start text-center lg:text-left">
          <div className="flex items-center gap-3">
            <h2 className="text-5xl md:text-[70px] font-bold leading-tight flex items-center">
              <span className="text-[#FF8A00]">{t("contactFindUs")}</span>
              <span className="text-white ml-2 md:ml-4">
                {t("contactUsMap")}
              </span>
            </h2>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="45"
              height="45"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="1.5"
              className="text-white/40 mt-2"
            >
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
            </svg>
          </div>

          <div className="flex items-center gap-2 mt-2 md:mt-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="35"
              height="35"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
              className="text-white/40"
            >
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            <h2 className="text-5xl md:text-[70px] font-bold leading-tight flex items-center">
              {t("contactOnMap")
                .split(" ")
                .map((word, index) => (
                  <span
                    key={index}
                    className={`${index === 1 ? "text-[#FF8A00]" : "text-white"} ${index > 0 ? "ml-2 md:ml-4" : ""}`}
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
          src={`https://maps.google.com/maps?q=${searchQuery}&t=&z=13&ie=UTF8&iwloc=&output=embed`}
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
