"use client";
import React from "react";
import Image from "next/image";
import { motion } from "framer-motion";
import { getMediaUrl } from "@/lib/api";

const TextImageSection = ({ data }) => {
  const isLeft = data.layout === 'text_left';
  return (
    <section className="py-20 px-6 md:px-16 lg:px-24 w-full relative overflow-hidden">
      <div className={`max-w-7xl mx-auto flex flex-col ${isLeft ? 'lg:flex-row' : 'lg:flex-row-reverse'} items-center gap-12 lg:gap-20`}>
        <div className="w-full lg:w-1/2">
          {data.tagline && (
            <div className="flex items-center gap-4 mb-4">
              <div className="w-12 h-1 bg-[#FF8A00] rounded-full"></div>
              <span className="text-[#FF8A00] font-semibold tracking-wider uppercase text-sm">
                {data.tagline}
              </span>
            </div>
          )}
          <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
            {data.title}
          </h2>
          <p className="text-gray-300 text-lg leading-relaxed whitespace-pre-line">
            {data.content}
          </p>
        </div>
        <div className="w-full lg:w-1/2 relative h-[300px] md:h-[400px]">
          {data.image && (
             <Image
              src={getMediaUrl(data.image)}
              alt={data.title}
              fill
              className="object-contain"
            />
          )}
        </div>
      </div>
    </section>
  );
};

const StatsSection = ({ data }) => {
  return (
    <section className="py-20 bg-[#05050f]/50">
      <div className="max-w-7xl mx-auto px-6">
        {data.title && <h2 className="text-3xl font-bold text-white text-center mb-12">{data.title}</h2>}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
          {data.items?.map((item, idx) => (
            <div key={idx} className="text-center">
              <div className="text-4xl md:text-5xl font-bold text-[#FF8A00] mb-2">{item.value}</div>
              <div className="text-gray-400 uppercase tracking-widest text-xs md:text-sm">{item.label}</div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

const FeaturesSection = ({ data }) => {
  return (
    <section className="py-20 px-6">
      <div className="max-w-7xl mx-auto">
        {data.title && <h2 className="text-3xl md:text-4xl font-bold text-white text-center mb-12">{data.title}</h2>}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {data.items?.map((item, idx) => (
            <div key={idx} className="p-8 rounded-xl bg-[#ffffff05] border border-[#ffffff10] hover:border-[#FF8A00]/50 transition-colors">
              <h3 className="text-xl font-bold text-white mb-4">{item.title}</h3>
              <p className="text-gray-400 text-sm leading-relaxed">{item.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

import OurJourney from "../AboutUs/OurJourney";
import Founders from "../AboutUs/Founders";
import AboutCTA from "../AboutUs/AboutCTA";

const PageSections = ({ sections }) => {
  if (!sections || !Array.isArray(sections)) return null;

  return (
    <>
      {sections.map((section, index) => {
        switch (section.type) {
          case 'text_image':
            return <TextImageSection key={index} data={section.data} />;
          case 'stats':
            return <StatsSection key={index} data={section.data} />;
          case 'features':
            return <FeaturesSection key={index} data={section.data} />;
          case 'journey':
            return <OurJourney key={index} data={section.data} />;
          case 'team':
            return <Founders key={index} data={section.data} />;
          case 'cta':
            return <AboutCTA key={index} data={section.data} />;
          default:
            return null;
        }
      })}
    </>
  );
};

export default PageSections;
