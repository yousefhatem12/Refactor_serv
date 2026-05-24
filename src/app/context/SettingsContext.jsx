"use client";
import React, { createContext, useContext, useState, useEffect } from 'react';
import { useLanguage } from './LanguageContext';
import { fetchAPI } from '@/lib/api';

const SettingsContext = createContext({});

export const SettingsProvider = ({ children, initialSettings }) => {
  const { language } = useLanguage();
  const [settings, setSettings] = useState(initialSettings || {});

  // Fetch new settings if the language changes on the client
  useEffect(() => {
    let mounted = true;
    fetchAPI(`/settings?lang=${language}`).then(data => {
      if (mounted && data) {
        setSettings(data);
      }
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
