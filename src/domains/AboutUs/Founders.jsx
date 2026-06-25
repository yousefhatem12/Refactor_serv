"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { fetchAPI, getMediaUrl } from "@/lib/api";

const Founders = ({ data }) => {
  const { language, t } = useLanguage();

  // The DB team has 'name', 'role', 'image'
  // The global /team has 'name', 'role', 'image'
  // We use data.items if available, otherwise fetch from /team, otherwise fallback.
  const founders = [
    {
      name: language === "ar" ? "جون دو" : "John Doe",
      role: t("founder1Role"),
      image: "person-1.webp",
    },
    {
      name: language === "ar" ? "جين سميث" : "Jane Smith",
      role: t("founder2Role"),
      image: "person-2.webp",
    },
    {
      name: language === "ar" ? "أحمد علي" : "Ahmed Ali",
      role: t("founder3Role"),
      image: "person-3.webp",
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
  }, [data, language]);

  const displayTeam =
    data?.items?.length > 0
      ? data.items.map((member) => ({
          name: member.name,
          role: member.role,
          image: member.image
            ? getMediaUrl(member.image)
            : "/about-us/person-1.webp",
        }))
      : team.length > 0
        ? team.map((member) => ({
            name: member.name,
            role: member.role,
            image: member.image
              ? getMediaUrl(member.image)
              : "/about-us/person-1.webp",
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
    <section className="py-32 px-6 md:px-16 lg:px-24 w-full relative overflow-hidden">
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
          <span className="text-blue-500 font-black tracking-[0.4em] uppercase text-[10px] md:text-xs mb-6 block opacity-80">
            {t("ourLeadership") || "Our Leadership"}
          </span>
          <h2 className="text-4xl md:text-6xl font-black text-white mb-10 tracking-tighter leading-none text-cinematic">
            {data?.title || settings.founders_title || t("aboutFoundersTag")}
          </h2>
          <p className="text-gray-400 text-base md:text-xl max-w-3xl mx-auto leading-relaxed whitespace-pre-line font-light text-balance">
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
          className="grid grid-cols-2 md:grid-cols-3 gap-6 md:gap-8 w-full max-w-6xl"
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
                  <h3 className="text-white font-black transform translate-y-10 group-hover:translate-y-0 transition-transform duration-400 text-xl md:text-xl tracking-tight mb-2">
                    {founder.name}
                  </h3>
                  <div className="h-[2px] w-0 bg-blue-500 group-hover:w-16 transition-all duration-700 ease-out mb-4 delay-100"></div>
                  <p className="text-blue-400 text-xs font-black tracking-[0.2em] uppercase hidden  group-hover:block opacity-0 group-hover:opacity-100 transition-all duration-700 delay-100">
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
