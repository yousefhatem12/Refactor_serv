import React from "react";
import HomeBackground from "./components/Home/HomeBackground";

export default function Loading() {
  return (
    <div
      className="min-h-screen flex items-center justify-center bg-[#0a0a0a] relative"
      aria-busy="true"
      aria-label="Loading content"
    >
      <HomeBackground />
      <div className="loader"></div>
    </div>
  );
}
