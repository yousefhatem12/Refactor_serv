"use client";
import React, { useRef, useState, useEffect } from "react";
import {
  motion,
  useScroll,
  useTransform,
  AnimatePresence,
} from "framer-motion";
import Link from "next/link";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../../context/LanguageContext";

const Services = () => {
  const { t, language } = useLanguage();
  const sectionRef = useRef(null);
  const [services, setServices] = useState([]);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isLoading, setIsLoading] = useState(false);

  const getServiceHref = (service) => {
    if (!service) return "#";
    if (service.id === "hosting") return "/services/services-cloud-hosting";
    if (service.id === "marketing") return "/services/services-social-media";
    if (service.id === "design-1") return "/services/services-ui-ux-design";
    if (service.id === "design-2" || service.id === "design-3")
      return "/services/services-web-development";
    return `/services/services-${service.id}`;
  };

  // Backend Integration
  useEffect(() => {
    const fetchServicesData = async () => {
      try {
        setIsLoading(true);
        const data = await fetchAPI(`/services?lang=${language}`);
        if (data && data.length > 0) {
          setServices(data);
        }
      } catch (error) {
        console.error("Failed to fetch services:", error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchServicesData();
  }, [language]);

  const { scrollYProgress } = useScroll({
    target: sectionRef,
    offset: ["start end", "end start"],
  });

  // Circular logic for indices
  const getIndex = (offset) => {
    if (services.length === 0) return null;
    return (currentIndex + offset + services.length) % services.length;
  };

  const handleNext = () => {
    setCurrentIndex((prev) => (prev + 1) % services.length);
  };

  const handlePrev = () => {
    setCurrentIndex((prev) => (prev - 1 + services.length) % services.length);
  };

  // --- Transforms ---

  // Text Content Transforms
  const contentY = useTransform(
    scrollYProgress,
    [0, 0.3, 0.7, 1],
    [100, 0, 0, -100],
  );
  const contentOpacity = useTransform(
    scrollYProgress,
    [0, 0.2, 0.8, 1],
    [0, 1, 1, 0],
  );

  // Main Planet Transforms (Slot 0)
  const mainY = useTransform(
    scrollYProgress,
    [0, 0.4, 0.7, 1],
    ["30vh", "0vh", "0vh", "-15vh"],
  );
  const mainScale = useTransform(
    scrollYProgress,
    [0, 0.4, 0.7, 1],
    [0.8, 1, 1, 1.1],
  );
  const mainOpacity = useTransform(
    scrollYProgress,
    [0, 0.2, 0.8, 1],
    [0, 1, 1, 0],
  );

  // Side Planet Transforms (Slots -1, 1)
  const sideX = ["-30vw", "0vw", "0vw", "-20vw"];
  const sideY = ["30vh", "0vh", "0vh", "-10vh"];

  const leftX = useTransform(scrollYProgress, [0, 0.4, 0.7, 1], sideX);
  const leftY = useTransform(scrollYProgress, [0, 0.4, 0.7, 1], sideY);
  const leftRotate = useTransform(scrollYProgress, [0, 0.4], [-60, 0]);
  const leftOpacity = useTransform(
    scrollYProgress,
    [0, 0.25, 0.75, 1],
    [0, 0.9, 0.9, 0],
  );

  const rightX = useTransform(
    scrollYProgress,
    [0, 0.4, 0.7, 1],
    ["30vw", "0vw", "0vw", "20vw"],
  );
  const rightY = useTransform(scrollYProgress, [0, 0.4, 0.7, 1], sideY);
  const rightRotate = useTransform(scrollYProgress, [0, 0.4], [60, 0]);
  const rightOpacity = useTransform(
    scrollYProgress,
    [0, 0.25, 0.75, 1],
    [0, 0.9, 0.9, 0],
  );

  // Far Side Planet Transforms (Slots -2, 2)
  const farLeftX = useTransform(
    scrollYProgress,
    [0, 0.4, 0.7, 1],
    ["-50vw", "0vw", "0vw", "-35vw"],
  );
  const farRightX = useTransform(
    scrollYProgress,
    [0, 0.4, 0.7, 1],
    ["50vw", "0vw", "0vw", "35vw"],
  );
  const farSideY = useTransform(
    scrollYProgress,
    [0, 0.4, 0.7, 1],
    ["20vh", "0vh", "0vh", "0vh"],
  );
  const farSideOpacity = useTransform(
    scrollYProgress,
    [0, 0.3, 0.7, 1],
    [0, 0.5, 0.5, 0],
  );

  // Orbit Decorator Transforms
  const orbitOpacity = useTransform(
    scrollYProgress,
    [0.2, 0.4, 0.7, 0.9],
    [0, 1, 1, 0],
  );
  const orbitScale = useTransform(scrollYProgress, [0.2, 0.4], [0.5, 1]);

  const springTransition = {
    type: "spring",
    stiffness: 100,
    damping: 18,
    mass: 1,
  };

  const hasServices = services.length > 0;
  const activeService = hasServices ? services[currentIndex] : null;
  const leftService =
    hasServices && services.length > 1 ? services[getIndex(-1)] : null;
  const rightService =
    hasServices && services.length > 2 ? services[getIndex(1)] : null;
  const farLeftService =
    hasServices && services.length > 4 ? services[getIndex(-2)] : null;
  const farRightService =
    hasServices && services.length > 3 ? services[getIndex(2)] : null;

  return (
    <section
      id="services"
      ref={sectionRef}
      className={`relative w-full min-h-screen flex flex-col items-center justify-center md:py-20 px-6 z-10 ${!hasServices ? "hidden" : ""}`}
    >
      {hasServices && (
        <>
          {/* Background Layers - Verse Effect */}
          <div className="absolute inset-0 pointer-events-none z-0 overflow-hidden">
            {[
              "w-[350%] sm:w-[250%] md:w-[200%] lg:w-[160%]",
              "w-[300%] sm:w-[210%] md:w-[170%] lg:w-[140%]",
              "w-[250%] sm:w-[170%] md:w-[140%] lg:w-[120%]",
              "w-[200%] sm:w-[130%] md:w-[110%] lg:w-[100%]",
            ].map((widthClass, index) => (
              <motion.img
                key={index}
                src="/Services/Outline 6.png"
                alt=""
                className={`absolute top-[100%] left-1/2 -translate-x-1/2 -translate-y-1/2 h-auto max-w-none ${widthClass}`}
                style={{
                  opacity: orbitOpacity,
                  scale: orbitScale,
                }}
              />
            ))}
          </div>

          {/* Content */}
          <motion.div
            style={{ y: contentY, opacity: contentOpacity }}
            className="relative z-[60] text-center max-w-4xl px-4 sm:px-0 pointer-events-none"
          >
            <div className="mb-8 md:mb-12">
              <h4 className="text-[#D98B50] font-bold tracking-[0.3em] uppercase text-xs md:text-sm mb-2 md:mb-4">
                {t("whatWeDo")}
              </h4>
              <h2 className="text-white text-3xl md:text-6xl font-bold">
                {t("ourServices")}
              </h2>
            </div>

            <AnimatePresence mode="wait">
              <motion.div
                key={activeService.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -20 }}
                transition={{ duration: 0.5 }}
                className="space-y-6"
              >
                <h3
                  className={`text-3xl sm:text-5xl md:text-7xl font-bold bg-gradient-to-b ${activeService.gradient} bg-clip-text text-transparent`}
                >
                  {activeService.title}
                </h3>
                <p className="text-white text-sm sm:text-base md:text-xl max-w-2xl mx-auto leading-relaxed bg-gray-900/50 inline p-1 md:leading-[2]">
                  {activeService.description}
                </p>
                <Link
                  href={getServiceHref(activeService)}
                  className="text-white border-b border-white/30 pb-1 block mx-auto mt-4 hover:border-white transition-colors uppercase tracking-widest text-sm font-medium pointer-events-auto w-max"
                >
                  {t("knowMore")}
                </Link>
              </motion.div>
            </AnimatePresence>
          </motion.div>

          {/* Planets Container */}
          <div className="absolute inset-0 z-30 pointer-events-none">
            {/* Far Left Planet Slot */}
            {farLeftService && (
              <motion.div
                layout
                onClick={() => setCurrentIndex(getIndex(-2))}
                transition={springTransition}
                style={{ x: farLeftX, y: farSideY, opacity: farSideOpacity }}
                className="absolute bottom-[40%] left-[2%] md:bottom-[35%] md:left-[5%] cursor-pointer pointer-events-auto group z-40"
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.9 }}
              >
                <motion.div
                  animate={{ y: [0, -10, 0] }}
                  transition={{
                    duration: 7,
                    repeat: Infinity,
                    ease: "easeInOut",
                  }}
                >
                  <motion.img
                    layoutId={farLeftService.id}
                    src={farLeftService.img}
                    className="w-[65px] sm:w-[85px] md:w-[100px] lg:w-[120px] aspect-square object-contain opacity-60 grayscale-[0.5] group-hover:grayscale-0 transition-all"
                    style={{ filter: "brightness(0.7)" }}
                    animate={{ rotate: -90 }}
                    transition={springTransition}
                  />
                </motion.div>
              </motion.div>
            )}

            {/* Left Planet Slot */}
            {leftService && (
              <motion.div
                layout
                onClick={handlePrev}
                transition={springTransition}
                style={{ x: leftX, y: leftY, opacity: leftOpacity }}
                className="absolute bottom-[15%] left-[6%] sm:left-[10%] md:bottom-[0%] md:left-[5%] flex items-center gap-4 md:gap-6 cursor-pointer pointer-events-auto group z-50"
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
              >
                <motion.div style={{ rotate: leftRotate }}>
                  <motion.div
                    animate={{ y: [0, -15, 0] }}
                    transition={{
                      duration: 5,
                      repeat: Infinity,
                      ease: "easeInOut",
                    }}
                  >
                    <motion.img
                      layoutId={leftService.id}
                      src={leftService.img}
                      className="w-[95px] sm:w-[120px] md:w-[140px] lg:w-[200px] aspect-square object-contain transition-transform duration-500 drop-shadow-[0_0_30px_rgba(255,255,255,0.05)]"
                      style={{ scale: 0.8 }}
                      animate={{ rotate: -45 }}
                      transition={springTransition}
                    />
                  </motion.div>
                </motion.div>
                <motion.div
                  initial={{ opacity: 0, x: -20 }}
                  animate={{ opacity: 1, x: -50 }}
                  className="hidden md:flex items-center gap-2"
                >
                  <span className="text-white/80 font-semibold text-lg lg:text-xl whitespace-nowrap">
                    {leftService.label}
                  </span>
                  <div
                    className={`w-2 h-2 rounded-full ${leftService.dotColor} ${leftService.shadow}`}
                  />
                </motion.div>
              </motion.div>
            )}

            {/* Main Planet Slot - Center Bottom */}
            <motion.div
              layout
              transition={springTransition}
              className="absolute bottom-[-10%] sm:bottom-[-20%] md:bottom-[-40%] lg:bottom-[-40%] left-1/2 -translate-x-1/2 w-[260px] sm:w-[340px] md:w-[450px] lg:w-[600px] aspect-square origin-bottom pointer-events-none"
              style={{
                y: mainY,
                scale: mainScale,
                opacity: mainOpacity,
                zIndex: 50,
              }}
            >
              <motion.div
                animate={{ y: [0, -20, 0] }}
                transition={{
                  duration: 6,
                  repeat: Infinity,
                  ease: "easeInOut",
                }}
                className="w-full h-full"
              >
                <motion.img
                  layoutId={activeService.id}
                  src={activeService.img}
                  alt={activeService.label}
                  className="w-full h-full object-contain opacity-95 drop-shadow-[0_0_50px_rgba(255,255,255,0.15)]"
                  style={{ scale: 1 }}
                  animate={{ rotate: 0 }}
                  transition={springTransition}
                />
              </motion.div>
            </motion.div>

            {/* Right Planet Slot */}
            {rightService && (
              <motion.div
                layout
                onClick={handleNext}
                transition={springTransition}
                style={{ x: rightX, y: rightY, opacity: rightOpacity }}
                className="absolute bottom-[15%] right-[6%] sm:right-[10%] md:bottom-[-2%] md:right-[5%] flex flex-row-reverse items-center gap-4 md:gap-6 cursor-pointer pointer-events-auto group z-50"
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
              >
                <motion.div style={{ rotate: rightRotate }}>
                  <motion.div
                    animate={{ y: [0, -15, 0] }}
                    transition={{
                      duration: 5.5,
                      repeat: Infinity,
                      ease: "easeInOut",
                    }}
                  >
                    <motion.img
                      layoutId={rightService.id}
                      src={rightService.img}
                      className="w-[95px] sm:w-[120px] md:w-[140px] lg:w-[200px] aspect-square object-contain transition-transform duration-500 drop-shadow-[0_0_30px_rgba(255,255,255,0.05)]"
                      style={{ scale: 0.8 }}
                      animate={{ rotate: 45 }}
                      transition={springTransition}
                    />
                  </motion.div>
                </motion.div>
                <motion.div
                  initial={{ opacity: 0, x: 20 }}
                  animate={{ opacity: 1, x: 40 }}
                  className="hidden md:flex flex-row-reverse items-center gap-2"
                >
                  <span className="text-white/80 font-semibold text-lg lg:text-xl whitespace-nowrap">
                    {rightService.label}
                  </span>
                  <div
                    className={`w-2 h-2 rounded-full ${rightService.dotColor} ${rightService.shadow}`}
                  />
                </motion.div>
              </motion.div>
            )}

            {/* Far Right Planet Slot */}
            {farRightService && (
              <motion.div
                layout
                onClick={() => setCurrentIndex(getIndex(2))}
                transition={springTransition}
                style={{ x: farRightX, y: farSideY, opacity: farSideOpacity }}
                className="absolute bottom-[40%] right-[2%] md:bottom-[30%] md:right-[10%] cursor-pointer pointer-events-auto group z-40"
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.9 }}
              >
                <motion.div
                  animate={{ y: [0, -10, 0] }}
                  transition={{
                    duration: 7.5,
                    repeat: Infinity,
                    ease: "easeInOut",
                  }}
                >
                  <motion.img
                    layoutId={farRightService.id}
                    src={farRightService.img}
                    className="w-[65px] sm:w-[85px] md:w-[100px] lg:w-[120px] aspect-square object-contain opacity-60 grayscale-[0.5] group-hover:grayscale-0 transition-all"
                    style={{ filter: "brightness(0.7)" }}
                    animate={{ rotate: 90 }}
                    transition={springTransition}
                  />
                </motion.div>
              </motion.div>
            )}

            {/* Orbit Spheres - Decorative */}
            <motion.div
              style={{ opacity: orbitOpacity, scale: orbitScale }}
              className="absolute inset-0 z-0 pointer-events-none"
            >
              <div className="absolute top-[45%] left-[28%] w-8 h-8 rounded-full bg-[#EAB308] shadow-[0_0_20px_#EAB308]" />
              <div className="absolute top-[52%] right-[32%] w-6 h-6 rounded-full bg-[#A855F7] shadow-[0_0_15px_#A855F7]" />
            </motion.div>
          </div>
        </>
      )}
    </section>
  );
};

export default Services;
