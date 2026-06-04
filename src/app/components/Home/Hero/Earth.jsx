"use client";
import React, { useEffect, useRef } from "react";
import gsap from "gsap";
import { useLanguage } from "../../../context/LanguageContext";

export default function Earth({ isPhaseTwo }) {
  const containerRef = useRef(null);
  const earthRef = useRef(null);
  const sunlightRef = useRef(null);
  const logoRef = useRef(null);
  const astronautWrapperRef = useRef(null);
  const astronautImgRef = useRef(null);
  const { language } = useLanguage();
  const isRtl = language === "ar";

  useEffect(() => {
    // Initial setup
    gsap.set(logoRef.current, { y: -10, scale: 1, opacity: 0.5 });

    // Continuous Earth Rotation
    gsap.to(earthRef.current, {
      rotation: 360,
      duration: 100,
      repeat: -1,
      ease: "none",
    });

    // Continuous Sunlight Movement
    gsap.to(sunlightRef.current, {
      x: "-50%",
      duration: 3,
      ease: "power2.inOut",
      repeat: -1,
      yoyo: true,
      repeatDelay: 2,
    });

    // Logo Floating
    gsap.to(logoRef.current, {
      y: 10,
      scale: 1.02,
      opacity: 1,
      duration: 2,
      repeat: -1,
      yoyo: true,
      ease: "power1.inOut",
    });

    // Astronaut Floating (Inner Image)
    gsap.to(astronautImgRef.current, {
      y: -15,
      rotation: 5,
      duration: 3,
      repeat: -1,
      yoyo: true,
      ease: "power1.inOut",
    });
  }, []);

  useEffect(() => {
    const isMobile = window.innerWidth < 768;

    // Phase Two Animations (Container and Astronaut Entrance)
    if (isPhaseTwo) {
      // Main container smooth scale & move
      gsap.to(containerRef.current, {
        x: isMobile ? "0%" : isRtl ? "-25%" : "25%",
        scale: isMobile ? 1 : 1.2, // Reduced from 1.6 to make it smaller
        duration: 1.5,
        ease: "power3.inOut",
      });

      // Astronaut Entrance
      if (!isMobile) {
        gsap.fromTo(
          astronautWrapperRef.current,
          {
            x: isRtl ? "-300%" : "300%",
            y: "50%",
            opacity: 0,
            rotation: isRtl ? -45 : 45,
            scale: 0.5,
          },
          {
            x: isRtl ? "85%" : "-85%",
            y: "85%",
            opacity: 1,
            rotation: isRtl ? 65 : 0,
            scale: 1.5,
            duration: 1.5,
            delay: 0.3,
            ease: "power3.out",
          },
        );
      }
    } else {
      // Phase One state
      gsap.to(containerRef.current, {
        x: "0%",
        scale: isMobile ? 0.8 : 0.6, // Reduced from 0.8 to make it smaller
        duration: 1.5,
        ease: "power3.inOut",
      });

      // Hide Astronaut
      gsap.set(astronautWrapperRef.current, { opacity: 0 });
    }
  }, [isPhaseTwo, isRtl]);

  return (
    <div
      ref={containerRef}
      className="relative flex items-center justify-center w-full h-screen  will-change-transform"
    >
      <div className="relative w-[400px] h-[400px] lg:w-[400px] lg:h-[400px]">
        {/* Earth Background Glow */}
        <div className="absolute inset-0 rounded-full bg-blue-500/20 blur-[80px] animate-pulse overflow-hidden will-change-opacity hidden md:block" />

        {/* Earth SVG */}
        <div
          ref={earthRef}
          className="absolute overflow-hidden inset-0 z-10 will-change-transform hidden md:block"
        >
          <img
            src="/Hero/earth.webp"
            alt="Earth"
            className="w-full h-full object-contain"
          />
        </div>

        {/* Sunlight Layer */}
        <div className="absolute inset-[5px] lg:inset-[5px] z-10 rounded-full overflow-hidden pointer-events-none hidden md:block">
          <div
            ref={sunlightRef}
            className="absolute inset-y-0 left-0 w-[200%] bg-[linear-gradient(to_right,rgba(0,0,0,0.8)_0%,rgba(0,0,0,0.8)_50%,transparent_65%)] mix-blend-multiply will-change-transform"
          />
        </div>

        {/* Center Logo Overlay */}
        <div
          ref={logoRef}
          className="absolute inset-0 flex items-center justify-center z-20 pointer-events-none"
        >
          <div className="w-[140%] h-[140%] sm:w-[120%] sm:h-[120%] md:w-[40%] md:h-[40%] relative transition-all duration-700">
            <img
              src="/Hero/logo.svg"
              alt="Serv5 Logo"
              className="w-full h-full object-contain 
                         mix-blend-luminosity md:mix-blend-normal 
                         drop-shadow-[0_0_40px_rgba(255,255,255,0.8)] md:drop-shadow-[0_0_20px_rgba(255,255,255,0.5)] 
                         brightness-150 md:brightness-100"
            />
          </div>
        </div>

        {/* Astronaut - Phase 2 Entrance */}
        <div
          ref={astronautWrapperRef}
          className={`absolute top-0 ${isRtl ? "left-0" : "right-0"} z-30 w-[100px] h-[100px] md:w-[130px] md:h-[130px] lg:w-[200px] lg:h-[200px] pointer-events-none opacity-0 hidden md:block`}
        >
          <div
            style={{
              transform: isRtl ? "scaleX(-1) rotate(70deg)" : "none",
              width: "100%",
              height: "100%",
            }}
          >
            <img
              ref={astronautImgRef}
              src="/Hero/astronaut.webp"
              alt="Astronaut"
              className="w-full h-full object-contain"
            />
          </div>
        </div>
      </div>
    </div>
  );
}
