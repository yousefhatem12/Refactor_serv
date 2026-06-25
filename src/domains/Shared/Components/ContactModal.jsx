"use client";
import React, { useState, useEffect } from "react";
import { createPortal } from "react-dom";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { fetchAPI } from "@/lib/api";
import {
  FaFacebook,
  FaInstagram,
  FaLinkedin,
  FaYoutube,
  FaTiktok,
} from "react-icons/fa";
import { RiTwitterXFill } from "react-icons/ri";

const getSocialIconData = (platform) => {
  const map = {
    'facebook': { color: "bg-[#1877F2]", svg: <FaFacebook /> },
    'twitter': { color: "bg-[#1DA1F2]", svg: <RiTwitterXFill /> },
    'instagram': { color: "bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600", svg: <FaInstagram /> },
    'linkedin': { color: "bg-[#0077B5]", svg: <FaLinkedin /> },
    'youtube': { color: "bg-[#FF0000]", svg: <FaYoutube /> },
    'tiktok': { color: "bg-black", svg: <FaTiktok /> },
  };
  return map[platform] || map['facebook'];
};

const ContactModal = ({ isOpen, onClose, settings }) => {
  const { t, language } = useLanguage();
  const isRtl = language === "ar";
  
  const [socials, setSocials] = useState([]);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    if (isOpen) {
      fetchAPI('/footer-data').then(data => {
        if (data && data.socials) {
          setSocials(data.socials);
        }
      }).catch(console.error);
    }
  }, [isOpen]);

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
            className="relative w-full max-w-2xl bg-[#0a0a0a] rounded-2xl shadow-[0_0_50px_rgba(217,139,80,0.2)] border border-white/10 z-10"
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

            {/* Content Container */}
            <div className="p-8 md:p-12 text-center">
              <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">
                {t("contactInfo") || (isRtl ? "تواصل معنا" : "Contact Us")}
              </h2>
              <p className="text-gray-400 text-lg max-w-md mx-auto mb-10">
                {isRtl 
                  ? "نحن هنا لمساعدتك! يمكنك التواصل معنا عبر الوسائل التالية:" 
                  : "We're here to help! You can reach us through the following channels:"}
              </p>

              <ul className="space-y-6 text-left inline-block w-full max-w-md">
                <li className="flex items-center gap-4 group bg-white/5 p-4 rounded-xl border border-white/10 hover:border-white/30 transition-all">
                  <div className="text-orange-500 p-3 rounded-lg bg-orange-500/10 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                  </div>
                  <span className="text-gray-200 text-lg font-medium group-hover:text-white transition-colors" dir="ltr">
                    {settings?.site_phone || "00201288853531"}
                  </span>
                </li>
                <li className="flex items-center gap-4 group bg-white/5 p-4 rounded-xl border border-white/10 hover:border-white/30 transition-all">
                  <div className="text-orange-500 p-3 rounded-lg bg-orange-500/10 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                  </div>
                  <span className="text-gray-200 text-lg font-medium group-hover:text-white transition-colors break-all">
                    {settings?.site_email || "sales@serv5.com"}
                  </span>
                </li>
                <li className="flex items-center gap-4 group bg-white/5 p-4 rounded-xl border border-white/10 hover:border-white/30 transition-all">
                  <div className="text-orange-500 p-3 rounded-lg bg-orange-500/10 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                  </div>
                  <span className="text-gray-200 text-lg font-medium group-hover:text-white transition-colors whitespace-pre-line">
                    {settings?.site_address || t("address")}
                  </span>
                </li>
              </ul>

              {socials.length > 0 && (
                <div className="mt-10 pt-8 border-t border-white/10">
                  <p className="text-gray-400 font-medium mb-4">
                    {isRtl ? "تابعنا على منصات التواصل:" : "Follow us on social media:"}
                  </p>
                  <div className="flex flex-wrap justify-center gap-4">
                    {socials.map((social) => {
                      const iconData = getSocialIconData(social.platform);
                      return (
                        <motion.a
                          key={social.id}
                          href={social.url}
                          target="_blank"
                          rel="noopener noreferrer"
                          whileHover={{ y: -5, scale: 1.1 }}
                          className={`${iconData.color} w-12 h-12 rounded-full flex items-center justify-center text-white shadow-[0_5px_15px_rgba(0,0,0,0.3)] transition-all`}
                        >
                          {iconData.svg}
                        </motion.a>
                      );
                    })}
                  </div>
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

export default ContactModal;
