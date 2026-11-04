"use client";
import React, { useState, useEffect, useRef } from "react";
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
  const earthRef = useRef(null);
  const planetRef = useRef(null);
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

  useEffect(() => {
    let ticking = false;

    const lerp = (start, end, t) => start + (end - start) * t;
    const mapRange = (val, inMin, inMax, outMin, outMax) => {
      if (val <= inMin) return outMin;
      if (val >= inMax) return outMax;
      return outMin + ((val - inMin) / (inMax - inMin)) * (outMax - outMin);
    };

    const updateScrollAnimations = () => {
      const container = containerRef.current;
      if (!container) return;

      const rect = container.getBoundingClientRect();
      const scrollHeight = container.scrollHeight - window.innerHeight;
      const progress = scrollHeight > 0 ? Math.min(Math.max(-rect.top / scrollHeight, 0), 1) : 0;
      const isMob = window.innerWidth < 768;

      // 1. Earth movement (Hero to Features: progress 0 -> 0.7)
      if (earthRef.current) {
        if (progress < 0.75) {
          const pEarth = Math.min(progress / 0.2, 1);
          const yPct = isMob ? lerp(0, 25, pEarth) : lerp(0, 60, pEarth);
          const scale = isMob ? lerp(0.6, 0.4, pEarth) : lerp(1, 0.8, pEarth);
          const xPct = isMob ? 0 : (isRtl ? lerp(0, 5, pEarth) : lerp(0, -5, pEarth));

          let opacity = 1;
          if (progress > 0.6) {
            opacity = mapRange(progress, 0.6, 0.7, 1, 0);
          }

          earthRef.current.style.transform = `translate3d(${xPct}%, ${yPct}%, 0) scale(${scale})`;
          earthRef.current.style.opacity = `${opacity}`;
          earthRef.current.style.display = opacity <= 0 ? "none" : "";
        } else {
          earthRef.current.style.display = "none";
        }
      }

      // 2. Planet Astronaut movement (Near CTA / Footer: progress 0.88 -> 1.0)
      if (planetRef.current) {
        if (progress >= 0.85) {
          let opacity = 0;
          if (progress <= 0.92) {
            opacity = mapRange(progress, 0.88, 0.92, 0, 0.8);
          } else {
            opacity = mapRange(progress, 0.92, 1.0, 0.8, 0.35);
          }

          const pX = mapRange(progress, 0.88, 0.95, 0, 1);
          const startX = isRtl ? 25 : -25;
          const endX = isRtl ? -45 : 45;
          const xPct = lerp(startX, endX, pX);

          let scale = 0.7;
          if (progress < 0.92) {
            scale = mapRange(progress, 0.88, 0.92, 0.7, 1.2);
          } else if (progress < 0.96) {
            scale = mapRange(progress, 0.92, 0.96, 1.2, 1.1);
          } else {
            scale = mapRange(progress, 0.96, 1.0, 1.1, 1.0);
          }

          const rotate = mapRange(progress, 0.88, 1.0, 0, 10);
          const zIndex = progress >= 0.92 ? 0 : -1;
          const contrast = mapRange(progress, 0.88, 1.0, 100, 60);

          planetRef.current.style.display = opacity <= 0 ? "none" : "flex";
          planetRef.current.style.opacity = `${opacity}`;
          planetRef.current.style.transform = `translate3d(${xPct}%, 0, 0) scale(${scale}) rotate(${rotate}deg)`;
          planetRef.current.style.zIndex = `${zIndex}`;
          planetRef.current.style.filter = `contrast(${contrast}%) brightness(${contrast}%)`;
        } else {
          planetRef.current.style.display = "none";
        }
      }

      ticking = false;
    };

    const onScroll = () => {
      if (!ticking) {
        requestAnimationFrame(updateScrollAnimations);
        ticking = true;
      }
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll, { passive: true });
    updateScrollAnimations();

    return () => {
      window.removeEventListener("scroll", onScroll);
      window.removeEventListener("resize", onScroll);
    };
  }, [isRtl]);

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
            <div
              ref={earthRef}
              className="sticky top-0 flex items-center justify-center h-screen will-change-transform"
            >
              <Earth isPhaseTwo={isPhaseTwo} />
            </div>
          </div>

          {/* Global PlanetAstronaut */}
          <div
            ref={planetRef}
            className="fixed inset-0 pointer-events-none hidden lg:flex items-center justify-center h-screen will-change-transform"
            style={{ opacity: 0, display: "none" }}
          >
            <div className="w-[300px] md:w-[450px] lg:w-[550px] aspect-square">
              <PlanetAstronaut />
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
