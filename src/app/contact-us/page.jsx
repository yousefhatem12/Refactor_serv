"use client";
import React, { useState, useEffect } from "react";
import ContactInfo from "../components/Contact/ContactInfo";
import ContactForm from "../components/Contact/ContactForm";
import ContactMap from "../components/Contact/ContactMap";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import Loading from "../loading";
import { notFound } from "next/navigation";

export default function ContactUsPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);
  const [error, setError] = useState(false);

  useEffect(() => {
    fetchAPI(`/pages/contact-us?lang=${language}`)
      .then((data) => {
        if (data) {
          setPageData(data);
        } else {
          setError(true);
        }
      })
      .catch((err) => {
        console.error(err);
        setError(true);
      });
  }, [language]);

  if (error) {
    notFound();
  }

  if (!pageData) {
    return <Loading />;
  }

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden text-white z-10">
      {/* Visual background glows */}
      <div className="absolute top-0 left-1/4 w-[500px] h-[500px] bg-[#FF8A00]/5 rounded-full filter blur-[120px] pointer-events-none z-0" />
      <div className="absolute top-1/2 right-1/4 w-[600px] h-[600px] bg-blue-500/5 rounded-full filter blur-[150px] pointer-events-none z-0" />

      <div className="w-full max-w-7xl mx-auto px-6 relative z-10 pt-32 pb-10">
        <div className="flex flex-col lg:flex-row items-start justify-between gap-12 lg:gap-8">
          <ContactInfo data={pageData} />
          <ContactForm />
        </div>

        <ContactMap />
      </div>
    </main>
  );
}
