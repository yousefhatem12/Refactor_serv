"use client";
import React, { useState, useEffect, useRef } from "react";
import { motion, useScroll, useTransform, useSpring } from "framer-motion";
import Hero from "../components/Home/Hero/Hero";
import Features from "../components/Home/Features/Features";
import Services from "../components/Home/Services/Services";
import OnlineWebsite from "../components/Home/OnlineWebsite/OnlineWebsite";
import OnlineWebsiteDetails from "../components/Home/OnlineWebsite/OnlineWebsiteDetails";
import Earth from "../components/Home/Hero/Earth";
import OurBlog from "../components/Home/Blog/OurBlog";
import Testimonial from "../components/Home/Testimonial/Testimonial";
import ContactUs from "../components/Home/ContactUs/ContactUs";
import CTA from "../components/Home/CTA/CTA";
import Footer from "../components/Footer/Footer";
import PlanetAstronaut from "../components/Home/Shared/PlanetAstronaut";
import PageSections from "../components/Shared/PageSections";
import Loader from "../components/Loader/Loader";
import { useLanguage } from "../context/LanguageContext";
import { fetchAPI } from "@/lib/api";

export default function HomePage() {
  const [isLoading, setIsLoading] = useState(true);
  const [pageData, setPageData] = useState(null);
  const [isPhaseTwo, setIsPhaseTwo] = useState(true);
  const [canScroll, setCanScroll] = useState(true);
  const [isMobile, setIsMobile] = useState(false);
  const containerRef = useRef(null);
  const { language } = useLanguage();
  const isRtl = language === "ar";

  useEffect(() => {
    fetchAPI(`/pages/home?lang=${language}`).then(data => {
      if (data) setPageData(data);
    }).catch(console.error);
  }, [language]);

  useEffect(() => {
    const handleResize = () => setIsMobile(window.innerWidth < 768);
    handleResize(); // set initial value
    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, []);

  useEffect(() => {
    window.history.scrollRestoration = "manual";
    window.scrollTo(0, 0);

    const loadingTimer = setTimeout(() => {
      setIsLoading(false);
    }, 50);

    // Fix anchor links (like #services) since the page locks scroll initially
    let scrollTimer;
    if (window.location.hash) {
      scrollTimer = setTimeout(() => {
        const id = window.location.hash.substring(1);
        const el = document.getElementById(id);
        if (el) {
          el.scrollIntoView({ behavior: "smooth" });
        }
      }, 500); // Give dynamic components time to render
    }

    return () => {
      clearTimeout(loadingTimer);
      if (scrollTimer) clearTimeout(scrollTimer);
    };
  }, []);

  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start start", "end end"],
  });

  const smoothProgress = useSpring(scrollYProgress, {
    stiffness: 70,
    damping: 20,
    restDelta: 0.001,
  });

  // Earth movement: moves from Hero to Features
  // It reaches its "middle" position early (at 0.3) and stays there
  const earthY = useTransform(
    smoothProgress,
    [0, 0.2],
    isMobile ? ["0%", "25%"] : ["0%", "60%"],
  );
  const earthScale = useTransform(
    smoothProgress,
    [0, 0.2],
    isMobile ? [0.6, 0.4] : [1, 0.8],
  );
  const earthOpacity = useTransform(
    smoothProgress,
    [0, 0.1, 0.6, 0.7],
    [1, 1, 1, 0],
  );
  const earthX = useTransform(
    smoothProgress,
    [0, 0.2],
    isMobile ? ["0%", "0%"] : (isRtl ? ["0%", "5%"] : ["0%", "-5%"])
  );

  // PlanetAstronaut movement: CTA to Footer
  const planetOpacity = useTransform(
    smoothProgress,
    [0.6, 0.92, 1],
    [0, 0.8, 0.35], // Reduced final opacity so text is readable
  );
  const planetX = useTransform(
    smoothProgress,
    [0.88, 0.95, 1],
    isRtl ? ["25%", "-45%", "-45%"] : ["-25%", "45%", "45%"]
  );
  const planetY = useTransform(
    smoothProgress,
    [0.88, 0.95, 1],
    ["0%", "0%", "0%"],
  );
  const planetScale = useTransform(
    smoothProgress,
    [0.88, 0.92, 0.96, 1],
    [0.7, 1.2, 1.1, 1],
  );
  const planetRotate = useTransform(smoothProgress, [0.88, 1], [0, 10]);
  const planetZIndex = useTransform(
    smoothProgress,
    [0.88, 0.92, 1],
    [-1, 0, 0], // Keeps it below the Footer's z-10 content
  );
  const planetFilter = useTransform(
    smoothProgress,
    [0.88, 1],
    ["contrast(100%) brightness(100%)", "contrast(60%) brightness(60%)"] // Reduces contrast and brightness at the bottom
  );
  return (
    <main
      className={`flex min-h-screen flex-col relative ${!canScroll ? "h-screen overflow-hidden" : "overflow-hidden"}`}
    >
      {isLoading && (
        <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black">
          <Loader />
        </div>
      )}
      <div className="relative" ref={containerRef}>
        <div className="w-full">
          <Hero isPhaseTwo={isPhaseTwo} data={pageData} />

          {/* Dynamic Sections from Dashboard */}
          <PageSections sections={pageData?.sections} />

          <Features />
          <Services />
          <OnlineWebsite />
          <OnlineWebsiteDetails />
          <OurBlog />
          <Testimonial />
          <ContactUs />
          <CTA />

          <div className="absolute inset-0 pointer-events-none z-40">
            {/* Global Earth */}
            <motion.div
              style={{
                scale: earthScale,
                opacity: earthOpacity,
                x: earthX,
                y: earthY,
              }}
              className="sticky top-0 flex items-center justify-center h-screen"
            >
              <Earth isPhaseTwo={isPhaseTwo} />
            </motion.div>
          </div>

          {/* Global PlanetAstronaut - Fixed layer for better visibility */}
          <motion.div
            style={{
              scale: planetScale,
              opacity: planetOpacity,
              x: planetX,
              y: planetY,
              rotate: planetRotate,
              zIndex: planetZIndex,
              filter: planetFilter,
            }}
            className="fixed inset-0 pointer-events-none hidden md:flex items-center justify-center h-screen"
          >
            <div className="w-[300px] md:w-[450px] lg:w-[550px] aspect-square">
              <PlanetAstronaut progress={smoothProgress} />
            </div>
          </motion.div>
        </div>
      </div>
    </main>
  );
}
