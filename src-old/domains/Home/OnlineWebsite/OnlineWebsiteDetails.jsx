"use client";
import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import OurPortfolio from "../Portfolio/OurPortfolio";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";

const OnlineWebsiteDetails = () => {
  const { language } = useLanguage();
  const [activeCategoryId, setActiveCategoryId] = useState("all");
  const [categories, setCategories] = useState([]);

  const isRtl = language === "ar";

  useEffect(() => {
    fetchAPI(`/portfolio/categories?lang=${language}`)
      .then((data) => {
        if (data) {
          setCategories([
            { name: isRtl ? "الكل" : "All", id: "all" },
            ...data.map((cat) => ({ name: cat.label, id: cat.id })),
          ]);
        }
      })
      .catch(console.error);
  }, [language, isRtl]);

  const getRotation = (index) => {
    const total = categories.length || 1;
    const maxSpread = 135; // Maximum total angle spread to keep within the semi-circle
    const idealStep = 30; // Ideal angular step between tabs
    const spread = Math.min(maxSpread, (total - 1) * idealStep);

    if (total === 1) return 0;
    const startAngle = -spread / 2;
    return startAngle + (index * spread) / (total - 1);
  };

  return (
    <section className="relative w-full min-h-screen overflow-hidden flex items-center text-white bg-transparent">
      {/* Background/Atmosphere - Removed solid gradients to prevent sharp lines */}

      <div className="w-full h-full relative flex flex-col lg:flex-row p-0  lg:pt-0">
        {/* Mobile Categories Menu */}
        <div className="w-full flex flex-wrap justify-center lg:hidden gap-3 px-4 py-4 z-20">
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
        </div>{" "}
        {/* Left/Right Planet Section */}
        <div className="relative hidden lg:flex w-[45%] h-screen items-center z-10">
          <motion.div
            initial={{
              x: isRtl ? 40 : -40,
              opacity: 0,
            }}
            whileInView={{ x: 0, opacity: 1 }}
            viewport={{ once: true, amount: 0.05 }}
            transition={{ duration: 0.25, ease: "easeOut" }}
            className={`absolute w-[100vh] h-[100vh] ${isRtl ? "right-[-45.5vh] left-auto" : "left-[-45.5vh] right-auto"} top-1/2 -translate-y-1/2 z-10 flex items-center justify-center`}
          >
            <img
              src="/aside/MArs 2.webp"
              alt="Planet Mars"
              className={`w-full h-full object-contain drop-shadow-[0_0_80px_rgba(255,138,0,0.3)] ${isRtl ? "scale-x-[-1]" : ""}`}
            />
            {/* The white arc */}
            <img
              src="/aside/circle.webp"
              alt="Arc"
              className={`absolute top-[5%] ${isRtl ? "right-[8.3vh] left-auto" : "left-[8.3vh] right-auto"} w-[95%] h-[100%] object-contain opacity-40 -z-10 pointer-events-none`}
              style={{
                transform: `scaleY(0.9) ${isRtl ? "scaleX(-1)" : "scaleX(1)"}`,
              }}
            />

            {/* Navigation Menu along the Arc */}
            <div
              className={`absolute  inset-0 z-20  pointer-events-none ${isRtl ? "right-15" : " left-15"}`}
              style={{
                transform: `scaleY(0.9) ${isRtl ? "scaleX(-1) " : " scaleX(1)"}`,
              }}
            >
              {categories.map((cat, index) => {
                // To keep spacing perfectly even, we calculate Y coordinates linearly
                // along the ellipse instead of using standard sinusoidal angles.
                const total = categories.length || 1;
                const spread = Math.min(135, (total - 1) * 30);
                const maxAngle = spread / 2;
                const maxRad = (maxAngle * Math.PI) / 180;
                const sinMax = Math.sin(maxRad);

                const t = total === 1 ? 0 : -1 + (2 * index) / (total - 1);

                // Radius in vh units placing the tabs exactly along the edge of Mars 2.webp
                const radius = 36;
                const targetY = radius * sinMax * t;
                const targetX = radius * Math.sqrt(1 - Math.pow(sinMax * t, 2));

                const isActive = activeCategoryId === cat.id;

                return (
                  <motion.div
                    key={cat.id}
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ duration: 0.1 }}
                    className="absolute pointer-events-auto cursor-pointer"
                    style={{
                      left: `calc(50% + ${targetX}vh)`,
                      top: `calc(50% + ${targetY}vh)`,
                      x: "-50%",
                      y: "-50%",
                    }}
                    onClick={() => setActiveCategoryId(cat.id)}
                  >
                    <span
                      className={`text-[18px] font-medium transition-all duration-300 whitespace-nowrap inline-block relative drop-shadow-[0_0_10px_rgba(0,0,0,0.5)] ${
                        isActive
                          ? "text-[#ffb800] font-bold scale-110 drop-shadow-[0_0_20px_rgba(255,184,0,0.5)]"
                          : "text-white/70 hover:text-[#ffb800]"
                      }`}
                      style={{
                        // Cancel scaleY(0.9) and scaleX(-1 for RTL) to keep text clean and readable without mirroring
                        transform: `scaleY(${1 / 0.9}) ${isRtl ? "scaleX(-1)" : "scaleX(1)"}`,
                      }}
                    >
                      <div>{cat.name}</div>
                      {isActive && (
                        <motion.div
                          layoutId="activeUnderline"
                          className="absolute -bottom-1.5 left-0 w-full h-[2px] bg-[#ffb800] shadow-[0_0_10px_#ffb800] rounded-full"
                        />
                      )}
                    </span>
                  </motion.div>
                );
              })}
            </div>
          </motion.div>
        </div>
        {/* Right Content Area */}
        <div className="w-full lg:w-[55%] relative z-20">
          <OurPortfolio activeCategoryId={activeCategoryId} />
        </div>
      </div>

      {/* Decorative smaller planets */}
      <div
        className={`absolute hidden lg:block w-[60px] h-[60px] top-[15%] ${isRtl ? "right-[18%] left-auto" : "left-[18%] right-auto"} rounded-full bg-[radial-gradient(circle_at_30%_30%,#8b4513,#2f1b0c)] shadow-[inset_-10px_-10px_20px_rgba(0,0,0,0.7),0_0_30px_rgba(255,138,0,0.2)] z-[5]`}
      ></div>
      <div
        className={`absolute hidden lg:block w-[75px] h-[75px] bottom-[12%] ${isRtl ? "right-[20%] left-auto" : "left-[20%] right-auto"} rounded-full bg-[radial-gradient(circle_at_30%_30%,#5d4037,#1b1b1b)] shadow-[inset_-10px_-10px_20px_rgba(0,0,0,0.7),0_0_30px_rgba(255,138,0,0.2)] z-[5]`}
      ></div>
    </section>
  );
};

export default OnlineWebsiteDetails;
