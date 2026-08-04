"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const QuotationHero = () => {
  const { t, language } = useLanguage();
  const isRtl = language === "ar";

  return (
    <section className="relative w-full min-h-[60vh] lg:min-h-[70vh] flex items-center justify-center px-6 md:px-12 lg:px-20 pt-32 pb-12 overflow-hidden">
      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none z-0">
        <div className="absolute top-[-20%] left-[-15%] w-[50%] h-[50%] bg-blue-600/15 blur-[130px] rounded-full" />
        <div className="absolute bottom-[-20%] right-[-15%] w-[50%] h-[50%] bg-purple-600/15 blur-[130px] rounded-full" />
      </div>

      <div className="relative z-20 max-w-7xl w-full mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center" dir={isRtl ? "rtl" : "ltr"}>
        {/* Text Column */}
        <div className={`flex flex-col ${isRtl ? "items-end text-right" : "items-start text-left"}`}>
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="inline-block px-4 py-1.5 rounded-full border border-white/10 bg-white/5 text-blue-400 text-xs md:text-sm font-semibold mb-6 backdrop-blur-sm tracking-wide"
          >
            {t("services")} • {t("requestQuote")}
          </motion.div>

          <motion.h1
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className={`text-white text-4xl md:text-5xl lg:text-5xl font-black mb-6 leading-[1.15] tracking-tight ${
              isRtl ? "font-arabic text-right" : "text-left"
            }`}
          >
            {isRtl ? (
              <>
                اطلب <span className="bg-gradient-to-r from-[#FF8A00] to-[#FF5C00] bg-clip-text text-transparent">تسعير مشروعك</span>
              </>
            ) : (
              <>
                Request a <span className="bg-gradient-to-r from-[#FF8A00] to-[#FF5C00] bg-clip-text text-transparent">Quote</span>
              </>
            )}
          </motion.h1>

          <motion.p
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className={`text-gray-400 text-base md:text-lg leading-relaxed max-w-xl font-light ${
              isRtl ? "text-right" : "text-left"
            }`}
          >
            {t("quoteHeroDesc") || (isRtl 
              ? "املأ هذا النموذج لنتمكن من دراسة متطلبات مشروعك وتقديم عرض سعر دقيق ومخصص لأهدافك."
              : "Fill out this form so we can study your project requirements and provide an accurate quote tailored to your goals.")}
          </motion.p>
        </div>

        {/* Hero Image Column */}
        <div className="flex items-center justify-center">
          <motion.div
            initial={{ opacity: 0, scale: 0.85 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 1, ease: "easeOut" }}
            className="relative w-full max-w-[280px] sm:max-w-[360px] lg:max-w-[420px] aspect-square flex items-center justify-center"
          >
            {/* Glowing Rings behind the Earth */}
            <div className="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-[#FF8A00]/10 to-purple-500/20 blur-2xl rounded-full animate-pulse" style={{ animationDuration: '6s' }} />
            
            <div className="absolute w-[85%] h-[85%] rounded-full border border-white/5 opacity-60" style={{ animation: 'spin 25s linear infinite' }} />
            <div className="absolute w-[65%] h-[65%] rounded-full border border-dashed border-[#FF8A00]/25 opacity-40" style={{ animation: 'spin 35s linear infinite reverse' }} />

            {/* Rotating Earth webp image */}
            <motion.img
              src="/Hero/earth.webp"
              alt="Planet Earth"
              className="w-[75%] h-auto object-contain relative z-10 filter drop-shadow-[0_0_35px_rgba(30,144,255,0.25)]"
              style={{ animation: 'spin 100s linear infinite' }}
              animate={{ 
                y: [0, -12, 0]
              }}
              transition={{
                duration: 6,
                repeat: Infinity,
                ease: "easeInOut"
              }}
            />

            {/* Orbiting Orange Planet from Hero */}
            <motion.img
              src="/Hero/orange-planet.webp"
              alt="Orange Planet"
              className="absolute -top-4 -left-4 w-[24%] h-auto object-contain z-20 opacity-90 filter drop-shadow-[0_0_15px_rgba(255,138,0,0.3)]"
              animate={{ 
                y: [0, 6, 0],
                rotate: [0, 360]
              }}
              transition={{ 
                y: { duration: 5, repeat: Infinity, ease: "easeInOut" },
                rotate: { duration: 25, repeat: Infinity, ease: "linear" }
              }}
            />

            {/* Orbiting Blue Planet */}
            <motion.img
              src="/Services/new/blue.webp"
              alt="Blue Planet"
              className="absolute -bottom-4 -right-4 w-[20%] h-auto object-contain z-20 opacity-80"
              animate={{ 
                y: [0, -8, 0],
                rotate: [0, -360]
              }}
              transition={{ 
                y: { duration: 6, repeat: Infinity, ease: "easeInOut" },
                rotate: { duration: 20, repeat: Infinity, ease: "linear" }
              }}
            />
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default QuotationHero;
