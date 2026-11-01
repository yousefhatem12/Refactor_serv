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
        className="absolute inset-0 z-10 pointer-events-none"
      >
        <img
          src="/Hero/front-stars.svg"
          alt=""
          aria-hidden="true"
          fetchPriority="high"
          className="w-full h-full object-cover"
        />
      </motion.div>

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
        className="absolute top-0 left-0 w-full h-full z-20 pointer-events-none"
      >
        <img
          src="/Hero/left-starts-Animate.svg"
          alt=""
          aria-hidden="true"
          fetchPriority="high"
          className="w-full h-full object-contain"
        />
      </motion.div>

      {/* Dark Gradient Overlay for Depth */}
      <div className="absolute inset-0 bg-gradient-to-b from-blue-950/20 via-transparent to-transparent z-30" />
    </div>
  );
}
