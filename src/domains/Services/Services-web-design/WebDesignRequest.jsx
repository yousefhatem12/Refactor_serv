"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const WebDesignRequest = () => {
  const { t } = useLanguage();
  return (
    <section className="relative w-full py-24 px-6 lg:px-20 z-20">
      {/* Small planet image acting as a background element */}
      <div 
        className="absolute inset-0 -z-10 pointer-events-none mix-blend-screen w-full h-full"
        style={{
          maskImage: "linear-gradient(to bottom, transparent 5%,  black 90%, transparent)",
          WebkitMaskImage: "linear-gradient(to bottom, transparent 5%, black 90%, transparent)"
        }}
      >
        <img
          src="/services-web-design/b966a362-46ae-4124-9f5d-60ce2a10e6d3.jfif"
          alt="Blue Planet Background"
          width={1920}
          height={1080}
          loading="lazy"
          className="w-full h-full object-cover"
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
