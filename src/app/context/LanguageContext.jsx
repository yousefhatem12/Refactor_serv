"use client";

import React, { createContext, useContext, useState, useEffect } from "react";
import { useRouter } from "next/navigation";

const LanguageContext = createContext();

import { translations } from "./translations";

export const LanguageProvider = ({ children, defaultLanguage = "en" }) => {
  const [language, setLanguage] = useState(defaultLanguage); // Initialized from defaultLanguage
  const [settings, setSettings] = useState({});
  const router = useRouter();

  useEffect(() => {
    // Read from cookie on initial load if we want to persist it
    const match = document.cookie.match(new RegExp('(^| )NEXT_LOCALE=([^;]+)'));
    const storedLang = match ? match[2] : defaultLanguage;
    
    setLanguage(storedLang);
    document.documentElement.lang = storedLang;
    document.documentElement.dir = storedLang === "ar" ? "rtl" : "ltr";
  }, [defaultLanguage]);

  useEffect(() => {
    let mounted = true;
    const fetchSettings = async () => {
      try {
        const { fetchAPI } = await import("@/lib/api");
        const data = await fetchAPI(`/settings?lang=${language}`);
        if (mounted && data) {
          setSettings(data);
        }
      } catch (err) {
        console.error("Error loading settings in LanguageContext:", err);
      }
    };
    fetchSettings();
    return () => { mounted = false; };
  }, [language]);

  const toggleLanguage = () => {
    const newLang = language === "en" ? "ar" : "en";
    
    // Set cookie for Server Components
    document.cookie = `NEXT_LOCALE=${newLang}; path=/; max-age=31536000`; // 1 year
    
    // Reload the page immediately. This prevents React from updating state and
    // rendering static translations before the reload, avoiding a mixed-language flash.
    window.location.reload();
  };

  const t = (key) => {
    const overrideKey = `trans_${key}`;
    if (settings && settings[overrideKey] !== undefined && settings[overrideKey] !== null && settings[overrideKey] !== "") {
      return settings[overrideKey];
    }
    if (settings && settings[key] !== undefined && settings[key] !== null && settings[key] !== "") {
      return settings[key];
    }
    return translations[language]?.[key] || key;
  };

  return (
    <LanguageContext.Provider value={{ language, toggleLanguage, t }}>
      {children}
    </LanguageContext.Provider>
  );
};

export const useLanguage = () => useContext(LanguageContext);
