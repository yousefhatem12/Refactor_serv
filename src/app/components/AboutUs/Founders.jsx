"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import { motion } from "framer-motion";
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
      fetchAPI("/team")
        .then((res) => {
          if (res && res.length > 0) setTeam(res);
        })
        .catch(console.error);
    }

    if (!data?.title) {
      fetchAPI("/settings")
        .then((res) => {
          if (res) setSettings(res);
        })
        .catch(console.error);
    }
  }, [data]);

  const displayTeam =
    data?.items?.length > 0
      ? data.items.map((member) => ({
          name: member.name,
          role: member.role,
          image: member.image
            ? getMediaUrl(member.image)
            : "/about-us/person-1.png",
        }))
      : team.length > 0
        ? team.map((member) => ({
            name: member.name,
            role: member.role,
            image: member.image || "/about-us/person-1.png",
          }))
        : founders;

  // Animation Variants
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.2,
      },
    },
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 40 },
    visible: {
      opacity: 1,
      y: 0,
      transition: {
        duration: 0.8,
        ease: [0.22, 1, 0.36, 1],
      },
    },
  };

  return (
    <section className="py-32 px-6 md:px-16 lg:px-24 w-full relative overflow-hidden bg-[#050b18]">
      {/* Abstract Background Decor */}
      <div className="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/10 blur-[120px] rounded-full -translate-y-1/2 pointer-events-none"></div>
      <div className="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/10 blur-[120px] rounded-full translate-y-1/2 pointer-events-none"></div>

      <div className="max-w-7xl mx-auto flex flex-col items-center relative z-20">
        {/* Header */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.8 }}
          className="text-center mb-24"
        >
          <span className="text-blue-400 font-semibold tracking-[0.3em] uppercase text-xs md:text-sm mb-4 block">
            {t("ourLeadership") || "Our Leadership"}
          </span>
          <h2 className="text-3xl md:text-5xl font-bold text-white mb-8 tracking-tight leading-tight">
            {data?.title || settings.founders_title || t("aboutFoundersTag")}
          </h2>
          <p className="text-gray-400 text-sm md:text-lg max-w-2xl mx-auto leading-relaxed whitespace-pre-line font-light">
            {data?.description ||
              settings.founders_desc ||
              t("aboutFoundersDesc")}
          </p>
          <div className="w-12 h-[2px] bg-gradient-to-r from-transparent via-blue-500 to-transparent mx-auto mt-10"></div>
        </motion.div>

        {/* Founders Grid */}
        <motion.div 
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true }}
          className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 md:gap-14 w-full max-w-6xl"
        >
          {displayTeam.map((founder, index) => (
            <motion.div 
              key={index} 
              variants={itemVariants}
              className="group relative flex flex-col"
            >
              {/* Main Card Container */}
              <div className="relative aspect-[3/4] overflow-hidden rounded-2xl bg-[#0d2a57]/20 border border-white/10 backdrop-blur-sm transition-all duration-500 group-hover:border-blue-500/50 group-hover:shadow-[0_0_40px_-10px_rgba(59,130,246,0.3)]">
                
                {/* Image */}
                <Image
                  src={
                    founder.image.startsWith("/about-us") ||
                    founder.image.startsWith("http") ||
                    founder.image.startsWith("data")
                      ? founder.image
                      : `/about-us/${founder.image}`
                  }
                  alt={founder.name}
                  fill
                  sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                  className="object-cover object-top transition-transform duration-1000 ease-out group-hover:scale-110 group-hover:brightness-110"
                />

                {/* Glass Gradient Overlay */}
                <div className="absolute inset-0 bg-gradient-to-t from-[#050b18] via-[#050b18]/10 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-500"></div>

                {/* Hover Glow Effect */}
                <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 bg-[radial-gradient(circle_at_50%_120%,rgba(59,130,246,0.2),transparent_70%)] pointer-events-none"></div>

                {/* Content Overlay (Mobile-friendly reveal) */}
                <div className="absolute bottom-0 left-0 w-full p-8 translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                  <h3 className="text-white font-bold text-xl md:text-2xl tracking-wide mb-2">
                    {founder.name}
                  </h3>
                  <div className="h-[2px] w-0 bg-blue-500 group-hover:w-12 transition-all duration-500 ease-out mb-3"></div>
                  <p className="text-blue-300/90 text-sm font-medium tracking-widest uppercase opacity-0 group-hover:opacity-100 transition-all duration-500 delay-100">
                    {founder.role}
                  </p>
                </div>

                {/* Tech Corners */}
                <div className="absolute top-4 right-4 w-6 h-6 border-t border-r border-white/20 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                <div className="absolute bottom-4 left-4 w-6 h-6 border-b border-l border-white/20 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
              </div>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
};

export default Founders;

