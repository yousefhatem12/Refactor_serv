"use client";
import React, { useState, useEffect } from "react";
import AboutHero from "../components/AboutUs/AboutHero";
import PageSections from "../components/Shared/PageSections";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import Experience from "../components/AboutUs/Experience";
import GlobalReach from "../components/AboutUs/GlobalReach";
import WhyChooseUs from "../components/AboutUs/WhyChooseUs";
import OurJourney from "../components/AboutUs/OurJourney";
import Founders from "../components/AboutUs/Founders";
import AboutCTA from "../components/AboutUs/AboutCTA";
import Solutions from "../components/AboutUs/Solutions";


export default function AboutUsPage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);

  useEffect(() => {
    fetchAPI(`/pages/about-us?lang=${language}`)
      .then((data) => {
        if (data) setPageData(data);
      })
      .catch(console.error);
  }, [language]);

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10">
        <AboutHero data={pageData} />
        <Experience />
        <Solutions />
        <GlobalReach />
        <WhyChooseUs />
        <OurJourney />
        <Founders />
        <AboutCTA />
        {/* Dynamic Sections from Dashboard */}
        <PageSections sections={pageData?.sections} />
      </div>
    </main>
  );
}
