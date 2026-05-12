"use client";
import React from "react";
import { motion, useTransform, useMotionValue } from "framer-motion";

const PlanetAstronaut = ({ className = "", progress, ...props }) => {
  const fallbackProgress = useMotionValue(0);
  const p = progress || fallbackProgress;

  return (
    <div
      className={`relative flex items-center justify-center ${className}`}
      {...props}
    >
      {/* The Planet & Astronaut Wrapper */}
      <motion.div 
        className="relative w-full aspect-square flex items-center justify-center"
      >
        <div className="relative w-full aspect-square">
          <img
            src="/Hero/orange-planet.png"
            alt="Planet"
            className="w-full h-full object-contain drop-shadow-[0_0_80px_rgba(255,138,0,0.4)] animate-[spin_60s_linear_infinite]"
          />
          {/* Subtle Glow */}
          <div className="absolute inset-0 bg-orange-500/10 blur-[100px] rounded-full -z-10"></div>
        </div>

        {/* The Astronaut */}
        <motion.img
          src="/Hero/astronaut.svg"
          alt="Astronaut"
          className="absolute top-1/2 left-1/4 w-[80%] h-auto object-contain z-10"
          animate={{
            x: ["-50%", "-52%", "-50%"],
            y: ["-50%", "-55%", "-50%"],
            rotate: [0, 5, 0],
          }}
          transition={{
            duration: 6,
            repeat: Infinity,
            ease: "easeInOut",
          }}
        />
      </motion.div>
    </div>
  );
};

export default PlanetAstronaut;
