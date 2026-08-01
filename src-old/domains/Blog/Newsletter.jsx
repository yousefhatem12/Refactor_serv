"use client";
import React from "react";
import Image from "next/image";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

const Newsletter = () => {
  const { t } = useLanguage();
  return (
    <section className="w-full py-24 text-white relative z-10 flex flex-col items-center justify-center text-center overflow-hidden">
      <div className="absolute inset-0 w-full h-full -z-10 pointer-events-none mix-blend-screen opacity-80">
        <Image
          src="/our-blog/get-our-blog.webp"
          alt="Newsletter Background"
          fill
          className="object-cover object-center mix-blend-screen opacity-80 [mask-image:radial-gradient(ellipse,black_20%,transparent_70%)] [-webkit-mask-image:radial-gradient(ellipse,black_20%,transparent_70%)] mix-blend-screen brightness-[130%] contrast-[120%] saturate-[130%]"
          priority
        />
      </div>

      <div className="relative z-10 flex flex-col items-center justify-center text-center w-full">
        <motion.h2
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-2xl md:text-3xl lg:text-4xl font-bold mb-10 max-w-2xl leading-snug"
        >
          {t("newsletterTitle")}
        </motion.h2>

        <motion.form
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="flex flex-col sm:flex-row gap-4 w-full max-w-lg px-4 justify-center items-center"
          onSubmit={(e) => e.preventDefault()}
        >
          <input
            type="email"
            placeholder={t("newsletterPlaceholder") || "Your Email"}
            className="w-full sm:flex-1 px-6 py-4 rounded-full bg-white text-black focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-lg text-sm sm:text-base"
            required
          />
          <button
            type="submit"
            className="w-full sm:w-auto px-8 py-4 rounded-full border border-blue-500 bg-blue-600 text-white hover:bg-blue-700 transition-colors font-medium shadow-lg whitespace-nowrap text-sm sm:text-base"
          >
            {t("newsletterSubscribe")}
          </button>
        </motion.form>
      </div>
    </section>
  );
};

export default Newsletter;
