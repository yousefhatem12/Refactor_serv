"use client";

import React, { useState, useEffect } from "react";
import Image from "next/image";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import {
  FaLinkedin,
  FaGithub,
  FaTwitter,
  FaCode,
  FaPaintBrush,
  FaChartLine,
  FaUsers,
} from "react-icons/fa";
import { RiTeamLine } from "react-icons/ri";

const getIcon = (iconName) => {
  switch (iconName) {
    case "FaCode":
      return <FaCode className="w-8 h-8 text-[#FF8A00]" />;
    case "FaPaintBrush":
      return <FaPaintBrush className="w-8 h-8 text-indigo-500" />;
    case "FaUsers":
      return <FaUsers className="w-8 h-8 text-blue-500" />;
    case "FaChartLine":
      return <FaChartLine className="w-8 h-8 text-green-500" />;
    default:
      return <FaUsers className="w-8 h-8 text-blue-500" />;
  }
};

const defaultTeam = [
  {
    nameEn: "MOHAMED ABDELAL",
    nameAr: "محمد عبد العال",
    roleEn: "Chief Creative Officer & Founder",
    roleAr: "المدير الإبداعي التنفيذي وشريك مؤسس",
    image: "/about-us/person-1.png",
    dept: "management",
    socials: {
      linkedin: "https://linkedin.com",
      github: "#",
      twitter: "https://x.com",
    },
  },
  {
    nameEn: "AHMED MOHAMED",
    nameAr: "أحمد محمد",
    roleEn: "Chief Technology Officer & Founder",
    roleAr: "الرئيس التنفيذي للتكنولوجيا وشريك مؤسس",
    image: "/about-us/person-2.png",
    dept: "management",
    socials: {
      linkedin: "https://linkedin.com",
      github: "https://github.com",
      twitter: "https://x.com",
    },
  },
  {
    nameEn: "AHMED RAMADAN",
    nameAr: "أحمد رمضان",
    roleEn: "Chief Marketing Officer & Founder",
    roleAr: "رئيس قطاع التسويق وشريك مؤسس",
    image: "/about-us/person-3.png",
    dept: "management",
    socials: {
      linkedin: "https://linkedin.com",
      github: "#",
      twitter: "https://x.com",
    },
  },
  {
    nameEn: "Sarah Jenkins",
    nameAr: "سارة جينكينز",
    roleEn: "Lead UI/UX Designer",
    roleAr: "رئيسة مصممي واجهة وتجربة المستخدم",
    image: "/about-us/person-1.png",
    dept: "design",
    socials: {
      linkedin: "https://linkedin.com",
      github: "#",
      twitter: "https://x.com",
    },
  },
  {
    nameEn: "David Chen",
    nameAr: "ديفيد تشين",
    roleEn: "Senior Full Stack Developer",
    roleAr: "مطور ويب متكامل أول",
    image: "/about-us/person-2.png",
    dept: "development",
    socials: {
      linkedin: "https://linkedin.com",
      github: "https://github.com",
      twitter: "https://x.com",
    },
  },
  {
    nameEn: "Amira Youssef",
    nameAr: "أميرة يوسف",
    roleEn: "Senior Frontend Engineer",
    roleAr: "مهندسة واجهات أمامية أولى",
    image: "/about-us/person-3.png",
    dept: "development",
    socials: {
      linkedin: "https://linkedin.com",
      github: "https://github.com",
      twitter: "https://x.com",
    },
  },
  {
    nameEn: "Kareem Mahmoud",
    nameAr: "كريم محمود",
    roleEn: "SEO & Growth Marketing Expert",
    roleAr: "خبير سيو وتسويق نمو رقمي",
    image: "/about-us/person-2.png",
    dept: "marketing",
    socials: {
      linkedin: "https://linkedin.com",
      github: "#",
      twitter: "https://x.com",
    },
  },
  {
    nameEn: "Emily Taylor",
    nameAr: "إيميلي تايلور",
    roleEn: "Art Director",
    roleAr: "المدير الفني الفني الإبداعي",
    image: "/about-us/person-1.png",
    dept: "design",
    socials: {
      linkedin: "https://linkedin.com",
      github: "#",
      twitter: "https://x.com",
    },
  },
];

export default function TeamWork({ data }) {
  const { t, language } = useLanguage();
  const [selectedDept, setSelectedDept] = useState("all");
  const [dbTeam, setDbTeam] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isExpanded, setIsExpanded] = useState(false);

  useEffect(() => {
    setIsExpanded(false);
  }, [selectedDept]);

  useEffect(() => {
    if (!data) {
      fetchAPI(`/team?lang=${language}`)
        .then((res) => {
          if (res && Array.isArray(res) && res.length > 0) {
            setDbTeam(res);
          }
        })
        .catch((err) =>
          console.warn("Failed to fetch database team data:", err),
        )
        .finally(() => setLoading(false));
    } else {
      setLoading(false);
    }
  }, [data, language]);

  // Determine section titles & descriptions
  const sectionTitle = data?.title || t("teamHeroTitle");
  const sectionDesc = data?.description || t("teamHeroDesc");

  // Determine Pillars
  const displayPillars =
    data?.pillars && Array.isArray(data.pillars) && data.pillars.length > 0
      ? data.pillars.map((p) => ({
          title: p.title,
          desc: p.description,
          icon: getIcon(p.icon),
        }))
      : [
          {
            title:
              language === "ar" ? "التعاون والابتكار" : "Synergy & Innovation",
            desc:
              language === "ar"
                ? "خلق بيئة تفاعلية تتلاقى فيها الرؤى والأفكار لتقديم حلول برمجية استثنائية."
                : "Fostering a creative environment where diverse ideas cross-pollinate to engineer cutting-edge digital products.",
            icon: <FaUsers className="w-8 h-8 text-blue-500" />,
          },
          {
            title:
              language === "ar"
                ? "التميز التقني والبرمجي"
                : "Technical Excellence",
            desc:
              language === "ar"
                ? "الالتزام بكتابة أكواد نظيفة، تصميم بنية تحتية قوية، واستخدام أحدث التقنيات التقنية."
                : "Committed to state-of-the-art software craftsmanship, solid system design, and clean, future-proof architectures.",
            icon: <FaCode className="w-8 h-8 text-[#FF8A00]" />,
          },
          {
            title:
              language === "ar"
                ? "التصميم ذو الأثر الفعال"
                : "Impact-Driven Design",
            desc:
              language === "ar"
                ? "دمج الذوق الإبداعي بمتطلبات تجربة المستخدم لخلق هويات رقمية مذهلة تحقق النتائج."
                : "Blending visual mastery with user psychology to forge beautiful, seamless, and high-converting customer journeys.",
            icon: <FaPaintBrush className="w-8 h-8 text-indigo-500" />,
          },
        ];

  // Determine display team members
  let displayTeam = [];
  if (data?.members && Array.isArray(data.members) && data.members.length > 0) {
    displayTeam = data.members.map((member) => ({
      name: member.name,
      role: member.role,
      image: member.image
        ? getMediaUrl(member.image)
        : "/about-us/person-1.png",
      dept: member.dept || "development",
      socials: {
        linkedin: member.linkedin || "https://linkedin.com",
        github: member.github || "#",
        twitter: member.twitter || "https://x.com",
      },
    }));
  } else if (dbTeam.length > 0) {
    displayTeam = dbTeam.map((member) => ({
      name: member.name,
      role: member.role,
      image: member.image
        ? getMediaUrl(member.image)
        : "/about-us/person-1.png",
      dept: member.department || "development",
      socials: {
        linkedin: member.linkedin_url || "https://linkedin.com",
        github: member.github_url || "#",
        twitter: member.twitter_url || "https://x.com",
      },
    }));
  } else {
    displayTeam = defaultTeam.map((member) => ({
      name: language === "ar" ? member.nameAr : member.nameEn,
      role: language === "ar" ? member.roleAr : member.roleEn,
      image: member.image,
      dept: member.dept,
      socials: member.socials,
    }));
  }

  // Filtering Logic
  const filteredTeam =
    selectedDept === "all"
      ? displayTeam
      : displayTeam.filter(
          (member) => member.dept.toLowerCase() === selectedDept.toLowerCase(),
        );

  const hasMore = filteredTeam.length > 6;
  const visibleTeam = isExpanded ? filteredTeam : filteredTeam.slice(0, 6);

  const departments = [
    { id: "all", label: t("deptAll") },
    { id: "management", label: t("deptManagement") },
    { id: "development", label: t("deptDevelopment") },
    { id: "design", label: t("deptDesign") },
    { id: "marketing", label: t("deptMarketing") },
  ];

  // Animation Variants
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: { staggerChildren: 0.15 },
    },
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
      opacity: 1,
      y: 0,
      transition: { duration: 0.7, ease: [0.25, 1, 0.5, 1] },
    },
  };

  return (
    <section className="w-full relative overflow-hidden">
      {/* Decorative Blur Orbs */}
      <div className="absolute top-[20%] left-[-10%] w-[450px] h-[450px] bg-blue-600/10 blur-[150px] rounded-full pointer-events-none"></div>
      <div className="absolute top-[50%] right-[-10%] w-[500px] h-[500px] bg-[#FF8A00]/5 blur-[180px] rounded-full pointer-events-none"></div>

      {/* Title & Header */}
      <div className="relative z-20 pt-20 pb-12 px-6 md:px-16 lg:px-24 max-w-7xl mx-auto w-full text-center">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.8 }}
          className="flex flex-col items-center"
        >
          <div className="flex items-center gap-3 bg-blue-500/10 border border-blue-500/20 px-4 py-2 rounded-full mb-6">
            <RiTeamLine className="w-5 h-5 text-blue-400" />
            <span className="text-blue-400 font-semibold tracking-wider text-xs md:text-sm uppercase">
              {t("teamWork")}
            </span>
          </div>

          <h2 className="text-4xl md:text-5xl font-black text-white mb-8 tracking-tighter leading-none max-w-4xl text-cinematic">
            {sectionTitle}
          </h2>
          <p className="text-gray-300 text-base md:text-lg max-w-3xl leading-relaxed font-light text-balance mb-12">
            {sectionDesc}
          </p>

          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-[#FF8A00] to-transparent"></div>
        </motion.div>
      </div>

      {/* Pillars Section */}
      <div className="relative z-20 py-10 px-6 md:px-16 lg:px-24 max-w-7xl mx-auto w-full">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {displayPillars.map((pillar, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 40 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8, delay: idx * 0.1 }}
              whileHover={{ y: -6, transition: { duration: 0.2 } }}
              className="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-8 flex flex-col items-start transition-all duration-300 hover:border-blue-500/30 hover:shadow-[0_12px_40px_-15px_rgba(59,130,246,0.15)]"
            >
              <div className="p-4 bg-white/5 rounded-xl border border-white/10 mb-6 flex items-center justify-center">
                {pillar.icon}
              </div>
              <h3 className="text-xl font-bold text-white mb-4">
                {pillar.title}
              </h3>
              <p className="text-gray-400 text-sm leading-relaxed font-light">
                {pillar.desc}
              </p>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Filterable Team Section */}
      <div className="relative z-20 py-16 px-6 md:px-16 lg:px-24 max-w-7xl mx-auto w-full">
        <div className="flex flex-col items-center mb-16">
          <span className="text-[#FF8A00] font-black tracking-[0.3em] uppercase text-xs mb-4">
            {t("ourTeam")}
          </span>
          <div className="w-12 h-[2px] bg-gradient-to-r from-transparent via-[#FF8A00] to-transparent mb-10"></div>

          {/* Filtering buttons */}
          <div className="flex flex-wrap justify-center gap-3 max-w-3xl">
            {departments.map((dept) => (
              <button
                key={dept.id}
                onClick={() => setSelectedDept(dept.id)}
                className={`px-6 py-2.5 rounded-full text-xs md:text-sm font-semibold uppercase tracking-wider transition-all duration-300 border ${
                  selectedDept === dept.id
                    ? "bg-[#FF8A00] text-white border-[#FF8A00] shadow-[0_0_20px_rgba(255,138,0,0.3)]"
                    : "bg-white/5 text-white/60 border-white/10 hover:border-white/30 hover:text-white"
                }`}
              >
                {dept.label}
              </button>
            ))}
          </div>
        </div>

        {/* Team Grid */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true }}
          layout
          className="grid grid-cols-2 lg:grid-cols-4 md:grid-cols-3 gap-6 md:gap-8 w-full"
        >
          <AnimatePresence mode="popLayout">
            {visibleTeam.map((member, index) => (
              <motion.div
                key={member.name + "-" + index}
                layout
                variants={itemVariants}
                initial="hidden"
                animate="visible"
                exit={{ opacity: 0, scale: 0.8 }}
                className="group relative flex flex-col"
              >
                {/* Main Card Container */}
                <div className="relative aspect-[3/4] overflow-hidden rounded-2xl bg-white/[0.02] border border-white/10 backdrop-blur-sm transition-all duration-500 group-hover:border-blue-500/50 group-hover:shadow-[0_0_40px_-10px_rgba(59,130,246,0.3)]">
                  {/* Image */}
                  <Image
                    src={member.image}
                    alt={member.name}
                    fill
                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 25vw"
                    className="object-cover object-top transition-transform duration-1000 ease-out group-hover:scale-110 group-hover:brightness-110"
                  />

                  {/* Dark Glass Overlay */}
                  <div className="absolute inset-0 bg-gradient-to-t from-[#02020a] via-[#02020a]/30 to-transparent opacity-85 group-hover:opacity-75 transition-opacity duration-500"></div>

                  {/* Hover Glow Ring */}
                  <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 bg-[radial-gradient(circle_at_50%_120%,rgba(59,130,246,0.2),transparent_70%)] pointer-events-none"></div>

                  {/* Social Links on Hover */}
                  <div className="absolute top-4 right-4 flex flex-col gap-2 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500 ease-out">
                    {member.socials.linkedin &&
                      member.socials.linkedin !== "#" && (
                        <a
                          href={member.socials.linkedin}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="w-8 h-8 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-blue-600 hover:border-blue-500 transition-all duration-300"
                        >
                          <FaLinkedin className="w-3.5 h-3.5" />
                        </a>
                      )}
                    {member.socials.github && member.socials.github !== "#" && (
                      <a
                        href={member.socials.github}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="w-8 h-8 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-neutral-800 hover:border-neutral-700 transition-all duration-300"
                      >
                        <FaGithub className="w-3.5 h-3.5" />
                      </a>
                    )}
                    {member.socials.twitter &&
                      member.socials.twitter !== "#" && (
                        <a
                          href={member.socials.twitter}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="w-8 h-8 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-sky-500 hover:border-sky-400 transition-all duration-300"
                        >
                          <FaTwitter className="w-3.5 h-3.5" />
                        </a>
                      )}
                  </div>

                  {/* Content Overlay */}
                  <div className="absolute bottom-0 left-0 w-full p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                    <h3 className="text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-400 font-bold text-md md:text-lg tracking-tight mb-2">
                      {member.name}
                    </h3>
                    <div className="h-[2px] w-0 bg-blue-500 group-hover:w-12 transition-all duration-700 ease-out mb-3 delay-100"></div>
                    <p className="text-blue-400 text-[10px] font-bold tracking-[0.2em] hidden  group-hover:block uppercase opacity-0 group-hover:opacity-100 transition-all duration-700 delay-100">
                      {member.role}
                    </p>
                  </div>

                  {/* Tech corner accents */}
                  <div className="absolute top-4 left-4 w-4 h-4 border-t border-l border-white/10 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                  <div className="absolute bottom-4 right-4 w-4 h-4 border-b border-r border-white/10 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                </div>
              </motion.div>
            ))}

            {hasMore && !isExpanded && (
              <motion.div
                key="show-more-card"
                layout
                variants={itemVariants}
                initial="hidden"
                animate="visible"
                exit={{ opacity: 0, scale: 0.8 }}
                onClick={() => setIsExpanded(true)}
                className="group relative flex flex-col cursor-pointer"
              >
                {/* Main Card Container with Cinematic Shadow */}
                <div className="relative aspect-[3/4] overflow-hidden rounded-2xl bg-white/[0.02] border border-white/10 backdrop-blur-sm transition-all duration-500 hover:border-blue-500/50 hover:shadow-[0_0_50px_rgba(59,130,246,0.4)] shadow-[0_10px_30px_rgba(59,130,246,0.15)] flex flex-col items-center justify-center p-6 text-center">
                  {/* Glowing ambient background shadow */}
                  <div className="absolute inset-0 bg-gradient-to-tr from-blue-600/10 via-transparent to-transparent opacity-65 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl pointer-events-none"></div>

                  {/* Pulsing glow for cinematic shadow effect */}
                  <div className="absolute -inset-1 rounded-3xl bg-gradient-to-r from-blue-500/20 to-indigo-500/20 opacity-0 group-hover:opacity-100 blur-xl transition duration-1000 group-hover:duration-300 pointer-events-none"></div>

                  {/* Circular icon container */}
                  <div className="relative z-10 w-16 h-16 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-5 transition-all duration-500 group-hover:scale-115 group-hover:bg-blue-600/15 group-hover:border-blue-500/30">
                    <RiTeamLine className="w-8 h-8 text-blue-400 group-hover:text-blue-300 transition-colors" />
                  </div>

                  {/* Text labels */}
                  <span className="relative z-10 text-white font-extrabold text-lg tracking-wider uppercase group-hover:text-blue-400 transition-colors">
                    {t("showMore")}
                  </span>
                  <p className="relative z-10 text-gray-400 text-xs mt-2 font-light max-w-[200px]">
                    {language === "ar"
                      ? "تصفح باقي أعضاء فريقنا المتميز"
                      : "Browse the rest of our talented team"}
                  </p>

                  {/* Tech accents */}
                  <div className="absolute top-4 left-4 w-4 h-4 border-t border-l border-white/10 opacity-50 group-hover:opacity-100 transition-all duration-500"></div>
                  <div className="absolute bottom-4 right-4 w-4 h-4 border-b border-r border-white/10 opacity-50 group-hover:opacity-100 transition-all duration-500"></div>
                </div>
              </motion.div>
            )}

            {hasMore && isExpanded && (
              <motion.div
                key="show-less-card"
                layout
                variants={itemVariants}
                initial="hidden"
                animate="visible"
                exit={{ opacity: 0, scale: 0.8 }}
                onClick={() => setIsExpanded(false)}
                className="group relative flex flex-col cursor-pointer"
              >
                {/* Main Card Container with Cinematic Shadow */}
                <div className="relative aspect-[3/4] overflow-hidden rounded-2xl bg-white/[0.02] border border-white/10 backdrop-blur-sm transition-all duration-500 hover:border-[#FF8A00]/50 hover:shadow-[0_0_50px_rgba(255,138,0,0.4)] shadow-[0_10px_30px_rgba(255,138,0,0.15)] flex flex-col items-center justify-center p-6 text-center">
                  {/* Glowing ambient background shadow */}
                  <div className="absolute inset-0 bg-gradient-to-tr from-[#FF8A00]/10 via-transparent to-transparent opacity-65 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl pointer-events-none"></div>

                  {/* Pulsing glow for cinematic shadow effect */}
                  <div className="absolute -inset-1 rounded-3xl bg-gradient-to-r from-[#FF8A00]/20 to-red-500/20 opacity-0 group-hover:opacity-100 blur-xl transition duration-1000 group-hover:duration-300 pointer-events-none"></div>

                  {/* Circular icon container */}
                  <div className="relative z-10 w-16 h-16 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-5 transition-all duration-500 group-hover:scale-115 group-hover:bg-[#FF8A00]/15 group-hover:border-[#FF8A00]/30">
                    <RiTeamLine className="w-8 h-8 text-[#FF8A00] group-hover:text-[#FF8A00] transition-colors" />
                  </div>

                  {/* Text labels */}
                  <span className="relative z-10 text-white font-extrabold text-lg tracking-wider uppercase group-hover:text-[#FF8A00] transition-colors">
                    {language === "ar" ? "عرض أقل" : "Show Less"}
                  </span>
                  <p className="relative z-10 text-gray-400 text-xs mt-2 font-light max-w-[200px]">
                    {language === "ar"
                      ? "إخفاء باقي أعضاء الفريق"
                      : "Collapse the team list"}
                  </p>

                  {/* Tech accents */}
                  <div className="absolute top-4 left-4 w-4 h-4 border-t border-l border-white/10 opacity-50 group-hover:opacity-100 transition-all duration-500"></div>
                  <div className="absolute bottom-4 right-4 w-4 h-4 border-b border-r border-white/10 opacity-50 group-hover:opacity-100 transition-all duration-500"></div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </motion.div>
      </div>
    </section>
  );
}
