"use client";
import React from "react";
import { motion } from "framer-motion";
import { FaCheckCircle } from "react-icons/fa";
import { useLanguage } from "../../context/LanguageContext";

const WebDevHowWeWork = () => {
  const { t } = useLanguage();
  const steps = [
    {
      id: 1,
      title: `1- ${t("designAndDevStrategy")}`,
      align: "right",
      items: [
        t("uxAnalysis"),
        t("devStrategy"),
        t("uiUxDesign"),
        t("systemArch"),
      ],
    },
    {
      id: 2,
      title: `2- ${t("designAndDevStrategy")}`,
      align: "left",
      items: [t("wireframing"), t("prototyping"), t("uiUxReview")],
    },
    {
      id: 3,
      title: `3- ${t("designAndDevStrategy")}`,
      align: "right",
      items: [t("frontendDev"), t("backendDev"), t("apiIntegration")],
    },
    {
      id: 4,
      title: `4- ${t("designAndDevStrategy")}`,
      align: "left",
      items: [t("qaTesting"), t("perfOptimization"), t("securityAudit")],
    },
    {
      id: 5,
      title: `5- ${t("designAndDevStrategy")}`,
      align: "right",
      items: [t("deployment"), t("maintenance"), t("clientSupport")],
    },
  ];

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-20">
          <motion.h3
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-[#D98B50] text-xl font-bold mb-4 tracking-widest uppercase"
          >
            {t("howWeWorkSubtitle")}
          </motion.h3>
          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="text-white text-3xl md:text-5xl font-bold leading-tight"
          >
            {t("howWeWorkTitle")}
          </motion.h2>
        </div>

        {/* Desktop Winding Timeline */}
        <div className="hidden lg:block relative mt-20 pb-20">
          {/* Custom SVG Winding Path */}
          <div className="absolute top-0 left-0 w-full h-full pointer-events-none flex justify-center">
            <svg
              className="w-[80%] h-full"
              viewBox="0 0 1000 1200"
              preserveAspectRatio="none"
            >
              {/* Winding dashed path */}
              <path
                d="M 800,0 V 150 Q 800,200 750,200 H 250 Q 200,200 200,250 V 400 Q 200,450 250,450 H 750 Q 800,450 800,500 V 650 Q 800,700 750,700 H 250 Q 200,700 200,750 V 900 Q 200,950 250,950 H 750 Q 800,950 800,1000 V 1150"
                fill="none"
                stroke="rgba(255,255,255,0.2)"
                strokeWidth="4"
                strokeDasharray="12 12"
              />
            </svg>
          </div>

          <div className="relative z-10 space-y-24">
            {steps.map((step, index) => (
              <motion.div
                key={step.id}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: "-100px" }}
                transition={{ duration: 0.6 }}
                className={`flex w-full ${
                  step.align === "right" ? "justify-end" : "justify-start"
                }`}
              >
                <div
                  className={`w-[45%] flex ${
                    step.align === "right"
                      ? "justify-start pl-[5%]"
                      : "justify-end pr-[5%]"
                  } relative`}
                >
                  {/* Point */}
                  <div
                    className={`absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-[#D98B50] shadow-[0_0_15px_rgba(217,139,80,0.8)] z-20 ${
                      step.align === "right" ? "left-[-10px]" : "right-[-10px]"
                    }`}
                  ></div>

                  <div className="bg-[#03071E]/80 backdrop-blur-md rounded-2xl p-8 border border-white/5 w-full max-w-sm hover:border-white/10 transition-colors">
                    <h4 className="text-white text-lg font-bold mb-4">
                      {step.title}
                    </h4>
                    <ul className="space-y-3">
                      {step.items.map((item, i) => (
                        <li
                          key={i}
                          className="flex items-center gap-3 text-gray-400 text-sm"
                        >
                          <FaCheckCircle className="text-[#D98B50] flex-shrink-0" />
                          <span>{item}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>

        {/* Mobile Vertical Timeline */}
        <div className="block lg:hidden relative mt-16">
          <div className="absolute left-6 top-0 bottom-0 w-1 bg-gradient-to-b from-transparent via-white/20 to-transparent border-l-2 border-dashed border-white/20"></div>

          <div className="space-y-12">
            {steps.map((step, index) => (
              <motion.div
                key={step.id}
                initial={{ opacity: 0, x: -30 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true, margin: "-50px" }}
                transition={{ duration: 0.5 }}
                className="relative pl-16"
              >
                {/* Point */}
                <div className="absolute left-[20px] top-6 w-4 h-4 rounded-full bg-[#D98B50] shadow-[0_0_15px_rgba(217,139,80,0.8)] z-20"></div>

                <div className="bg-[#03071E]/80 backdrop-blur-md rounded-2xl p-6 border border-white/5 hover:border-white/10 transition-colors">
                  <h4 className="text-white text-lg font-bold mb-4">
                    {step.title}
                  </h4>
                  <ul className="space-y-3">
                    {step.items.map((item, i) => (
                      <li
                        key={i}
                        className="flex items-center gap-3 text-gray-400 text-sm"
                      >
                        <FaCheckCircle className="text-[#D98B50] flex-shrink-0" />
                        <span>{item}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default WebDevHowWeWork;
