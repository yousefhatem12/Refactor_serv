"use client";
import React, { useState, useEffect, useRef } from "react";
import { motion, useScroll, useTransform, useSpring } from "framer-motion";
import Hero from "../components/Home/Hero/Hero";
import PageSections from "../components/Shared/PageSections";
import dynamic from "next/dynamic";

// All dynamic imports use { ssr: false } because:
// 1. HomePageClient is a "use client" component
// 2. These components use browser APIs (window, IntersectionObserver, etc.)
// 3. { ssr: true } inside a client component causes hydration mismatch (React error #418)
//    because the server renders HTML for them but the client receives mismatched bundles
const Features = dynamic(() => import("../components/Home/Features/Features"), { ssr: false });
const Services = dynamic(() => import("../components/Home/Services/Services"), { ssr: false });
const OnlineWebsite = dynamic(() => import("../components/Home/OnlineWebsite/OnlineWebsite"), { ssr: false });
const OnlineWebsiteDetails = dynamic(() => import("../components/Home/OnlineWebsite/OnlineWebsiteDetails"), { ssr: false });
const Earth = dynamic(() => import("../components/Home/Hero/Earth"), { ssr: false });
const OurBlog = dynamic(() => import("../components/Home/Blog/OurBlog"), { ssr: false });
const Testimonial = dynamic(() => import("../components/Home/Testimonial/Testimonial"), { ssr: false });
const ContactUs = dynamic(() => import("../components/Home/ContactUs/ContactUs"), { ssr: false });
const CTA = dynamic(() => import("../components/Home/CTA/CTA"), { ssr: false });
const PlanetAstronaut = dynamic(() => import("../components/Home/Shared/PlanetAstronaut"), { ssr: false });
import { useLanguage } from "../context/LanguageContext";
import { fetchAPI } from "@/lib/api";
import Loading from "../loading";

function LazySection({ children, minHeight = "600px", rootMargin = "500px" }) {
  const [shouldRender, setShouldRender] = useState(() => {
    if (typeof window !== "undefined" && window.location.hash) {
      return true;
    }
    return false;
  });
  const ref = useRef(null);

  useEffect(() => {
    if (shouldRender) return;
    const el = ref.current;
    if (!el) return;

    if (typeof IntersectionObserver === "undefined") {
      setShouldRender(true);
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting) {
          setShouldRender(true);
          observer.disconnect();
        }
      },
      { rootMargin }
    );

    observer.observe(el);
    return () => observer.disconnect();
  }, [shouldRender, rootMargin]);

  return (
    <div
      ref={ref}
      className="content-visibility-auto"
      style={{ minHeight: shouldRender ? undefined : minHeight }}
    >
      {shouldRender ? children : null}
    </div>
  );
}

export default function HomePageClient({ initialHomeData, initialLocale = "en" }) {
  const { language } = useLanguage();
  const [pageData, setPageData] = useState(initialHomeData);
  const [isPhaseTwo, setIsPhaseTwo] = useState(true);
  const [canScroll, setCanScroll] = useState(true);
  // null initial value prevents server/client mismatch (React error #418).
  // A null check in JSX renders the same on server and client until useEffect fires.
  const [isMobile, setIsMobile] = useState(null);
  const containerRef = useRef(null);
  const isRtl = language === "ar";
  const initialLangRef = useRef(initialLocale);
  const isInitialMountRef = useRef(true);

  useEffect(() => {
    let isMounted = true;

    const loadDataSequentially = async () => {
      try {
        const isFirstMountWithData = isInitialMountRef.current && initialHomeData && language === initialLangRef.current;
        isInitialMountRef.current = false;

        // Priority 1: Top section data (Home and Featured Products) executed first
        // Skip duplicate home API call if initial data was already rendered by SSR
        const [homeData, productsData] = await Promise.all([
          isFirstMountWithData
            ? Promise.resolve(null)
            : fetchAPI(`/pages/home?lang=${language}`, { priority: "high" }),
          fetchAPI(`/products?lang=${language}&category=all&page=1&per_page=3`, { priority: "high" }),
        ]);

        if (!isMounted) return;

        if (homeData) {
          const actualHomeData = homeData.data || homeData;
          if (JSON.stringify(pageData) !== JSON.stringify(actualHomeData)) {
            setPageData(actualHomeData);
          }
          if (actualHomeData?.seo?.title || actualHomeData?.title) {
            document.title = actualHomeData.seo?.title || actualHomeData.title;
          }
          if (typeof window !== "undefined") {
            try {
              localStorage.setItem(
                `cached_home_data_${language}`,
                JSON.stringify(actualHomeData),
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
      } catch (error) {
        console.error("Error in sequential loading:", error);
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
    const timer = setTimeout(() => {
      setIsPhaseTwo(true);
      setCanScroll(true);
    }, 0);
    if (typeof window !== "undefined") {
      sessionStorage.setItem("seenHomeIntro", "true");
    }
    return () => clearTimeout(timer);
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
  }, []);

  useEffect(() => {
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
    [0.88, 0.92, 1],
    [0, 0.8, 0.35],
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
    [-1, 0, 0],
  );
  const planetFilter = useTransform(
    smoothProgress,
    [0.88, 1],
    ["contrast(100%) brightness(100%)", "contrast(60%) brightness(60%)"],
  );

  return (
    <main
      className={`flex min-h-screen flex-col relative ${!canScroll ? "h-screen overflow-hidden" : "overflow-hidden"}`}
    >
      <div className="relative" ref={containerRef} style={{ position: "relative" }}>
        <div className="w-full">
          <Hero isPhaseTwo={isPhaseTwo} data={pageData} />

          {/* Dynamic Sections from Dashboard */}
          {pageData?.sections && <PageSections sections={pageData.sections} />}

          <Features />
          <LazySection minHeight="700px"><Services /></LazySection>
          <LazySection minHeight="700px"><OnlineWebsite /></LazySection>
          <LazySection minHeight="500px"><OnlineWebsiteDetails /></LazySection>
          <LazySection minHeight="600px"><OurBlog /></LazySection>
          <LazySection minHeight="600px"><Testimonial /></LazySection>
          <LazySection minHeight="600px"><ContactUs /></LazySection>
          <LazySection minHeight="800px"><CTA /></LazySection>

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

          {/* Global PlanetAstronaut */}
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
