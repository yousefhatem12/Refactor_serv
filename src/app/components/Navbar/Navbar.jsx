"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { usePathname } from "next/navigation";
import Link from "next/link";
import { useLanguage } from "../../context/LanguageContext";
import { useSettings } from "../../context/SettingsContext";
import { fetchAPI } from "@/lib/api";

const Navbar = () => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [mobileServicesOpen, setMobileServicesOpen] = useState(false);

  const { toggleLanguage, t, language } = useLanguage();
  const settings = useSettings();
  const [navLinks, setNavLinks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeHash, setActiveHash] = useState("");

  const pathname = usePathname();

  useEffect(() => {
    const fetchLinks = async () => {
      try {
        const data = await fetchAPI(`/nav-links?lang=${language}`);
        if (data && Array.isArray(data) && data.length > 0) {
          setNavLinks(data);
        } else {
          // Use fallback directly instead of throwing an error
          setNavLinks([
            { name: t("home"), href: "/Home" },
            { name: t("aboutUs"), href: "/about-us" },
            {
              name: t("services"),
              href: "#",
              dropdown: [
                { name: t("webDevelopment"), href: "/services-web-dev" },
                { name: t("webDesign"), href: "/services-web-design" },
                { name: t("socialMedia"), href: "/services-social-media" },
                { name: t("webHosting"), href: "/services-web-hosting" },
              ],
            },
            { name: t("portfolio"), href: "/portfolio" },
            { name: t("blog"), href: "/blog" },
            { name: t("faq"), href: "/faq" },
            { name: t("contactUs"), href: "/contact-us" },
          ]);
        }
      } catch (error) {
        console.warn("Failed to fetch nav links, using fallback:", error);
        setNavLinks([
          { name: t("home"), href: "/Home" },
          { name: t("aboutUs"), href: "/about-us" },
          {
            name: t("services"),
            href: "#",
            dropdown: [
              { name: t("webDevelopment"), href: "/services-web-dev" },
              { name: t("webDesign"), href: "/services-web-design" },
              { name: t("socialMedia"), href: "/services-social-media" },
              { name: t("webHosting"), href: "/services-web-hosting" },
            ],
          },
          { name: t("portfolio"), href: "/portfolio" },
          { name: t("blog"), href: "/blog" },
          { name: t("faq"), href: "/faq" },
          { name: t("contactUs"), href: "/contact-us" },
        ]);
      } finally {
        setLoading(false);
      }
    };

    fetchLinks();
  }, [t, language]);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);

      // ScrollSpy Logic
      let currentHash = "";
      // Only look for hashes on the current page
      const pageHashLinks = navLinks
        .filter(link => link.href.startsWith(pathname + '#'))
        .map(link => link.href.split('#')[1]);

      for (const sectionId of pageHashLinks) {
        const element = document.getElementById(sectionId);
        if (element) {
          const rect = element.getBoundingClientRect();
          // Adjust threshold: if the section is occupying the middle of the screen
          if (rect.top <= window.innerHeight / 2 && rect.bottom >= 100) {
            currentHash = `#${sectionId}`;
            break; // found the active one
          }
        }
      }

      // If user scrolls to the absolute top, clear the hash
      if (window.scrollY < 100) {
        currentHash = "";
      }

      setActiveHash(currentHash);
    };
    
    window.addEventListener("scroll", handleScroll);
    // Initial check
    handleScroll();
    
    return () => window.removeEventListener("scroll", handleScroll);
  }, [pathname, navLinks]);

  return (
    <nav
      className={`fixed top-0 left-0 w-full z-[1000] transition-all duration-500 ease-in-out ${isScrolled
          ? "py-4 bg-[#05050f]/5 backdrop-blur-md shadow-lg "
          : "py-6 bg-transparent"
        }`}
    >
      <div className="max-w-[1400px] mx-auto px-6 md:px-10 flex items-center justify-between">
        {/* Logo */}
        <div className="flex-shrink-0">
          <Link href="/Home">
            <img
              src={settings?.site_logo || "/logo.svg"}
              alt="Serv5 Logo"
              className="h-12 md:h-16 w-auto object-contain transition-transform duration-300 hover:scale-105 cursor-pointer"
            />
          </Link>
        </div>

        {/* Desktop Links */}
        <div className="hidden lg:flex items-center gap-8">
          {navLinks.map((link) => {
            const isHashLink = link.href.includes('#');
            const targetPath = link.href.split('#')[0];
            const targetHash = '#' + (link.href.split('#')[1] || '');

            let isActive = false;

            if (link.dropdown && link.dropdown.some((sub) => pathname === sub.href)) {
              isActive = true;
            } else if (isHashLink) {
              isActive = pathname === targetPath && activeHash === targetHash;
            } else {
              // It's a normal link. Only active if pathname matches AND we aren't currently in a known hash section.
              // Alternatively, if it's the exact pathname and activeHash is empty.
              const hasActiveHash = activeHash !== "";
              isActive = (pathname === link.href && !hasActiveHash);
            }

            return (
              <div key={link.name} className="relative group">
                <Link
                  href={link.href}
                  className={`flex items-center gap-1 text-sm font-medium tracking-wider transition-all duration-300 ${isActive
                      ? "text-[#D98B50]"
                      : "text-white/70 hover:text-white"
                    }`}
                >
                  {link.name}
                  {link.dropdown && (
                    <svg
                      className="w-4 h-4 transition-transform duration-300 group-hover:rotate-180"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M19 9l-7 7-7-7"
                      />
                    </svg>
                  )}
                  <span
                    className={`absolute -bottom-1 left-0 h-[2px] bg-[#D98B50] rounded-full transition-all duration-300 ${isActive ? "w-full" : "w-0 group-hover:w-full"
                      }`}
                  />
                </Link>

                {/* Desktop Dropdown */}
                {link.dropdown && (
                  <div className="absolute top-full left-0 pt-6 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                    <div className="bg-[#05050f]/95 backdrop-blur-md border border-white/10 rounded-lg p-3 min-w-[220px] shadow-2xl flex flex-col gap-1">
                      {link.dropdown.map((subLink) => (
                        <Link
                          key={subLink.name}
                          href={subLink.href}
                          className={`block px-4 py-3 text-sm rounded-md transition-all duration-200 ${pathname === subLink.href
                              ? "text-[#D98B50] bg-white/10 font-semibold"
                              : "text-white/70 hover:text-white hover:bg-white/5 hover:translate-x-1"
                            }`}
                        >
                          {subLink.name}
                        </Link>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            );
          })}
        </div>

        {/* Actions (Globe & Menu) */}
        <div className="flex items-center gap-3 md:gap-5">
          <button
            onClick={toggleLanguage}
            className="text-white p-2 rounded-full hover:bg-white/10 transition-all duration-300 hover:-translate-y-0.5 cursor-pointer border-none bg-transparent"
            aria-label="Toggle Language"
          >
            <svg
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="2" y1="12" x2="22" y2="12" />
              <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
            </svg>
          </button>

          <button
            className="lg:hidden text-white p-2 rounded-full hover:bg-white/10 transition-all duration-300 cursor-pointer border-none bg-transparent"
            aria-label="Toggle Menu"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            <svg
              width="28"
              height="18"
              viewBox="0 0 30 20"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <rect width="30" height="2" rx="1" fill="currentColor" />
              <rect y="9" width="20" height="2" rx="1" fill="currentColor" />
              <rect y="18" width="30" height="2" rx="1" fill="currentColor" />
            </svg>
          </button>
        </div>
      </div>

      {/* Mobile Menu Overlay */}
      <AnimatePresence>
        {isMobileMenuOpen && (
          <>
            {/* Backdrop */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setIsMobileMenuOpen(false)}
              className="fixed inset-0 bg-black/60 backdrop-blur-sm z-[1001] lg:hidden"
            />

            {/* Menu Panel */}
            <motion.div
              initial={{ x: "100%" }}
              animate={{ x: 0 }}
              exit={{ x: "100%" }}
              transition={{ type: "spring", damping: 25, stiffness: 200 }}
              className="fixed top-0 right-0 w-[300px] h-screen bg-[#05050f] border-l border-white/10 z-[1002] p-8 shadow-2xl flex flex-col lg:hidden overflow-y-auto"
            >
              <div className="flex justify-end mb-10">
                <button
                  onClick={() => setIsMobileMenuOpen(false)}
                  className="text-white hover:text-[#D98B50] transition-colors cursor-pointer border-none bg-transparent"
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
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>
              <div className="flex flex-col gap-6">
                {navLinks.map((link) => {
                  const isHashLink = link.href.includes('#');
                  const targetPath = link.href.split('#')[0];
                  const targetHash = '#' + (link.href.split('#')[1] || '');

                  let isActive = false;
                  if (link.dropdown && link.dropdown.some((sub) => pathname === sub.href)) {
                    isActive = true;
                  } else if (isHashLink) {
                    isActive = pathname === targetPath && activeHash === targetHash;
                  } else {
                    const hasActiveHash = activeHash !== "";
                    isActive = (pathname === link.href && !hasActiveHash);
                  }

                  return (
                    <div key={link.name} className="flex flex-col gap-4">
                      <div
                        className="flex items-center justify-between cursor-pointer"
                        onClick={() => {
                          if (link.dropdown) {
                            setMobileServicesOpen(!mobileServicesOpen);
                          } else {
                            setIsMobileMenuOpen(false);
                          }
                        }}
                      >
                        <Link
                          href={link.dropdown ? "#" : link.href}
                          className={`text-xl font-semibold tracking-wide transition-colors ${isActive
                              ? "text-[#D98B50]"
                              : "text-white/80 hover:text-white"
                            }`}
                          onClick={(e) => {
                            if (link.dropdown) e.preventDefault();
                          }}
                        >
                          {link.name}
                        </Link>
                        {link.dropdown && (
                          <button className="text-white/80 hover:text-white transition-colors cursor-pointer bg-transparent border-none p-1">
                            <svg
                              className={`w-6 h-6 transition-transform duration-300 ${mobileServicesOpen ? "rotate-180" : ""}`}
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                            >
                              <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M19 9l-7 7-7-7"
                              />
                            </svg>
                          </button>
                        )}
                      </div>

                      {/* Mobile Dropdown */}
                      {link.dropdown && (
                        <AnimatePresence>
                          {mobileServicesOpen && (
                            <motion.div
                              initial={{ height: 0, opacity: 0 }}
                              animate={{ height: "auto", opacity: 1 }}
                              exit={{ height: 0, opacity: 0 }}
                              className="flex flex-col gap-4 pl-4 overflow-hidden border-l-2 border-white/10 ml-2"
                            >
                              {link.dropdown.map((subLink) => (
                                <Link
                                  key={subLink.name}
                                  href={subLink.href}
                                  className={`text-lg py-1 transition-colors ${pathname === subLink.href
                                      ? "text-[#D98B50] font-medium"
                                      : "text-white/60 hover:text-white"
                                    }`}
                                  onClick={() => setIsMobileMenuOpen(false)}
                                >
                                  {subLink.name}
                                </Link>
                              ))}
                            </motion.div>
                          )}
                        </AnimatePresence>
                      )}
                    </div>
                  );
                })}
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </nav>
  );
};

export default Navbar;
