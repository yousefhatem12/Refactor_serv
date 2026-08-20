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
    // Read from URL path first to keep URL as source of truth
    const path = window.location.pathname;
    let pathLang = null;
    if (path.startsWith("/ar") || path === "/ar") {
      pathLang = "ar";
    } else if (path.startsWith("/en") || path === "/en") {
      pathLang = "en";
    }

    const match = document.cookie.match(new RegExp('(^| )NEXT_LOCALE=([^;]+)'));
    const storedLang = pathLang || (match ? match[2] : defaultLanguage);
    
    setLanguage(storedLang);
    document.documentElement.lang = storedLang;
    document.documentElement.dir = storedLang === "ar" ? "rtl" : "ltr";

    // Keep the cookie synchronized with the URL path
    if (pathLang && (!match || match[2] !== pathLang)) {
      document.cookie = `NEXT_LOCALE=${pathLang}; path=/; max-age=31536000`; // 1 year
    }
  }, [defaultLanguage]);

  useEffect(() => {
    let mounted = true;
    const fetchSettings = async () => {
      try {
        const { fetchAPI } = await import("@/lib/api");
        const data = await fetchAPI(`/settings?lang=${language}`);
        if (mounted && data) {
          setSettings(data);
          const path = window.location.pathname;
          if (path === "/" || path === "/ar" || path === "/en" || path === "/ar/" || path === "/en/") {
            const fallbackTitle = language === "ar" ? "سيرف فايف | Serv5" : "Serv5";
            document.title = data.site_title || fallbackTitle;
          }
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
    
    // Update the browser URL with the new locale path prefix
    const currentPath = window.location.pathname;
    let newPath = currentPath;
    if (currentPath.startsWith("/ar")) {
      newPath = currentPath.replace("/ar", `/${newLang}`);
    } else if (currentPath.startsWith("/en")) {
      newPath = currentPath.replace("/en", `/${newLang}`);
    } else {
      newPath = `/${newLang}${currentPath === "/" ? "" : currentPath}`;
    }

    window.location.href = newPath;
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
