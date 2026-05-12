"use client";
import React, { useState, useEffect } from "react";
import ContactInfo from "../components/Contact/ContactInfo";
import ContactForm from "../components/Contact/ContactForm";
import ContactMap from "../components/Contact/ContactMap";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";

export default function ContactUsPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);

  useEffect(() => {
    fetchAPI(`/pages/contact-us?lang=${language}`).then(data => {
      if (data) setPageData(data);
    }).catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      {/* Background Color Layer */}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      <div className="w-full max-w-7xl mx-auto px-6 relative z-10 pt-32 pb-10">
        <div className="flex flex-col lg:flex-row items-start justify-between">
          <ContactInfo data={pageData} />
          <ContactForm />
        </div>

        <ContactMap />
      </div>
    </main>
  );
}
