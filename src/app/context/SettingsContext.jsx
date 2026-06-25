"use client";
import React, { createContext, useContext, useState, useEffect, useRef } from 'react';
import { useLanguage } from './LanguageContext';
import { fetchAPI } from '@/lib/api';

const SettingsContext = createContext({});

export const SettingsProvider = ({ children, initialSettings }) => {
  const { language } = useLanguage();
  const [settings, setSettings] = useState(initialSettings || {});
  // Track the language of the initial SSR settings to avoid re-fetch if already correct
  const initialLang = useRef(null);

  useEffect(() => {
    let mounted = true;

    // Fetch settings in the current language (client-side, locale-aware)
    fetchAPI(`/settings?lang=${language}`)
      .then(data => {
        if (mounted && data) {
          setSettings(data);
        }
      })
      .catch(() => {
        // Keep SSR initialSettings if fetch fails
      });

    return () => { mounted = false; };
  }, [language]);

  return (
    <SettingsContext.Provider value={settings}>
      {children}
    </SettingsContext.Provider>
  );
};

export const useSettings = () => useContext(SettingsContext);
