"use client";
import React, { useState, useEffect } from "react";
import { usePathname } from "next/navigation";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI } from "@/lib/api";
import { motion } from "framer-motion";
import PlanetAstronaut from "../Home/Shared/PlanetAstronaut";
import {
  FaFacebook,
  FaInstagram,
  FaLinkedin,
  FaYoutube,
  FaTiktok,
} from "react-icons/fa";
import { RiTwitterXFill } from "react-icons/ri";
// Inline SVGs are used instead of lucide-react to avoid dependency issues.

// Note: Since lucide-react is not in package.json, I will use inline SVGs
// for the icons to match the design perfectly and avoid dependency issues.

const Footer = () => {
  const { t } = useLanguage();
  const pathname = usePathname();
  const isHomePage = pathname === "/" || pathname === "/Home";

  const [settings, setSettings] = useState({});
  const [footerData, setFooterData] = useState({
    texts: {},
    links: { usefulLinks: [], quickLinks: [], offers: [], company: [] },
    socials: []
  });

  useEffect(() => {
    fetchAPI('/settings').then(data => {
      if (data) setSettings(data);
    }).catch(console.error);

    fetchAPI('/footer-data').then(data => {
      if (data) setFooterData(data);
    }).catch(console.error);
  }, []);

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

  return (
    <footer className="bg-transparent text-white pt-10 pb-6 relative overflow-hidden">
      {/* Background Glows */}
      <div className="absolute bottom-0 right-0 w-1/2 h-1/2 bg-blue-600/10 blur-[150px] -z-10 rounded-full"></div>
      <div className="absolute top-0 left-0 w-1/3 h-1/3 bg-orange-500/5 blur-[120px] -z-10 rounded-full"></div>

      {/* Decorative Element */}
      <img 
        src="/Element.png" 
        alt="Element" 
        className="absolute top-0 -right-[150px] mix-blend-luminosity opacity-50 w-48 md:w-[500px]  object-contain pointer-events-none z-0 " 
      />

      <div className=" mx-auto px-6 lg:px-20 relative z-10">
        {/* Top Section: Heading & Astronaut */}
        <div className="flex relative flex-col md:flex-row items-center justify-between  relative">
          <div className="max-w-2xl text-center  md:text-left z-20">
            <motion.h2
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="text-4xl md:text-7xl font-bold mb-4 md:mb-6 leading-[1.1] tracking-tight"
            >
              <span dangerouslySetInnerHTML={{ __html: (footerData.texts?.heading || t("haveAGoodWebDesign")).replace('\n', '<br className="hidden md:block" />') }} />
            </motion.h2>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.1 }}
              className="text-gray-400 text-lg md:text-xl mb-6 max-w-xl font-light"
            >
              {footerData.texts?.description || t("footerDesc")}
            </motion.p>
            <motion.button
              initial={{ opacity: 0, scale: 0.8 }}
              whileInView={{ opacity: 1, scale: 1 }}
              viewport={{ once: true }}
              transition={{ delay: 0.2 }}
              whileHover={{
                scale: 1.05,
                boxShadow: "0 0 20px rgba(255,255,255,0.2)",
              }}
              whileTap={{ scale: 0.95 }}
              className="px-12 py-4 rounded-full border border-white/30 bg-white/5 backdrop-blur-md text-white font-semibold hover:bg-white hover:text-[#020617] transition-all duration-300 text-lg"
            >
              {footerData.texts?.button_text || t("getStarted")}
            </motion.button>
          </div>

          {/* Space reserved for global planet transition */}
          <div className="absolute top-[-10%] right-[-10%] w-full md:w-1/2 flex justify-center md:justify-end pr-10 pointer-events-none invisible lg:visible">
            <div 
              className={`w-72 md:w-[500px] lg:w-[600px] aspect-square ${!isHomePage ? "opacity-[0.35]" : ""}`}
              style={!isHomePage ? { filter: "contrast(60%) brightness(60%)", transform: "rotate(10deg) translatey(8rem) translatex(15rem) " } : {}}
            >
              {!isHomePage && <PlanetAstronaut />}
            </div>
          </div>
        </div>

        {/* Middle Section: Links Grid */}
        <div className="relative grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-10 mb-10 pt-6">
          {/* Useful Links */}
          <div>
            <h4 className="text-xl font-bold mb-5 text-white">{t("usefulLinks")}</h4>
            <ul className="space-y-2">
              {footerData.links.usefulLinks.map((link) => (
                <li key={link.id}>
                  <a
                    href={link.url}
                    className="text-gray-400 hover:text-orange-500 transition-colors text-base font-medium"
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-xl font-bold mb-5 text-white">{t("quickLinks")}</h4>
            <ul className="space-y-2">
              {footerData.links.quickLinks.map((link) => (
                <li key={link.id}>
                  <a
                    href={link.url}
                    className="text-gray-400 hover:text-orange-500 transition-colors text-base font-medium"
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Offers */}
          <div>
            <h4 className="text-xl font-bold mb-5 text-white">{t("offers")}</h4>
            <ul className="space-y-2">
              {footerData.links.offers.map((link) => (
                <li key={link.id}>
                  <a
                    href={link.url}
                    className="text-gray-400 hover:text-orange-500 transition-colors text-base font-medium"
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Company */}
          <div>
            <h4 className="text-xl font-bold mb-5 text-white">{t("company")}</h4>
            <ul className="space-y-2">
              {footerData.links.company.map((link) => (
                <li key={link.id}>
                  <a
                    href={link.url}
                    className="text-gray-400 hover:text-orange-500 transition-colors text-base font-medium"
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h4 className="text-xl font-bold mb-5 text-white">{t("contactInfo")}</h4>
            <ul className="space-y-2">
              <li className="flex items-start gap-4 group">
                <div className="text-orange-500 p-2 rounded-lg bg-orange-500/10 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
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
                <span className="text-gray-400 text-base font-medium group-hover:text-white transition-colors pt-1">
                  {settings.site_phone || "00201288853531"}
                </span>
              </li>
              <li className="flex items-start gap-4 group">
                <div className="text-orange-500 p-2 rounded-lg bg-orange-500/10 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
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
                <span className="text-gray-400 text-base font-medium group-hover:text-white transition-colors pt-1 break-all">
                  {settings.site_email || "sales@serv5.com"}
                </span>
              </li>
              <li className="flex items-start gap-4 group">
                <div className="text-orange-500 p-2 rounded-lg bg-orange-500/10 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
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
                <span className="text-gray-400 text-base font-medium group-hover:text-white transition-colors pt-1 whitespace-pre-line">
                  {settings.site_address || t("address")}
                </span>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom Section: Socials & Copyright */}
        <div className="pt-6 border-t-[3px] border-blue-500 flex flex-col md:flex-row items-center justify-between gap-8">
          <div className="flex items-center gap-6">
            <span className="text-gray-300 font-bold text-lg">{t("followUs")}</span>
            <div className="flex flex-wrap justify-center gap-4">
              {footerData.socials.map((social) => {
                const iconData = getSocialIconData(social.platform);
                return (
                  <motion.a
                    key={social.id}
                    href={social.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    whileHover={{ y: -5, scale: 1.1 }}
                    className={`${iconData.color} w-10 h-10 rounded-full flex items-center justify-center text-white shadow-[0_5px_15px_rgba(0,0,0,0.3)] transition-all`}
                  >
                    {iconData.svg}
                  </motion.a>
                );
              })}
            </div>
          </div>

          <div className="text-gray-400 text-base font-medium">
            {footerData.texts?.copyright || t("allRightsReserved")}
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
