"use client";
import React, { useRef } from "react";
import { motion, useScroll, useTransform, useSpring } from "framer-motion";
import { FaCheckCircle } from "react-icons/fa";
import { useLanguage } from "@/app/context/LanguageContext";

const MobileAppHowWeWork = ({ howWeWorkList }) => {
  const { language } = useLanguage();
  const isRtl = language === "ar";
  const containerRef = useRef(null);

  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start center", "end center"],
  });

  const pathLength = useSpring(scrollYProgress, {
    stiffness: 80,
    damping: 25,
    restDelta: 0.001,
  });

  const offsetDistance = useTransform(scrollYProgress, [0, 1], ["0%", "100%"]);

  // Horizontal S-curve winding path (Desktop)
  const path =
    "M 80,200 H 200 C 270,200 270,380 340,380 C 410,380 410,220 480,220 H 580 C 650,220 650,60 720,60 C 790,60 790,380 860,380 C 930,380 930,200 1000,200 H 1120";

  // Vertical winding path (Mobile)
  const mobilePath = "M 50,0 V 1500";

  // Localization Dictionary matching the image steps
  const translations = {
    en: {
      subtitle: "HOW WE WORK?",
      title: "We have a clear business plan from start to finish",
      steps: [
        {
          id: 1,
          title: "1. Design and Requirement analysis",
          items: [
            "UI/UX design",
            "Web Architecture",
            "Customer review and sign off",
            "Requirement documentation",
            "UI/UX design prototype",
          ],
        },
        {
          id: 2,
          title: "2. Development",
          items: [
            "UI/UX design",
            "Web Architecture",
            "Customer review and sign off",
            "UI/UX design",
          ],
        },
        {
          id: 3,
          title: "3. Design and Requirement analysis",
          items: [
            "UI/UX design",
            "Web Architecture",
            "Customer review and sign off",
            "Requirement documentation",
            "UI/UX design prototype",
          ],
        },
        {
          id: 4,
          title: "4. Product Launch",
          items: [
            "UI/UX design",
            "Web Architecture",
            "Customer review and sign off",
          ],
        },
        {
          id: 5,
          title: "5. Production Support",
          items: [
            "UI/UX design",
            "Web Architecture",
            "Customer review and sign off",
          ],
        },
        {
          id: 6,
          title: "6. Production Support",
          items: [
            "UI/UX design",
            "Web Architecture",
            "Customer review and sign off",
          ],
        },
      ],
    },
    ar: {
      subtitle: "كيف نعمل؟",
      title: "لدينا خطة عمل واضحة ومدروسة من البداية وحتى النهاية",
      steps: [
        {
          id: 1,
          title: "1. تحليل وتصميم المتطلبات",
          items: [
            "تصميم واجهات وتجربة المستخدم (UI/UX)",
            "البنية البرمجية وهيكلة التطبيق",
            "مراجعة وتوقيع العميل",
            "توثيق متطلبات المشروع",
            "النموذج الأولي لتجربة المستخدم",
          ],
        },
        {
          id: 2,
          title: "2. التطوير والبرمجة",
          items: [
            "تصميم واجهات وتجربة المستخدم (UI/UX)",
            "البنية البرمجية وهيكلة التطبيق",
            "مراجعة وتوقيع العميل",
            "برمجة الواجهات وتكامل التطبيق",
          ],
        },
        {
          id: 3,
          title: "3. تحليل وتصميم المتطلبات",
          items: [
            "تصميم واجهات وتجربة المستخدم (UI/UX)",
            "البنية البرمجية وهيكلة التطبيق",
            "مراجعة وتوقيع العميل",
            "توثيق متطلبات المشروع",
            "النموذج الأولي لتجربة المستخدم",
          ],
        },
        {
          id: 4,
          title: "4. إطلاق المنتج والتشغيل",
          items: [
            "تصميم واجهات وتجربة المستخدم (UI/UX)",
            "البنية البرمجية وهيكلة التطبيق",
            "مراجعة وتوقيع العميل",
          ],
        },
        {
          id: 5,
          title: "5. دعم المنتج والصيانة",
          items: [
            "تصميم واجهات وتجربة المستخدم (UI/UX)",
            "البنية البرمجية وهيكلة التطبيق",
            "مراجعة وتوقيع العميل",
          ],
        },
        {
          id: 6,
          title: "6. دعم المنتج والصيانة",
          items: [
            "تصميم واجهات وتجربة المستخدم (UI/UX)",
            "البنية البرمجية وهيكلة التطبيق",
            "مراجعة وتوقيع العميل",
          ],
        },
      ],
    },
  };

  const defaultTrans = translations[language] || translations.en;
  
  const activeTrans = {
    subtitle: defaultTrans.subtitle,
    title: defaultTrans.title,
    steps: howWeWorkList && howWeWorkList.length > 0 
      ? howWeWorkList.map((step, index) => {
          if (!step) return null;
          const itemTitle = step.title && typeof step.title === "object"
            ? (step.title[language] || step.title.en || "")
            : (step.title || "");
          const itemDesc = step.desc && typeof step.desc === "object"
            ? (step.desc[language] || step.desc.en || "")
            : (step.desc || "");
          return {
            id: index + 1,
            title: `${index + 1}. ${itemTitle}`,
            items: itemDesc ? itemDesc.split('\n').filter(Boolean) : [],
          };
        }).filter(Boolean)
      : defaultTrans.steps
  };

  if (activeTrans.steps.length === 0) return null;

  // Exact coordinates for placing step cards horizontally (Desktop)
  // Aligned perfectly next to/inside loops to prevent overlapping
  const cardPositions = [
    { left: "2%", top: "250px" }, // Step 1: Below start road
    { left: "20%", top: "40px" }, // Step 2: Above trough 1
    { left: "37.5%", top: "260px" }, // Step 3: Below middle flat
    { left: "54.5%", top: "140px" }, // Step 4: Below crest
    { left: "67%", top: "40px" }, // Step 5: Above trough 2
    { left: "83.5%", top: "250px" }, // Step 6: Below end road
  ];

  return (
    <section
      ref={containerRef}
      className="relative w-full py-24 px-6 lg:px-12  overflow-hidden border-t border-white/5"
    >
      {/* Decorative Galaxy Stars Background */}
      <div className="absolute inset-0 opacity-40 pointer-events-none">
        <div className="absolute top-1/4 left-1/4 w-[250px] h-[250px] rounded-full bg-blue-500/10 blur-[80px]"></div>
        <div className="absolute bottom-1/3 right-1/4 w-[300px] h-[300px] rounded-full bg-purple-600/10 blur-[100px]"></div>
        <div className="absolute top-10 left-10 w-2 h-2 rounded-full bg-white opacity-40 animate-pulse"></div>
        <div className="absolute top-1/3 right-20 w-3 h-3 rounded-full bg-white opacity-30 animate-pulse"></div>
        <div className="absolute bottom-1/4 left-1/3 w-2.5 h-2.5 rounded-full bg-white opacity-50 animate-pulse"></div>
      </div>

      <div className="max-w-7xl mx-auto relative z-10">
        {/* Title Section */}
        <div className="text-center mb-16">
          <motion.h3
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-[#E17F3D] text-lg font-bold mb-3 tracking-widest uppercase"
          >
            {activeTrans.subtitle}
          </motion.h3>
          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="text-white text-3xl md:text-5xl font-extrabold leading-tight tracking-tight"
          >
            {activeTrans.title}
          </motion.h2>
        </div>

        {/* Desktop Custom Winding Road Timeline */}
        <div className="hidden lg:block relative w-full h-[520px] mt-24">
          <div className="absolute inset-0 flex justify-center items-center pointer-events-none">
            <svg
              className="w-full h-full"
              viewBox="0 0 1200 480"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              {/* Glow Filter */}
              <defs>
                <filter
                  id="road-glow"
                  x="-10%"
                  y="-10%"
                  width="120%"
                  height="120%"
                >
                  <feGaussianBlur stdDeviation="6" result="blur" />
                  <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
              </defs>

              {/* Main Outer Road Border/Glow */}
              <path
                d={path}
                stroke="#0e173a"
                strokeWidth="64"
                strokeLinecap="round"
                opacity="0.8"
              />

              {/* Main Inner Road Tube */}
              <path
                d={path}
                stroke="#090f26"
                strokeWidth="56"
                strokeLinecap="round"
              />

              {/* Center Dashed Lane Line */}
              <path
                d={path}
                stroke="#ffffff"
                strokeWidth="3"
                strokeDasharray="14 14"
                strokeLinecap="round"
                opacity="0.55"
              />

              {/* Animated Glowing Cap following the path */}
              <motion.circle
                r="7"
                fill="#E17F3D"
                filter="url(#road-glow)"
                style={{
                  offsetPath: `path('${path}')`,
                  offsetDistance,
                }}
              />

              {/* Start Orange Circular Cap */}
              <circle
                cx="80"
                cy="200"
                r="10"
                fill="#E17F3D"
                filter="url(#road-glow)"
              />

              {/* Step 2 T-Marker (bottom of trough 1) */}
              <line
                x1="340"
                y1="380"
                x2="340"
                y2="420"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <line
                x1="330"
                y1="420"
                x2="350"
                y2="420"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <circle cx="340" cy="420" r="5" fill="#E17F3D" />

              {/* Step 3 T-Marker (middle flat) */}
              <line
                x1="530"
                y1="220"
                x2="530"
                y2="260"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <line
                x1="520"
                y1="260"
                x2="540"
                y2="260"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <circle cx="530" cy="260" r="5" fill="#E17F3D" />

              {/* Step 4 T-Marker (top of crest) */}
              <line
                x1="720"
                y1="60"
                x2="720"
                y2="20"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <line
                x1="710"
                y1="20"
                x2="730"
                y2="20"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <circle cx="720" cy="20" r="5" fill="#E17F3D" />

              {/* Step 5 T-Marker (bottom of trough 2) */}
              <line
                x1="860"
                y1="380"
                x2="860"
                y2="420"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <line
                x1="850"
                y1="420"
                x2="870"
                y2="420"
                stroke="#E17F3D"
                strokeWidth="4"
              />
              <circle cx="860" cy="420" r="5" fill="#E17F3D" />

              {/* End Orange Circular Cap */}
              <circle
                cx="1120"
                cy="200"
                r="10"
                fill="#E17F3D"
                filter="url(#road-glow)"
              />
            </svg>
          </div>

          {/* Absolute-Positioned Step Cards */}
          {activeTrans.steps.map((step, idx) => {
            const pos = cardPositions[idx];
            return (
              <motion.div
                key={step.id}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.6, delay: idx * 0.1 }}
                className="absolute w-[210px] bg-[#070b24]/90 backdrop-blur-md border border-white/5 hover:border-white/10 rounded-xl p-4 shadow-2xl transition-all hover:-translate-y-1 hover:shadow-[#e17f3d]/5 group"
                style={{
                  left: pos.left,
                  top: pos.top,
                }}
                dir={isRtl ? "rtl" : "ltr"}
              >
                <h4 className="text-white text-sm md:text-base font-extrabold mb-3 group-hover:text-[#E17F3D] transition-colors leading-tight">
                  {step.title}
                </h4>
                <ul className="space-y-2">
                  {step.items.map((item, i) => (
                    <li
                      key={i}
                      className="flex items-start gap-2 text-gray-400 text-xs"
                    >
                      <FaCheckCircle className="text-[#E17F3D] mt-0.5 flex-shrink-0" />
                      <span className="leading-tight">{item}</span>
                    </li>
                  ))}
                </ul>
              </motion.div>
            );
          })}
        </div>

        {/* Mobile/Tablet Vertical Custom Road Timeline */}
        <div className="block lg:hidden relative mt-12">
          {/* Vertical Road Track */}
          <div className={`absolute top-0 bottom-0 w-[40px] flex justify-center pointer-events-none ${isRtl ? "right-3" : "left-3"}`}>
            <svg className="w-full h-full" preserveAspectRatio="none">
              {/* Outer Glow */}
              <line
                x1="20"
                y1="0"
                x2="20"
                y2="100%"
                stroke="#0e173a"
                strokeWidth="24"
                opacity="0.6"
              />
              {/* Road Tube */}
              <line
                x1="20"
                y1="0"
                x2="20"
                y2="100%"
                stroke="#090f26"
                strokeWidth="20"
              />
              {/* Center dashed line */}
              <line
                x1="20"
                y1="0"
                x2="20"
                y2="100%"
                stroke="#ffffff"
                strokeWidth="2"
                strokeDasharray="10 10"
                opacity="0.5"
              />
            </svg>
          </div>

          <div className={`space-y-8 relative z-10 ${isRtl ? "pr-14 pl-4" : "pl-14 pr-4"}`}>
            {activeTrans.steps.map((step, idx) => (
              <motion.div
                key={step.id}
                initial={{ opacity: 0, x: -30 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true, margin: "-50px" }}
                transition={{ duration: 0.5, delay: idx * 0.05 }}
                className="relative bg-[#070b24]/90 backdrop-blur-md border border-white/5 hover:border-white/10 rounded-2xl p-5 shadow-xl hover:shadow-[#e17f3d]/5 group"
                dir={isRtl ? "rtl" : "ltr"}
              >
                {/* Connecting Pin to the Road */}
                <div
                  className={`absolute ${isRtl ? "right-[-32px]" : "left-[-32px]"} top-6 w-4 h-4 rounded-full bg-[#E17F3D] border-4 border-[#070b24] shadow-lg group-hover:scale-125 transition-transform z-20`}
                ></div>

                <h4 className="text-white text-base md:text-lg font-extrabold mb-3 group-hover:text-[#E17F3D] transition-colors leading-tight">
                  {step.title}
                </h4>
                <ul className="space-y-2">
                  {step.items.map((item, i) => (
                    <li
                      key={i}
                      className="flex items-center gap-2.5 text-gray-400 text-sm"
                    >
                      <FaCheckCircle className="text-[#E17F3D] flex-shrink-0" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
              </motion.div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default MobileAppHowWeWork;
