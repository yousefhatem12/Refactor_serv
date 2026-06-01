"use client";
import React from "react";

const HomeBackground = () => {
  return (
    <div
      className="fixed bg-[#040A37] inset-0 bg-no-repeat bg-cover bg-center -z-10 mix-blend-screen [mask-image:radial-gradient(circle,black_0%,transparent_100%)]
      [-webkit-mask-image:radial-gradient(circle,black_0%,transparent_100%)] saturate-[130%] brightness-[150%] contrast-[120%] transform-gpu will-change-transform"
      style={{ backgroundImage: "url('/Hero/bg.webp')" }}
    />
  );
};

export default HomeBackground;
