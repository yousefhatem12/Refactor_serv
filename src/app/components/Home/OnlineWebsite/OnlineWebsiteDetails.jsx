"use client";
import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import OurPortfolio from "../Portfolio/OurPortfolio";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../../context/LanguageContext";

const OnlineWebsiteDetails = () => {
  const { language } = useLanguage();
  const [activeCategoryId, setActiveCategoryId] = useState("all");
  const [categories, setCategories] = useState([]);

  useEffect(() => {
    fetchAPI(`/portfolio-categories?lang=${language}`)
      .then((data) => {
        if (data) {
          setCategories([
            { name: language === "ar" ? "الكل" : "All", id: "all" },
            ...data.map((cat) => ({ name: cat.label, id: cat.id }))
          ]);
        }
      })
      .catch(console.error);
  }, [language]);

  const getRotation = (index) => {
    const total = categories.length || 1;
    const startAngle = -42; // Spread out to match image
    const endAngle = 42;
    if (total === 1) return 0;
    return startAngle + (index * (endAngle - startAngle)) / (total - 1);
  };

  return (
    <section className="relative w-full min-h-screen overflow-hidden flex items-center text-white bg-transparent">
      {/* Background/Atmosphere - Removed solid gradients to prevent sharp lines */}

      <div className="w-full h-full relative flex flex-col lg:flex-row p-0  lg:pt-0">
        {/* Mobile Categories Menu */}
        <div
          className="w-full flex flex-wrap justify-center lg:hidden gap-3 px-4 py-4 z-20"
        >
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setActiveCategoryId(cat.id)}
              className={`whitespace-nowrap px-4 py-2 rounded-full border text-sm font-medium transition-all ${
                activeCategoryId === cat.id
                  ? "bg-[#ffb800] text-black border-[#ffb800]"
                  : "text-white/70 border-white/20 hover:text-white"
              }`}
            >
              {cat.name}
            </button>
          ))}
        </div>

        {/* Left Planet Section */}
        <div className="relative hidden lg:flex w-[45%] h-screen items-center z-10">
          <motion.div
            initial={{ x: -200, opacity: 0, rotate: -10 }}
            whileInView={{ x: 0, opacity: 1, rotate: 0 }}
            transition={{ duration: 1.5, ease: "easeOut" }}
            className="absolute w-[100vh] left-[-45.5vh] top-1/2 -translate-y-1/2 z-10 flex items-center justify-center"
          >
            <img
              src="/aside/MArs 2.png"
              alt="Planet Mars"
              className="w-full h-full object-contain drop-shadow-[0_0_80px_rgba(255,138,0,0.3)]"
            />
            {/* The white arc */}
            <img
              src="/aside/circle.png"
              alt="Arc"
              className="absolute top-[5%] left-[90px] w-[90%] h-[100%] scale-y-[90%]  object-contain opacity-40 -z-10 pointer-events-none"
            />
          </motion.div>

          {/* Navigation Menu along the Arc */}
          <div className="absolute left-0 top-0 w-full h-full z-20 pointer-events-none">
            {categories.map((cat, index) => {
              const rotation = getRotation(index);
              const isActive = activeCategoryId === cat.id;

              return (
                <motion.div
                  key={cat.id}
                  initial={{ opacity: 0, rotate: rotation }}
                  whileInView={{ opacity: 1, rotate: rotation }}
                  viewport={{ once: true }}
                  transition={{ delay: 0.2 + index * 0.1, duration: 0.8 }}
                  className="absolute left-0 top-1/2 [transform-origin:0%_50%] pointer-events-none "
                  style={{ y: "-30%" }}
                >
                  <motion.div
                    initial={{ x: 0 }}
                    whileInView={{ x: "32vh" }}
                    viewport={{ once: true }}
                    transition={{
                      delay: 0.2 + index * 0.1,
                      duration: 1,
                      ease: "easeOut",
                    }}
                    className="cursor-pointer pointer-events-auto "
                    onClick={() => setActiveCategoryId(cat.id)}
                  >
                    <span
                      className={`text-[18px]   font-medium transition-all  duration-300 break-words inline-block relative drop-shadow-[0_0_10px_rgba(0,0,0,0.5)] ${
                        isActive
                          ? "text-[#ffb800] font-bold scale-110 drop-shadow-[0_0_20px_rgba(255,184,0,0.5)]"
                          : "text-white/70 hover:text-[#ffb800]"
                      }`}
                      style={{
                        transform: `rotate(-${rotation}deg)`,
                      }}
                    >
                      <div className="">{cat.name}</div>
                      {isActive && (
                        <motion.div
                          layoutId="activeUnderline"
                          className="absolute -bottom-1.5 left-0 w-full h-[2px] bg-[#ffb800] shadow-[0_0_10px_#ffb800] rounded-full"
                        />
                      )}
                    </span>
                  </motion.div>
                </motion.div>
              );
            })}
          </div>
        </div>

        {/* Right Content Area */}
        <div className="w-full lg:w-[55%] relative z-20">
          <OurPortfolio activeCategoryId={activeCategoryId} />
        </div>
      </div>

      {/* Decorative smaller planets */}
      <div className="absolute hidden lg:block w-[60px] h-[60px] top-[15%] left-[18%] rounded-full bg-[radial-gradient(circle_at_30%_30%,#8b4513,#2f1b0c)] shadow-[inset_-10px_-10px_20px_rgba(0,0,0,0.7),0_0_30px_rgba(255,138,0,0.2)] z-[5]"></div>
      <div className="absolute hidden lg:block w-[75px] h-[75px] bottom-[12%] left-[20%] rounded-full bg-[radial-gradient(circle_at_30%_30%,#5d4037,#1b1b1b)] shadow-[inset_-10px_-10px_20px_rgba(0,0,0,0.7),0_0_30px_rgba(255,138,0,0.2)] z-[5]"></div>
    </section>
  );
};

export default OnlineWebsiteDetails;
