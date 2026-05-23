"use client";
import React, { useState, useEffect } from "react";
import AboutHero from "../components/AboutUs/AboutHero";
import PageSections from "../components/Shared/PageSections";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../context/LanguageContext";
import GlobalReach from "../components/AboutUs/GlobalReach";
import WhyChooseUs from "../components/AboutUs/WhyChooseUs";
import OurJourney from "../components/AboutUs/OurJourney";
import Founders from "../components/AboutUs/Founders";
import TeamWork from "../components/AboutUs/TeamWork";
import AboutCTA from "../components/AboutUs/AboutCTA";


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

  const blocks = pageData?.sections?.blocks || [];
  
  const hasGlobalReach = blocks.some(s => s.type === 'global_reach');
  const hasWhyChooseUs = blocks.some(s => s.type === 'features');
  const hasJourney = blocks.some(s => s.type === 'journey');
  const hasTeam = blocks.some(s => s.type === 'team');
  const hasTeamWork = blocks.some(s => s.type === 'team_work');
  const hasCta = blocks.some(s => s.type === 'cta');

  const textImageBlocks = blocks.filter(s => s.type === 'text_image');

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10">
        <AboutHero data={pageData} />
        {/* Render text_image sections (Experience & Solutions) right after the hero cards */}
        <PageSections sections={textImageBlocks} />
        
        {/* Render GlobalReach dynamically from dashboard, or fallback to static */}
        {hasGlobalReach ? (
          <PageSections sections={blocks.filter(s => s.type === 'global_reach')} />
        ) : (
          <GlobalReach />
        )}

        {/* Render WhyChooseUs dynamically from dashboard, or fallback to static */}
        {hasWhyChooseUs ? (
          <PageSections sections={blocks.filter(s => s.type === 'features')} />
        ) : (
          <WhyChooseUs />
        )}

        {/* Render OurJourney dynamically from dashboard, or fallback to static */}
        {hasJourney ? (
          <PageSections sections={blocks.filter(s => s.type === 'journey')} />
        ) : (
          <OurJourney />
        )}

        {/* Render Founders dynamically from dashboard, or fallback to static */}
        {hasTeam ? (
          <PageSections sections={blocks.filter(s => s.type === 'team')} />
        ) : (
          <Founders />
        )}

        {/* Render TeamWork dynamically from dashboard, or fallback to static */}
        {hasTeamWork ? (
          <PageSections sections={blocks.filter(s => s.type === 'team_work')} />
        ) : (
          <TeamWork />
        )}

        {/* Render CTA dynamically from dashboard, or fallback to static */}
        {hasCta ? (
          <PageSections sections={blocks.filter(s => s.type === 'cta')} />
        ) : (
          <AboutCTA />
        )}
      </div>
    </main>
  );
}
