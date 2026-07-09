"use client";
import React, { createContext, useContext, useState, useEffect, useRef } from 'react';
import { useLanguage } from './LanguageContext';
import { fetchAPI } from '@/lib/api';

const SettingsContext = createContext({});

export const SettingsProvider = ({ children, initialSettings, ssrLocale }) => {
  const { language } = useLanguage();
  const [settings, setSettings] = useState(initialSettings || {});
  const hasFetched = useRef(false);

  useEffect(() => {
    let mounted = true;

    // Skip fetch ONLY if we have SSR settings AND the SSR locale matches current language
    const ssrMatchesLanguage = ssrLocale && ssrLocale === language;
    if (!hasFetched.current && initialSettings && Object.keys(initialSettings).length > 0 && ssrMatchesLanguage) {
      hasFetched.current = true;
      return;
    }

    hasFetched.current = true;

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

