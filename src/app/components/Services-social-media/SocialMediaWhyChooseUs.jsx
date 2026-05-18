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
import { useLanguage } from "../../context/LanguageContext";

const SocialMediaWhyChooseUs = () => {
  const { t } = useLanguage();
  const reasons = [
    {
      id: 1,
      title: t("smReason1Title") || "Daily Reports",
      desc: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
      icon: <FiFileText className="text-4xl" />,
    },
    {
      id: 2,
      title: t("smReason2Title") || "Dedicated My Account",
      desc: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
      icon: <FiUserCheck className="text-4xl" />,
    },
    {
      id: 3,
      title: t("smReason3Title") || "Data protection",
      desc: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
      icon: <FiShield className="text-4xl" />,
    },
    {
      id: 4,
      title: t("smReason4Title") || "Proven track record",
      desc: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
      icon: <FiAward className="text-4xl" />,
    },
    {
      id: 5,
      title: t("smReason5Title") || "Data integration",
      desc: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
      icon: <FiShare2 className="text-4xl" />,
    },
    {
      id: 6,
      title: t("smReason6Title") || "Result Driven Work",
      desc: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
      icon: <FiTarget className="text-4xl" />,
    },
  ];

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
              {t("ourBenefits")}
            </p>
            <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight max-w-2xl">
              {t("smWhyChooseUsTitle")}
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

        {/* Right Image (Earth) */}
        <motion.div
          initial={{ opacity: 0, x: 50 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 1 }}
          className="absolute right-0 top-1/2 -translate-y-1/2 w-[40%] max-w-[600px] pointer-events-none hidden lg:block z-10"
        >
          <img
            src="/Hero/earth.png"
            alt="Earth"
            className="w-full max-h-[110vh] object-contain translate-x-[250px] scale-120 opacity-80"
          />
        </motion.div>
      </div>
    </section>
  );
};

export default SocialMediaWhyChooseUs;
