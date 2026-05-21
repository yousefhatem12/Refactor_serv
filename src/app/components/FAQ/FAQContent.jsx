"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI } from "../../../lib/api";

const FAQContent = ({ data }) => {
  const [openId, setOpenId] = useState(null);
  const [faqs, setFaqs] = useState([]);
  const [loading, setLoading] = useState(true);
  const { t, language } = useLanguage();

  const title = data?.title || (t("faqTitle1") + " " + t("faqTitle2") + " " + t("faqTitle3"));
  const description = data?.content || "";
  const heroImage = data?.hero_image || "/Hero/earth.png";

  useEffect(() => {
    const loadFaqs = async () => {
      try {
        setLoading(true);
        const data = await fetchAPI(`/faqs?lang=${language}`);
        const formattedData = Array.isArray(data) ? data.map((item, index) => ({
          ...item,
          displayId: (index + 1).toString().padStart(2, '0')
        })) : [];
        setFaqs(formattedData);
        if (formattedData.length > 0) {
          setOpenId(formattedData[0].id);
        }
      } catch (error) {
        console.error("Failed to fetch FAQs:", error);
      } finally {
        setLoading(false);
      }
    };

    loadFaqs();
  }, [language]);

  const toggleAccordion = (id) => {
    setOpenId(openId === id ? null : id);
  };

  return (
    <div className="max-w-[1400px] mx-auto px-6 md:px-10 relative mt-10 mb-32 min-h-screen">
      <div className="flex flex-col lg:flex-row relative">
        {/* Left Side: Text and Accordions */}
        <div className="w-full lg:w-[55%] z-20">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="mb-10"
          >
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold tracking-widest leading-[1.2] text-white mb-4">
              {title}
            </h1>
            {description && (
              <p className="text-white/80 text-lg md:text-xl leading-relaxed max-w-2xl">
                {description}
              </p>
            )}
          </motion.div>

          <div className="flex flex-col gap-4">
            {loading ? (
              <div className="text-white text-center py-10">Loading FAQs...</div>
            ) : faqs.length === 0 ? (
              <div className="text-white text-center py-10">No FAQs available.</div>
            ) : (
              faqs.map((item) => {
              const isOpen = openId === item.id;
              return (
                <motion.div
                  key={item.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  className={`border ${isOpen ? "border-[#304880] bg-[#1a2650]/40" : "border-white/10 bg-[#05050f]/60"} rounded-lg overflow-hidden backdrop-blur-md transition-colors duration-300`}
                >
                  <button
                    onClick={() => toggleAccordion(item.id)}
                    className="w-full px-6 py-5 flex items-center justify-between text-left focus:outline-none"
                  >
                    <div className="flex items-center gap-6">
                      <span className="text-white font-bold text-lg">
                        {item.displayId}
                      </span>
                      <span className="text-white font-bold text-base md:text-lg">
                        {item.question}
                      </span>
                    </div>
                    <div
                      className={`w-10 h-10 flex items-center justify-center rounded-md shrink-0 transition-colors ${isOpen ? "bg-[#4B79A1]" : "bg-[#294a73] hover:bg-[#345c8c]"} `}
                    >
                      {isOpen ? (
                        <svg
                          width="14"
                          height="2"
                          viewBox="0 0 14 2"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path d="M0 1H14" stroke="white" strokeWidth="2" />
                        </svg>
                      ) : (
                        <svg
                          width="14"
                          height="14"
                          viewBox="0 0 14 14"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M7 0V14M0 7H14"
                            stroke="white"
                            strokeWidth="2"
                          />
                        </svg>
                      )}
                    </div>
                  </button>

                  <AnimatePresence>
                    {isOpen && (
                      <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: "auto", opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.3 }}
                      >
                        <div className="px-6 pb-6 pl-[4.5rem] text-white/70 text-sm leading-relaxed space-y-4">
                          {(item.answer || "").split("\n").map((paragraph, idx) => (
                            <p key={idx}>{paragraph}</p>
                          ))}
                        </div>
                      </motion.div>
                    )}
                  </AnimatePresence>
                </motion.div>
              );
            }))}
          </div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3 }}
            className="mt-16 flex flex-col sm:flex-row items-center gap-6"
          >
            <h3 className="text-3xl font-bold text-white">
              {t("faqStillQuestions")}
            </h3>
            <button className="px-8 py-3 bg-[#D98B50] hover:bg-[#c47c45] text-white font-semibold rounded-md transition-colors shadow-[0_0_15px_rgba(217,139,80,0.4)]">
              {t("getInTouch2")}
            </button>
          </motion.div>
        </div>
      </div>

      {/* Right Side: Earth Image */}
      <div className="absolute md:right-[-25%] lg:right-[-25%] top-[5%] md:w-[500px] lg:w-[800px] h-auto z-10 pointer-events-none hidden md:block">
        <motion.img
          initial={{ opacity: 0, rotate: -10 }}
          animate={{ opacity: 1, rotate: 0 }}
          transition={{ duration: 1.5, ease: "easeOut" }}
          src={heroImage}
          alt="FAQ Illustration"
          className="w-full h-full object-contain drop-shadow-[0_0_40px_rgba(59,130,246,0.3)]"
        />
      </div>
    </div>
  );
};

export default FAQContent;
