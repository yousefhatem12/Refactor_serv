"use client";
import React from "react";
import Link from "next/link";
import HomeBackground from "./components/Home/HomeBackground";
import { useLanguage } from "./context/LanguageContext";

export default function NotFound() {
  const { language, t } = useLanguage();
  const isAr = language === "ar";

  return (
    <div 
      className="min-h-screen flex items-center justify-center relative px-6 overflow-hidden text-white"
      dir={isAr ? "rtl" : "ltr"}
    >
      {/* Visual background glows */}
      <HomeBackground />
      <div className="absolute top-1/4 left-1/4 w-[300px] h-[300px] bg-[#FF8A00]/10 rounded-full filter blur-[100px] pointer-events-none z-0" />
      <div className="absolute bottom-1/4 right-1/4 w-[400px] h-[400px] bg-blue-500/10 rounded-full filter blur-[130px] pointer-events-none z-0" />

      {/* Glassmorphic Card */}
      <div className="relative z-10 w-full max-w-lg p-8 md:p-12 rounded-3xl bg-white/[0.02] border border-white/10 backdrop-blur-xl shadow-2xl text-center flex flex-col items-center gap-6 md:gap-8 group">
        
        {/* Animated Glowing 404 Text */}
        <div className="relative select-none">
          <h1 className="text-8xl md:text-9xl font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-[#FF8A00] to-orange-500 filter drop-shadow-[0_0_30px_rgba(255,138,0,0.5)] animate-pulse">
            404
          </h1>
          <div className="absolute inset-0 bg-[#FF8A00]/20 blur-2xl rounded-full scale-75 -z-10 group-hover:scale-95 transition-transform duration-700" />
        </div>

        {/* Text Details */}
        <div className="flex flex-col gap-3 max-w-sm">
          <h2 className="text-2xl md:text-3xl font-bold tracking-tight text-white uppercase font-sans">
            {t("pageNotFound")}
          </h2>
          <p className="text-gray-400 text-sm md:text-base leading-relaxed font-light">
            {t("pageNotFoundDesc")}
          </p>
        </div>

        {/* Action Button */}
        <Link
          href="/Home"
          className="relative overflow-hidden bg-[#FF8A00] hover:bg-[#ff9c24] text-white px-8 py-4 rounded-xl font-bold transition-all duration-300 shadow-[0_4px_20px_rgba(255,138,0,0.3)] hover:shadow-[0_8px_30px_rgba(255,138,0,0.5)] hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-3 group/btn"
        >
          <span>{t("goBackHome")}</span>
          <svg 
            className={`w-5 h-5 transition-transform duration-300 ${isAr ? "group-hover/btn:translate-x-1" : "group-hover/btn:-translate-x-1 rotate-180"}`}
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor" 
            strokeWidth="2.5"
          >
            <path strokeLinecap="round" strokeLinejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </Link>
      </div>
    </div>
  );
}
