"use client";
import React, { useState, useEffect } from "react";
import { createPortal } from "react-dom";
import { motion, AnimatePresence } from "framer-motion";
import { getMediaUrl } from "@/lib/api";

const VideoModal = ({ isOpen, onClose, videoUrl }) => {
  const [mounted, setMounted] = useState(false);
  useEffect(() => {
    setMounted(true);
  }, []);

  if (!mounted) return null;

  return createPortal(
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6 md:p-10">
          {/* Backdrop */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="absolute inset-0 bg-black/60 backdrop-blur-md cursor-pointer"
          />

          {/* Modal Container */}
          <motion.div
            initial={{ scale: 0.9, opacity: 0, y: 20 }}
            animate={{ scale: 1, opacity: 1, y: 0 }}
            exit={{ scale: 0.9, opacity: 0, y: 20 }}
            transition={{ type: "spring", damping: 25, stiffness: 300 }}
            className="relative w-full max-w-5xl aspect-video bg-black rounded-2xl overflow-hidden shadow-[0_0_50px_rgba(217,139,80,0.3)] border border-white/10 z-10"
          >
            {/* Close Button */}
            <button
              onClick={onClose}
              className="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all hover:rotate-90 group border-none cursor-pointer"
            >
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>

            {/* Video Content */}
            <div className="w-full h-full">
              {videoUrl ? (
                (() => {
                  const isExternal = videoUrl.startsWith("http") || videoUrl.startsWith("https");
                  const isYouTube = videoUrl.includes("youtube.com") || videoUrl.includes("youtu.be");

                  if (isExternal && isYouTube) {
                    let finalUrl = videoUrl;
                    if (videoUrl.includes("youtube.com/watch?v=")) {
                      finalUrl = videoUrl.replace("watch?v=", "embed/");
                    } else if (videoUrl.includes("youtube.com/shorts/")) {
                      finalUrl = videoUrl.replace("shorts/", "embed/");
                    } else if (videoUrl.includes("youtu.be/")) {
                      finalUrl = videoUrl.replace("youtu.be/", "youtube.com/embed/");
                    }
                    return (
                      <iframe
                        src={finalUrl}
                        title="Company Video"
                        className="w-full h-full border-none"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowFullScreen
                      />
                    );
                  } else if (isExternal) {
                    // Other external video links
                    return (
                      <video
                        src={videoUrl}
                        controls
                        autoPlay
                        className="w-full h-full"
                      />
                    );
                  } else {
                    // Local storage video
                    const fullUrl = getMediaUrl(videoUrl);
                    return (
                      <video
                        src={fullUrl}
                        controls
                        autoPlay
                        className="w-full h-full"
                      />
                    );
                  }
                })()
              ) : (
                <div className="w-full h-full flex flex-col items-center justify-center text-white/50 gap-4">
                  <div className="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                     <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="5 3 19 12 5 21 5 3" />
                     </svg>
                  </div>
                  <p className="text-lg font-medium tracking-wide">Video Coming Soon...</p>
                </div>
              )}
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>,
    document.body
  );
};

export default VideoModal;
