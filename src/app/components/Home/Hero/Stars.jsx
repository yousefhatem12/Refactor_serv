"use client";
import React from "react";
import { motion } from "framer-motion";

export default function Stars() {
  return (
    <div className="absolute inset-0 z-0 overflow-hidden select-none pointer-events-none">
      {/* Front Stars Layer */}
      <motion.div
        initial={{ opacity: 0.5 }}
        animate={{ opacity: [0.4, 0.7, 0.4] }}
        transition={{ duration: 5, repeat: Infinity, ease: "easeInOut" }}
        className="absolute inset-0 z-10"
        style={{
          backgroundImage: "url('/Hero/front-stars.svg')",
          backgroundSize: "cover",
        }}
      />

      {/* Left Stars Animated Layer */}
      <motion.div
        animate={{
          x: [0, 15, 0],
          y: [0, -10, 0],
          opacity: [0.6, 1, 0.6],
        }}
        transition={{
          duration: 8,
          repeat: Infinity,
          ease: "easeInOut",
        }}
        className="absolute top-0 left-0 w-full h-full z-20"
        style={{
          backgroundImage: "url('/Hero/left-starts-Animate.svg')",
          backgroundSize: "contain",
          backgroundRepeat: "no-repeat",
        }}
      />

      {/* Dark Gradient Overlay for Depth */}
      <div className="absolute inset-0 bg-gradient-to-b from-blue-950/20 via-transparent to-black/40 z-30" />
    </div>
  );
}
