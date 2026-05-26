import React from "react";
import HomeBackground from "./components/Home/HomeBackground";
export default function Loading() {
  return (
    <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-[#0a0a0a]">
      <HomeBackground />
      <div className="loader"></div>
    </div>
  );
}
