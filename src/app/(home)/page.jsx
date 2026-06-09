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
import { useLanguage } from "../context/LanguageContext";
import { fetchAPI } from "@/lib/api";
import Loading from "../loading";

export default function HomePage() {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(null);
  const [isPhaseTwo, setIsPhaseTwo] = useState(false);
  const [canScroll, setCanScroll] = useState(false);
  const [isMobile, setIsMobile] = useState(false);
  const containerRef = useRef(null);
  const isRtl = language === "ar";

  useEffect(() => {
    let isMounted = true;

    const loadDataSequentially = async () => {
      try {
        // 1. Fetch critical home page structure and products in parallel from the database
        const [homeData, productsData] = await Promise.all([
          fetchAPI(`/pages/home?lang=${language}`),
          fetchAPI(`/products?lang=${language}&category=all&page=1&per_page=3`),
        ]);

        if (!isMounted) return;

        if (homeData) {
          setPageData(homeData);
          if (typeof window !== "undefined") {
            try {
              localStorage.setItem(
                `cached_home_data_${language}`,
                JSON.stringify(homeData),
              );
              localStorage.setItem(
                `cached_products_data_${language}`,
                JSON.stringify(productsData),
              );
            } catch (e) {
              console.error("Cache write error:", e);
            }
          }
        }

        // 2. Fetch the remaining endpoints in parallel in background to populate cache
        const remainingEndpoints = [
          `/settings?lang=${language}`,
          `/settings`,
          `/features`,
          `/services?lang=${language}`,
          `/products/categories?lang=${language}`,
          `/portfolio/categories?lang=${language}`,
          `/blog?lang=${language}&limit=5`,
          `/blog/trending?lang=${language}`,
          `/testimonials?lang=${language}`,
        ];

        Promise.allSettled(
          remainingEndpoints.map((endpoint) => fetchAPI(endpoint)),
        ).then(() => {
          if (isMounted) {
            console.log("Parallel preloading completed successfully");
          }
        });
      } catch (error) {
        console.error("Error in parallel preloading:", error);
      }
    };

    loadDataSequentially();

    return () => {
      isMounted = false;
    };
  }, [language]);

  useEffect(() => {
    const handleResize = () => setIsMobile(window.innerWidth < 768);
    handleResize(); // set initial value
    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, []);

  useEffect(() => {
    setIsPhaseTwo(true);
    setCanScroll(true);
    if (typeof window !== "undefined") {
      sessionStorage.setItem("seenHomeIntro", "true");
    }
  }, []);

  useEffect(() => {
    // Dynamically manage body/html overflow to lock scrolling
    if (!canScroll) {
      document.body.style.overflow = "hidden";
      document.documentElement.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
      document.documentElement.style.overflow = "";
    }
    return () => {
      document.body.style.overflow = "";
      document.documentElement.style.overflow = "";
    };
  }, [canScroll]);

  useEffect(() => {
    window.history.scrollRestoration = "manual";
    window.scrollTo(0, 0);

    // Fix anchor links (like #services) since the page locks scroll initially
    if (window.location.hash) {
      const id = window.location.hash.substring(1);
      const el = document.getElementById(id);
      if (el) {
        el.scrollIntoView({ behavior: "smooth" });
      }
    }
  }, [pageData]);

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
    isMobile ? ["0%", "0%"] : isRtl ? ["0%", "5%"] : ["0%", "-5%"],
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
    isRtl ? ["25%", "-45%", "-45%"] : ["-25%", "45%", "45%"],
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
    ["contrast(100%) brightness(100%)", "contrast(60%) brightness(60%)"], // Reduces contrast and brightness at the bottom
  );
  return (
    <main
      className={`flex min-h-screen flex-col relative ${!canScroll || !pageData ? "h-screen overflow-hidden" : "overflow-hidden"}`}
    >
      <div className="relative" ref={containerRef} style={{ position: "relative" }}>
        <div className="w-full">
          {!pageData ? (
            <Loading />
          ) : (
            <>
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
            </>
          )}

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
            className="fixed inset-0 pointer-events-none hidden lg:flex items-center justify-center h-screen"
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
