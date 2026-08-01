/* eslint-disable @next/next/no-img-element */
"use client";
import React, { useState, useRef, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { fetchAPI } from "@/lib/api";

const StarRating = ({ rating }) => {
  return (
    <div className="flex gap-1 mb-6">
      {[...Array(5)].map((_, i) => (
        <svg
          key={i}
          className={`w-4 h-4 ${i < rating ? "text-yellow-400 fill-yellow-400" : "text-gray-600 fill-gray-600"}`}
          viewBox="0 0 20 20"
        >
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
      ))}
    </div>
  );
};

const Testimonial = () => {
  const [activeMedia, setActiveMedia] = useState(null);
  const sliderRef = useRef(null);
  const { language, t } = useLanguage();

  const [data, setData] = useState([]);
  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(true);

  const checkScroll = () => {
    if (sliderRef.current) {
      const { scrollLeft, scrollWidth, clientWidth } = sliderRef.current;
      const maxScroll = scrollWidth - clientWidth;
      
      if (maxScroll <= 0) {
        setCanScrollLeft(false);
        setCanScrollRight(false);
        return;
      }

      const isRtl = language === "ar";
      const currentScroll = Math.abs(scrollLeft);
      const atStart = currentScroll < 10;
      const atEnd = currentScroll > maxScroll - 10;

      if (isRtl) {
        setCanScrollLeft(!atEnd);
        setCanScrollRight(!atStart);
      } else {
        setCanScrollLeft(!atStart);
        setCanScrollRight(!atEnd);
      }
    }
  };

  const scrollLeft = () => {
    if (sliderRef.current) {
      sliderRef.current.scrollBy({ left: -320, behavior: "smooth" });
      setTimeout(checkScroll, 300);
    }
  };

  const scrollRight = () => {
    if (sliderRef.current) {
      sliderRef.current.scrollBy({ left: 320, behavior: "smooth" });
      setTimeout(checkScroll, 300);
    }
  };

  useEffect(() => {
    fetchAPI(`/testimonials?lang=${language}`)
      .then((res) => {
        const testimonialData = res && res.data ? res.data : res;
        if (testimonialData && testimonialData.length > 0) {
          setData(testimonialData);
          // Check initial scroll state after data loads
          setTimeout(checkScroll, 100);
        }
      })
      .catch(console.error);
  }, [language]);

  const displayTestimonials = data.map((item) => ({
    id: item.id,
    name: item.client_name,
    role: item.position,
    quote: item.review,
    rating: item.rating || 5,
    avatar: item.image || "/user-placeholder.webp",
    videoUrl: item.video_url || null,
    audioUrl: item.audio_url || null,
  }));

  if (displayTestimonials.length === 0) return null;

  return (
    <section className="py-20 px-4 md:px-8 relative overflow-hidden">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-16"
        >
          <h2 className="text-4xl md:text-5xl font-black text-[#FF8A00] mb-4">
            {t("testimonial")}
          </h2>
          <div className="flex flex-col items-center gap-2">
            <p className="text-gray-300 text-lg">{t("trustedByClients")}</p>
            <div className="flex gap-1 text-yellow-400">
              {[...Array(5)].map((_, i) => (
                <svg
                  key={i}
                  className="w-5 h-5 fill-current"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              ))}
            </div>
          </div>
        </motion.div>

        {/* Carousel Container */}
        <div className="relative group w-full flex items-center">
          {/* Navigation Arrows */}
          <button
            onClick={scrollLeft}
            disabled={!canScrollLeft}
            className={`absolute left-0 lg:-left-6 z-30 w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-[#0a0a0f]/80 backdrop-blur-md border border-white/10 text-white transition-all duration-300 shadow-[0_0_20px_rgba(0,0,0,0.5)] ${
              canScrollLeft 
                ? "opacity-100 hover:bg-[#FF8A00] hover:border-[#FF8A00] hover:scale-110 cursor-pointer" 
                : "opacity-30 cursor-not-allowed"
            }`}
            aria-label="Scroll Left"
          >
            <svg
              className="w-5 h-5 md:w-6 md:h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
          <button
            onClick={scrollRight}
            disabled={!canScrollRight}
            className={`absolute right-0 lg:-right-6 z-30 w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-[#0a0a0f]/80 backdrop-blur-md border border-white/10 text-white transition-all duration-300 shadow-[0_0_20px_rgba(0,0,0,0.5)] ${
              canScrollRight 
                ? "opacity-100 hover:bg-[#FF8A00] hover:border-[#FF8A00] hover:scale-110 cursor-pointer" 
                : "opacity-30 cursor-not-allowed"
            }`}
            aria-label="Scroll Right"
          >
            <svg
              className="w-5 h-5 md:w-6 md:h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>

          {/* Cards Slider */}
          <div
            className="w-full flex items-center"
            style={{
              WebkitMaskImage:
                "linear-gradient(to right, transparent, black 10%, black 90%, transparent)",
              maskImage:
                "linear-gradient(to right, transparent, black 10%, black 90%, transparent)",
            }}
          >
            <div
              ref={sliderRef}
              onScroll={checkScroll}
              className="w-full overflow-x-auto pb-16 pt-4 scrollbar-hide snap-x snap-mandatory flex items-stretch gap-6"
              style={{ msOverflowStyle: "none", scrollbarWidth: "none" }}
            >
              {/* Spacer */}
              <div className="w-4 lg:w-8 shrink-0" />

              {displayTestimonials.map((item, index) => (
                <motion.div
                  key={item.id}
                  initial={{ opacity: 0, scale: 0.9 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  viewport={{ once: true, amount: 0.3 }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  className="shrink-0 snap-center w-[280px] md:w-[320px] lg:w-[360px] h-auto flex flex-col"
                >
                  {item.videoUrl ? (
                    // Video Card Design
                    <div className="h-full relative bg-[#0F172A]/40 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition-all group/card shadow-2xl flex flex-col">
                      <div
                        className="relative h-48 w-full bg-black/60 group-hover/card:bg-black/40 transition-colors flex items-center justify-center cursor-pointer"
                        onClick={() =>
                          setActiveMedia({ type: "video", url: item.videoUrl })
                        }
                      >
                        <div className="absolute inset-0 bg-gradient-to-t from-[#0F172A] to-transparent opacity-80 z-0"></div>
                        <div className="relative z-10 w-14 h-14 bg-[#FF8A00]/90 rounded-full flex items-center justify-center shadow-[0_0_20px_rgba(255,138,0,0.5)] group-hover/card:scale-110 group-hover/card:bg-[#FF8A00] transition-all">
                          <svg
                            className="w-6 h-6 text-white ml-1"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                          >
                            <path d="M8 5v14l11-7z" />
                          </svg>
                        </div>
                        <div className="absolute top-3 left-3 bg-black/50 backdrop-blur-sm px-2 py-1 rounded text-[10px] font-bold tracking-wider text-white uppercase border border-white/10">
                          {t("video")}
                        </div>
                      </div>
                      <div className="p-3 md:p-6 flex flex-col flex-grow">
                        <StarRating rating={item.rating} />
                        <p className="text-gray-300 text-sm leading-relaxed mb-6 flex-grow italic line-clamp-3">
                          &ldquo;{item.quote}&rdquo;
                        </p>
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center border border-white/20 overflow-hidden shrink-0">
                            {item.avatar &&
                            item.avatar !== "/user-placeholder.webp" ? (
                              <img
                                src={item.avatar}
                                alt={item.name}
                                className="w-full h-full object-cover"
                              />
                            ) : (
                              <svg
                                className="w-6 h-6 text-white/50"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                              </svg>
                            )}
                          </div>
                          <div>
                            <h4 className="text-white font-bold text-sm leading-tight">
                              {item.name}
                            </h4>
                            <p className="text-[#FF8A00] text-[10px] font-medium uppercase tracking-wider">
                              {item.role}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  ) : item.audioUrl ? (
                    // Audio Card Design
                    <div className="h-full relative bg-gradient-to-br from-[#0F172A]/80 to-[#1e293b]/40 backdrop-blur-md border border-[#FF8A00]/20 rounded-2xl p-3 md:p-6 hover:border-[#FF8A00]/50 transition-all group/card shadow-2xl flex flex-col items-center text-center">
                      <div
                        className="w-16 h-16 mt-2 bg-[#FF8A00]/10 rounded-full flex items-center justify-center mb-4 cursor-pointer group-hover/card:bg-[#FF8A00]/20 transition-all shadow-[0_0_15px_rgba(255,138,0,0.1)] group-hover/card:shadow-[0_0_25px_rgba(255,138,0,0.3)] relative"
                        onClick={() =>
                          setActiveMedia({ type: "audio", url: item.audioUrl })
                        }
                      >
                        <svg
                          className="w-8 h-8 text-[#FF8A00] relative z-10"
                          viewBox="0 0 24 24"
                          fill="currentColor"
                        >
                          <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
                        </svg>
                        <div className="absolute inset-0 rounded-full border border-[#FF8A00]/30 animate-ping opacity-50"></div>
                      </div>
                      <div className="flex gap-1 items-end h-8 mb-6 opacity-60 group-hover/card:opacity-100 transition-opacity">
                        {[30, 60, 40, 80, 50, 100, 60, 30, 70, 40, 20].map(
                          (height, i) => (
                            <div
                              key={i}
                              className="w-1 bg-[#FF8A00] rounded-t-sm transition-all duration-300"
                              style={{ height: `${height}%` }}
                            ></div>
                          ),
                        )}
                      </div>
                      <p className="text-gray-300 text-sm leading-relaxed mb-6 flex-grow italic">
                        &ldquo;{item.quote}&rdquo;
                      </p>
                      <StarRating rating={item.rating} />
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center border border-white/20 overflow-hidden shrink-0">
                          {item.avatar &&
                          item.avatar !== "/user-placeholder.webp" ? (
                            <img
                              src={item.avatar}
                              alt={item.name}
                              className="w-full h-full object-cover"
                            />
                          ) : (
                            <svg
                              className="w-6 h-6 text-white/50"
                              fill="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                          )}
                        </div>
                        <div>
                          <h4 className="text-white font-bold text-sm leading-tight">
                            {item.name}
                          </h4>
                          <p className="text-[#FF8A00] text-[10px] font-medium uppercase tracking-wider">
                            {item.role}
                          </p>
                        </div>
                      </div>
                    </div>
                  ) : (
                    // Text Card Design
                    <div className="h-full relative bg-gradient-to-b from-[#1e293b]/60 to-[#0F172A]/80 backdrop-blur-xl border border-white/5 rounded-2xl p-4 md:p-8 transition-all duration-500 group/card shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:shadow-[0_8px_30px_rgba(255,138,0,0.1)] hover:border-white/10 hover:-translate-y-1 flex flex-col overflow-hidden">
                      {/* Glowing Accent Top Line */}
                      <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#FF8A00] to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>

                      {/* Background glow effect */}
                      <div className="absolute -top-24 -right-24 w-48 h-48 bg-[#FF8A00]/20 rounded-full blur-3xl opacity-0 group-hover/card:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                      {/* Large Background Quote Watermark */}
                      <div className="absolute right-6 bottom-24 rtl:left-6 rtl:right-auto text-white/[0.025] pointer-events-none select-none z-0">
                        <svg
                          className="w-36 h-36 fill-current"
                          viewBox="0 0 32 32"
                        >
                          <path d="M10 8c-3.3 0-6 2.7-6 6v10h10V14H8c0-2.2 1.8-4 4-4V8zm16 0c-3.3 0-6 2.7-6 6v10h10V14h-6c0-2.2 1.8-4 4-4V8z" />
                        </svg>
                      </div>

                      <div className="relative z-10 flex flex-col h-full">
                        {/* Quote Icon & Rating Header */}
                        <div className="flex justify-between items-start mb-6">
                          <div className="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center border border-white/10 group-hover/card:border-[#FF8A00]/30 transition-colors shrink-0">
                            <svg
                              className="w-5 h-5 text-[#FF8A00]"
                              fill="currentColor"
                              viewBox="0 0 32 32"
                            >
                              <path d="M10 8c-3.3 0-6 2.7-6 6v10h10V14H8c0-2.2 1.8-4 4-4V8zm16 0c-3.3 0-6 2.7-6 6v10h10V14h-6c0-2.2 1.8-4 4-4V8z" />
                            </svg>
                          </div>
                          <StarRating rating={item.rating} />
                        </div>

                        {/* Text */}
                        <div className="flex flex-col justify-center flex-grow mb-8 min-h-[140px] relative z-10">
                          <p className="text-gray-100 text-lg md:text-[18px] leading-relaxed font-semibold relative z-10 drop-shadow-md text-center md:text-start">
                            &ldquo;{item.quote}&rdquo;
                          </p>
                        </div>

                        <div className="flex items-center gap-4 mt-auto pt-4 border-t border-white/5">
                          <div className="relative shrink-0">
                            <div className="w-12 h-12 rounded-full bg-gradient-to-br from-[#FF8A00] to-orange-600 p-[2px] group-hover/card:shadow-[0_0_15px_rgba(255,138,0,0.4)] transition-all">
                              <div className="w-full h-full rounded-full bg-[#0F172A] flex items-center justify-center overflow-hidden">
                                {item.avatar &&
                                item.avatar !== "/user-placeholder.webp" ? (
                                  <img
                                    src={item.avatar}
                                    alt={item.name}
                                    className="w-full h-full object-cover"
                                  />
                                ) : (
                                  <svg
                                    className="w-6 h-6 text-white/70"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                  >
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                  </svg>
                                )}
                              </div>
                            </div>
                          </div>
                          <div>
                            <h4 className="text-white font-bold text-base leading-tight group-hover/card:text-[#FF8A00] transition-colors">
                              {item.name}
                            </h4>
                            <p className="text-gray-400 text-xs font-medium uppercase tracking-wider mt-0.5">
                              {item.role}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  )}
                </motion.div>
              ))}

              {/* Spacer */}
              <div className="w-4 lg:w-8 shrink-0" />
            </div>
          </div>
        </div>
      </div>

      {/* Custom styles to hide scrollbar for webkit */}
      <style
        dangerouslySetInnerHTML={{
          __html: `
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
      `,
        }}
      />

      {/* Media Modal Popup */}
      <AnimatePresence>
        {activeMedia && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            onClick={() => setActiveMedia(null)}
          >
            <motion.div
              initial={{ scale: 0.9, opacity: 0, y: 20 }}
              animate={{ scale: 1, opacity: 1, y: 0 }}
              exit={{ scale: 0.9, opacity: 0, y: 20 }}
              transition={{ type: "spring", bounce: 0.3 }}
              className="relative w-full max-w-4xl bg-[#0F172A] rounded-2xl border border-white/10 shadow-2xl p-4 md:p-6 overflow-hidden"
              onClick={(e) => e.stopPropagation()}
            >
              {/* Close Button */}
              <button
                onClick={() => setActiveMedia(null)}
                className="absolute top-4 right-4 z-10 p-2 bg-black/50 hover:bg-red-500 text-white rounded-full transition-colors backdrop-blur-md"
              >
                <svg
                  className="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>

              {activeMedia.type === "video" ? (
                <div className="aspect-video w-full rounded-xl overflow-hidden bg-black mt-10 md:mt-0">
                  <video
                    className="w-full h-full object-cover"
                    controls
                    autoPlay
                    src={activeMedia.url}
                  />
                </div>
              ) : (
                <div className="p-8 py-16 flex flex-col items-center justify-center bg-white/5 rounded-xl border border-white/5 mt-10 md:mt-0">
                  <div className="w-20 h-20 bg-[#FF8A00]/20 rounded-full flex items-center justify-center mb-8 animate-pulse">
                    <svg
                      className="w-10 h-10 text-[#FF8A00]"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" />
                    </svg>
                  </div>
                  <h3 className="text-white text-xl font-bold mb-6">
                    {t("voiceTestimonial")}
                  </h3>
                  <audio
                    className="w-full max-w-md outline-none custom-audio-player"
                    controls
                    autoPlay
                    src={activeMedia.url}
                  />
                </div>
              )}
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </section>
  );
};

export default Testimonial;
