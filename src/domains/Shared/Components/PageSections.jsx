"use client";
import React from "react";
import Image from "next/image";
import { motion } from "framer-motion";
import { getMediaUrl } from "@/lib/api";

const TextImageSection = ({ data }) => {
  const isLeft = data.layout === "text_left";

  // Detect if this is the Experience or Solutions section to apply beautiful astronaut/rocket images and styles
  const taglineLower = data.tagline?.toLowerCase() || "";
  const titleLower = data.title?.toLowerCase() || "";

  const isExperience =
    taglineLower.includes("experience") ||
    titleLower.includes("experience") ||
    taglineLower.includes("خبر") ||
    titleLower.includes("خبر") ||
    taglineLower.includes("تجرب") ||
    titleLower.includes("تجرب");

  const isSolutions =
    taglineLower.includes("solutions") ||
    titleLower.includes("solution") ||
    taglineLower.includes("حلول") ||
    titleLower.includes("حلول");

  let imageUrl = null;
  let glowColor = "bg-blue-600/20";
  let imageClass = "object-contain";

  if (isExperience) {
    imageUrl = "/about-us/section-2-image.webp";
    glowColor = "bg-blue-600/20";
    imageClass =
      "object-cover [mask-image:radial-gradient(circle,black_0%,transparent_100%)] [-webkit-mask-image:radial-gradient(circle,black_0%,transparent_100%)] mix-blend-screen brightness-[130%] contrast-[120%] saturate-[130%]";
  } else if (isSolutions) {
    imageUrl = "/about-us/section-3-image.webp";
    glowColor = "bg-purple-600/20";
    imageClass =
      "object-cover [mask-image:radial-gradient(circle,black_0%,transparent_100%)] [-webkit-mask-image:radial-gradient(circle,black_0%,transparent_100%)] mix-blend-screen brightness-[130%] contrast-[120%] saturate-[130%]";
  } else {
    imageUrl = data.image ? getMediaUrl(data.image) : null;
  }

  return (
    <section className="py-20 px-6 md:px-16 lg:px-24 w-full relative overflow-hidden">
      <div
        className={`max-w-7xl mx-auto flex flex-col ${isLeft ? "lg:flex-row" : "lg:flex-row-reverse"} items-center gap-12 lg:gap-20`}
      >
        <div className="w-full lg:w-1/2 flex flex-col justify-center">
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
        {imageUrl && (
          <div className="w-full lg:w-1/2 relative h-[300px] md:h-[400px] flex justify-center lg:justify-end">
            <div className="relative w-full h-full">
              <div
                className={`absolute inset-0 ${glowColor} blur-[100px] rounded-full -z-10`}
              ></div>
              <Image
                src={imageUrl}
                alt={data.title}
                fill
                className={imageClass}
              />
            </div>
          </div>
        )}
      </div>
    </section>
  );
};

const StatsSection = ({ data }) => {
  return (
    <section className="py-20 bg-[#05050f]/50">
      <div className="max-w-7xl mx-auto px-6">
        {data.title && (
          <h2 className="text-3xl font-bold text-white text-center mb-12">
            {data.title}
          </h2>
        )}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
          {data.items?.map((item, idx) => (
            <div key={idx} className="text-center">
              <div className="text-4xl md:text-5xl font-bold text-[#FF8A00] mb-2">
                {item.value}
              </div>
              <div className="text-gray-400 uppercase tracking-widest text-xs md:text-sm">
                {item.label}
              </div>
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
        {data.title && (
          <h2 className="text-3xl md:text-4xl font-bold text-white text-center mb-12">
            {data.title}
          </h2>
        )}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {data.items?.map((item, idx) => (
            <div
              key={idx}
              className="p-8 rounded-xl bg-[#ffffff05] border border-[#ffffff10] hover:border-[#FF8A00]/50 transition-colors"
            >
              <h3 className="text-xl font-bold text-white mb-4">
                {item.title}
              </h3>
              <p className="text-gray-400 text-sm leading-relaxed">
                {item.description}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

import OurJourney from "@/domains/AboutUs/OurJourney";
import Founders from "@/domains/AboutUs/Founders";
import AboutCTA from "@/domains/AboutUs/AboutCTA";
import WhyChooseUs from "@/domains/AboutUs/WhyChooseUs";
import GlobalReach from "@/domains/AboutUs/GlobalReach";
import TeamWork from "@/domains/AboutUs/TeamWork";

const PageSections = ({ sections }) => {
  if (!sections || !Array.isArray(sections)) return null;

  return (
    <>
      {sections.map((section, index) => {
        switch (section.type) {
          case "text_image":
            return <TextImageSection key={index} data={section.data} />;
          case "stats":
            return <StatsSection key={index} data={section.data} />;
          case "features":
            return <WhyChooseUs key={index} data={section.data} />;
          case "global_reach":
            return <GlobalReach key={index} data={section.data} />;
          case "journey":
            return <OurJourney key={index} data={section.data} />;
          case "team":
            return <Founders key={index} data={section.data} />;
          case "team_work":
            return <TeamWork key={index} data={section.data} />;
          case "cta":
            return <AboutCTA key={index} data={section.data} />;
          default:
            return null;
        }
      })}
    </>
  );
};

export default PageSections;
