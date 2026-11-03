"use client";
import React, { useState } from "react";
import Image from "next/image";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { getCaptchaRequiredMessage } from "@/lib/captcha";
import TurnstileWidget from "@/app/components/Shared/TurnstileWidget";

const Newsletter = () => {
  const { t, language } = useLanguage();
  const [email, setEmail] = useState("");
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [captchaToken, setCaptchaToken] = useState("");
  const [captchaError, setCaptchaError] = useState("");
  const [captchaVersion, setCaptchaVersion] = useState(0);
  const isRtl = language === "ar";

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!email || isSubmitting) return;
    if (!captchaToken) {
      setCaptchaError(getCaptchaRequiredMessage(language));
      return;
    }

    setIsSubmitting(true);
    setCaptchaError("");

    // Simulate a short delay for UX feel
    await new Promise((resolve) => setTimeout(resolve, 800));

    setIsSubmitted(true);
    setIsSubmitting(false);
    setEmail("");
    setCaptchaToken("");
    setCaptchaVersion((version) => version + 1);

    // Reset success message after 5 seconds
    setTimeout(() => setIsSubmitted(false), 5000);
  };

  return (
    <section className="w-full py-24 text-white relative z-10 flex flex-col items-center justify-center text-center overflow-hidden">
      <div className="absolute inset-0 w-full h-full -z-10 pointer-events-none mix-blend-screen opacity-80">
        <Image
          src="/our-blog/get-our-blog.webp"
          alt="Newsletter Background"
          fill
          sizes="100vw"
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
          onSubmit={handleSubmit}
        >
          <input
            type="email"
            aria-label={t("newsletterPlaceholder") || "Your Email"}
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder={t("newsletterPlaceholder") || "Your Email"}
            className="w-full sm:flex-1 px-6 py-4 rounded-full bg-white text-black focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-lg text-sm sm:text-base"
            required
            disabled={isSubmitting}
          />
          <TurnstileWidget
            key={captchaVersion}
            action="newsletter"
            language={language}
            onTokenChange={(token) => {
              setCaptchaToken(token);
              if (token) setCaptchaError("");
            }}
            className="sm:col-span-2"
          />
          {captchaError && (
            <p className="sm:col-span-2 text-xs text-red-400" role="alert">
              {captchaError}
            </p>
          )}
          <motion.button
            type="submit"
            disabled={isSubmitting}
            whileTap={{ scale: 0.93 }}
            className="w-full sm:w-auto px-8 py-4 rounded-full border border-blue-500 bg-blue-600 text-white hover:bg-blue-700 transition-colors font-medium shadow-lg whitespace-nowrap text-sm sm:text-base cursor-pointer disabled:opacity-70 disabled:cursor-wait"
          >
            {isSubmitting
              ? (isRtl ? "جاري الإرسال..." : "Sending...")
              : t("newsletterSubscribe")}
          </motion.button>
        </motion.form>

        {/* Success Message */}
        <AnimatePresence>
          {isSubmitted && (
            <motion.div
              initial={{ opacity: 0, y: 20, scale: 0.9 }}
              animate={{ opacity: 1, y: 0, scale: 1 }}
              exit={{ opacity: 0, y: -10, scale: 0.9 }}
              transition={{ type: "spring", stiffness: 300, damping: 25 }}
              className="mt-6 flex items-center gap-3 px-6 py-3 rounded-full bg-green-500/20 border border-green-500/30 backdrop-blur-md shadow-[0_0_20px_rgba(34,197,94,0.2)]"
            >
              <svg className="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
              </svg>
              <span className="text-green-300 font-medium text-sm">
                {isRtl
                  ? "تم التسجيل بنجاح! ستصلك مقالاتنا قريباً"
                  : "Successfully subscribed! You'll receive our articles soon"}
              </span>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </section>
  );
};

export default Newsletter;

