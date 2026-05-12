"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";

const WebDesignRequest = () => {
  const { t } = useLanguage();
  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      {/* Small planet image acting as a background element */}
      <div className="absolute left-1/2 -translate-x-1/2 top-0 -z-10 opacity-30 pointer-events-none mix-blend-screen max-w-[400px] w-full">
        <img
          src="/Services/Blue Planet.png"
          alt="Blue Planet Background"
          className="w-full h-auto"
        />
      </div>

      <div className="max-w-4xl mx-auto text-center relative z-10">
        <motion.h2
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-[#D98B50] text-3xl md:text-5xl font-bold mb-4"
        >
          {t("wdRequestTitle")}
        </motion.h2>
        <motion.p
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ delay: 0.2 }}
          className="text-gray-300 text-lg mb-10"
        >
          {t("wdRequestDesc")}
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ delay: 0.4 }}
          className="flex flex-wrap justify-center gap-6"
        >
          <button className="px-10 py-3 rounded-full bg-white text-[#040A37] font-semibold text-lg hover:bg-gray-200 transition-colors shadow-lg">
            {t("orderNow")}
          </button>
          <button className="px-10 py-3 rounded-full bg-transparent border border-white text-white font-semibold text-lg hover:bg-white/10 transition-colors shadow-lg">
            {t("contact")}
          </button>
        </motion.div>
      </div>
    </section>
  );
};

export default WebDesignRequest;
