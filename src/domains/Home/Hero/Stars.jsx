import React from "react";
import Image from "next/image";

export default function Stars() {
  return (
    <div className="absolute inset-0 z-0 overflow-hidden select-none pointer-events-none">
      {/* Front Stars Layer */}
      <div className="absolute inset-0 z-10 pointer-events-none animate-stars-pulse">
        <Image
          src="/Hero/front-stars.svg"
          alt=""
          aria-hidden="true"
          fill
          priority
          sizes="100vw"
          className="object-cover"
        />
      </div>

      {/* Left Stars Animated Layer */}
      <div className="absolute top-0 left-0 w-full h-full z-20 pointer-events-none animate-stars-drift">
        <Image
          src="/Hero/left-starts-Animate.svg"
          alt=""
          aria-hidden="true"
          fill
          priority
          sizes="100vw"
          className="object-contain"
        />
      </div>

      {/* Dark Gradient Overlay for Depth */}
      <div className="absolute inset-0 bg-gradient-to-b from-blue-950/20 via-transparent to-transparent z-30" />
    </div>
  );
}
