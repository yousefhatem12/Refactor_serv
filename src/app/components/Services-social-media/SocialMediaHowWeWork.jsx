"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const SocialMediaHowWeWork = ({ howWeWorkTitle, howWeWorkSubtitle, howWeWorkList }) => {
  const { language, t } = useLanguage();
  const steps = (howWeWorkList && Array.isArray(howWeWorkList) && howWeWorkList.length > 0)
    ? howWeWorkList.map((item, index) => {
        if (!item) return null;
        const idNum = index + 1;
        const idString = idNum < 10 ? `0${idNum}` : `${idNum}`;
        const icons = [
          "/services-social-media/icon-1.png",
          "/services-social-media/icon-2.png",
          "/services-social-media/icon-3.png",
          "/services-social-media/icon-4.png",
        ];
        return {
          id: idString,
          title: item.title?.[language] || item.title || "",
          desc: item.desc?.[language] || item.desc || item.content?.[language] || item.content || "",
          icon: icons[index % icons.length],
          align: index % 2 === 0 ? "left" : "right",
        };
      }).filter(Boolean)
    : [];

  if (steps.length === 0) return null;

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-24 max-w-4xl mx-auto">
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-[#D98B50] font-semibold uppercase tracking-wider mb-2"
          >
            {howWeWorkSubtitle?.[language] || howWeWorkSubtitle || t("howWeWorkSubtitle")}
          </motion.p>
          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="text-white text-3xl md:text-5xl font-bold leading-tight"
          >
            {howWeWorkTitle?.[language] || howWeWorkTitle || t("howWeWorkTitle")}
          </motion.h2>
        </div>

        <div className="relative w-full max-w-4xl mx-auto flex flex-col gap-12 md:gap-16">
          {steps.map((step, index) => (
            <motion.div
              key={step.id}
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 0.7 }}
              className={`flex flex-col md:flex-row items-center gap-6 md:gap-12 relative ${
                step.align === "left"
                  ? "md:flex-row md:-translate-x-10 lg:-translate-x-16"
                  : "md:flex-row-reverse md:translate-x-10 lg:translate-x-16"
              }`}
            >
              {/* Background large number */}
              <div
                className={`absolute top-1/2 -translate-y-1/2 text-white/15 font-bold text-8xl md:text-[150px] leading-none z-0 ${step.align === "left" ? "left-[-50px] md:left-[-100px]" : "right-[-50px] md:right-[-100px]"}`}
              >
                {step.id}
              </div>

              {/* Icon Image */}
              <div
                className={`w-full md:w-1/2 flex ${step.align === "left" ? "justify-end" : "justify-start"} z-10 relative`}
              >
                <div className="w-48 h-48 md:w-48 md:h-48 relative">
                  <img
                    src={step.icon}
                    alt={step.title}
                    className="w-full h-full object-contain"
                  />
                </div>
              </div>

              {/* Text Content */}
              <div
                className={`w-full md:w-1/2 z-10 flex flex-col ${step.align === "left" ? "items-start text-left" : "items-end text-right md:text-left md:items-start"}`}
              >
                <h3 className="text-[#D98B50] text-2xl md:text-3xl font-bold mb-4 flex items-center gap-3">
                  {step.align === "right" && (
                    <span className="hidden md:inline text-white/20 text-3xl">
                      {step.id}.
                    </span>
                  )}
                  {step.title}
                </h3>
                <p className="text-gray-400 text-base md:text-lg leading-relaxed max-w-sm">
                  {step.desc}
                </p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default SocialMediaHowWeWork;
