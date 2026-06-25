"use client";
import React from "react";
import { motion } from "framer-motion";
import {
  FiFileText,
  FiUserCheck,
  FiShield,
  FiAward,
  FiShare2,
  FiTarget,
} from "react-icons/fi";
import { useLanguage } from "@/app/context/LanguageContext";

const SocialMediaWhyChooseUs = ({ whyTitle, whySubtitle, whyList }) => {
  const { language, t } = useLanguage();
  const isRtl = language === "ar";
  const reasons =
    whyList && Array.isArray(whyList) && whyList.length > 0
      ? whyList.map((item, index) => {
          const icons = [
            <FiFileText className="text-4xl" key="1" />,
            <FiUserCheck className="text-4xl" key="2" />,
            <FiShield className="text-4xl" key="3" />,
            <FiAward className="text-4xl" key="4" />,
            <FiShare2 className="text-4xl" key="5" />,
            <FiTarget className="text-4xl" key="6" />,
          ];
          return {
            id: index + 1,
            title: item.title?.[language] || item.title || "",
            desc:
              item.desc?.[language] ||
              item.desc ||
              item.content?.[language] ||
              item.content ||
              "",
            icon: icons[index % icons.length],
          };
        })
      : [];

  if (reasons.length === 0) return null;

  return (
    <section className="relative w-full py-24 px-6 lg:px-20 overflow-hidden z-20">
      <div className="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">
        {/* Left Content */}
        <div className="w-full lg:w-[65%] z-20">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="mb-12"
          >
            <p className="text-[#D98B50] font-semibold uppercase tracking-wider mb-2">
              {whySubtitle?.[language] || whySubtitle || t("ourBenefits")}
            </p>
            <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight max-w-2xl">
              {whyTitle?.[language] || whyTitle || t("smWhyChooseUsTitle")}
            </h2>
          </motion.div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
            {reasons.map((reason, index) => (
              <motion.div
                key={reason.id}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: index * 0.1, duration: 0.5 }}
                className="flex items-start gap-4"
              >
                <div className="text-[#D98B50] mt-1 shrink-0">
                  {reason.icon}
                </div>
                <div>
                  <h3 className="text-white text-lg font-bold mb-2">
                    {reason.title}
                  </h3>
                  <p className="text-gray-400 text-sm leading-relaxed">
                    {reason.desc}
                  </p>
                </div>
              </motion.div>
            ))}
          </div>
        </div>

        {/* Right/Left Image (Earth) */}
        <motion.div
          initial={{ opacity: 0, x: isRtl ? -50 : 50 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 1 }}
          className={`absolute ${isRtl ? "left-0" : "right-0"} top-1/2 -translate-y-1/2 w-[40%] max-w-[600px] pointer-events-none hidden lg:block z-10`}
        >
          <img
            src="/Hero/earth.webp"
            alt="Earth"
            className={`w-full max-h-[110vh] object-contain opacity-50 brightness-50 contrast-125 ${
              isRtl
                ? "-translate-x-[250px] scale-x-[-1.2] scale-y-[1.2]"
                : "translate-x-[250px] scale-[1.2]"
            }`}
          />
        </motion.div>
      </div>
    </section>
  );
};

export default SocialMediaWhyChooseUs;
