"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI, getMediaUrl } from "@/lib/api";

const Founders = ({ data }) => {
  const { t } = useLanguage();

  // The DB team has 'name', 'role', 'image'
  // The global /team has 'name', 'role', 'image'
  // We use data.items if available, otherwise fetch from /team, otherwise fallback.
  const founders = [
    {
      name: t("founder1Name") || "MOHAMED ABDELAL",
      role: t("founder1Role"),
      image: "person-1.png",
    },
    {
      name: t("founder2Name") || "AHMED MOHAMED",
      role: t("founder2Role"),
      image: "person-2.png",
    },
    {
      name: t("founder3Name") || "AHMED RAMADAN",
      role: t("founder3Role"),
      image: "person-3.png",
    },
  ];

  const [team, setTeam] = useState([]);
  const [settings, setSettings] = useState({});

  useEffect(() => {
    // Only fetch if data items are not provided via Page Builder
    if (!data?.items || data.items.length === 0) {
      fetchAPI('/team').then(res => {
        if (res && res.length > 0) setTeam(res);
      }).catch(console.error);
    }

    if (!data?.title) {
      fetchAPI('/settings').then(res => {
        if (res) setSettings(res);
      }).catch(console.error);
    }
  }, [data]);

  const displayTeam = data?.items?.length > 0 ? data.items.map(member => ({
    name: member.name,
    role: member.role,
    image: member.image ? getMediaUrl(member.image) : "/about-us/person-1.png",
  })) : (team.length > 0 ? team.map(member => ({
    name: member.name,
    role: member.role,
    image: member.image || "/about-us/person-1.png",
  })) : founders);

  return (
    <section className="py-24 px-6 md:px-16 lg:px-24 w-full relative z-20">
      <div className="max-w-7xl mx-auto flex flex-col items-center">
        
        {/* Header */}
        <div className="text-center mb-16">
          <h2 className="text-2xl md:text-3xl font-medium text-white mb-6 tracking-widest uppercase">
            {data?.title || settings.founders_title || t("aboutFoundersTag")}
          </h2>
          <p className="text-gray-300 text-sm md:text-base max-w-3xl mx-auto leading-relaxed whitespace-pre-line">
            {data?.description || settings.founders_desc || t("aboutFoundersDesc")}
          </p>
          <div className="w-20 h-[1px] bg-gray-500/50 mx-auto mt-8"></div>
        </div>

        {/* Founders Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 w-full max-w-5xl">
          {displayTeam.map((founder, index) => (
            <div key={index} className="flex flex-col w-full group">
              {/* Image Container */}
              <div className="relative w-full h-[400px] border-[16px] border-[#1e4a86] bg-transparent">
                <Image
                  src={founder.image.startsWith('/about-us') || founder.image.startsWith('http') || founder.image.startsWith('data') ? founder.image : `/about-us/${founder.image}`}
                  alt={founder.name}
                  fill
                  className="object-cover object-bottom transition-transform duration-500 group-hover:scale-105"
                />
              </div>
              
              {/* Text Container */}
              <div className="text-center bg-[#0d2a57] w-full py-5 px-2">
                <h3 className="text-white font-bold text-sm md:text-base tracking-wide mb-1 uppercase">{founder.name}</h3>
                <p className="text-gray-300 text-xs md:text-sm">{founder.role}</p>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
};

export default Founders;
