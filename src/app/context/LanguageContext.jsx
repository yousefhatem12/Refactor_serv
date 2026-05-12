"use client";

import React, { createContext, useContext, useState, useEffect } from "react";
import { useRouter } from "next/navigation";

const LanguageContext = createContext();

import { translations } from "./translations";

export const LanguageProvider = ({ children }) => {
  const [language, setLanguage] = useState("en"); // Default to English
  const router = useRouter();

  useEffect(() => {
    // Read from cookie on initial load if we want to persist it
    const match = document.cookie.match(new RegExp('(^| )NEXT_LOCALE=([^;]+)'));
    const storedLang = match ? match[2] : "en";
    
    setLanguage(storedLang);
    document.documentElement.lang = storedLang;
    document.documentElement.dir = storedLang === "ar" ? "rtl" : "ltr";
  }, []);

  const toggleLanguage = () => {
    const newLang = language === "en" ? "ar" : "en";
    setLanguage(newLang);
    
    // Set cookie for Server Components
    document.cookie = `NEXT_LOCALE=${newLang}; path=/; max-age=31536000`; // 1 year
    
    // Update HTML attributes
    document.documentElement.lang = newLang;
    document.documentElement.dir = newLang === "ar" ? "rtl" : "ltr";
    
    // Refresh Server Components
    router.refresh();
  };

  const t = (key) => {
    return translations[language]?.[key] || key;
  };

  return (
    <LanguageContext.Provider value={{ language, toggleLanguage, t }}>
      {children}
    </LanguageContext.Provider>
  );
};

export const useLanguage = () => useContext(LanguageContext);
