"use client";
import React from "react";
import { useLanguage } from "../../context/LanguageContext";

const Newsletter = () => {
  const { t } = useLanguage();
  return (
    <section className="w-full py-20 text-white relative z-10 flex flex-col items-center justify-center text-center">
      <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold mb-8 max-w-2xl leading-snug">
        {t("newsletterTitle")}
      </h2>
      
      <form className="flex flex-col sm:flex-row gap-3 w-full max-w-md px-4" onSubmit={(e) => e.preventDefault()}>
        <input 
          type="email" 
          placeholder={t("newsletterPlaceholder") || "Your Email"}
          className="flex-1 px-6 py-3 rounded-md text-black focus:outline-none"
          required
        />
        <button 
          type="submit" 
          className="px-8 py-3 rounded-md border border-blue-500 text-white hover:bg-blue-500/10 transition-colors font-medium"
        >
          {t("newsletterSubscribe")}
        </button>
      </form>
    </section>
  );
};

export default Newsletter;
