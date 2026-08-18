"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { usePathname } from "next/navigation";
import Link from "next/link";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import { safeUrl } from "@/lib/safe-url";

const getDropdownGridClass = (dropdown) => {
  if (!dropdown) return "min-w-[220px] flex flex-col gap-1";
  const count = dropdown.length;
  if (count <= 5) {
    return "min-w-[220px] flex flex-col gap-1";
  }
  const cols = Math.min(Math.ceil(count / 5), 5);
  if (cols === 2) return "min-w-[480px] grid grid-cols-2 gap-2";
  if (cols === 3) return "min-w-[720px] grid grid-cols-3 gap-2";
  if (cols === 4) return "min-w-[960px] grid grid-cols-4 gap-2";
  return "min-w-[1200px] grid grid-cols-5 gap-2";
};

const Navbar = ({ initialServices = [], initialNavLinks = [] }) => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isHidden, setIsHidden] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [openMobileDropdown, setOpenMobileDropdown] = useState(null);

  const { toggleLanguage, t, language } = useLanguage();
  const settings = useSettings();
  const [activeHash, setActiveHash] = useState("");
  const [windowWidth, setWindowWidth] = useState(1400);

  const pathname = usePathname();

  const getLocalizedHref = (href) => {
    const safeHref = safeUrl(href);
    if (safeHref === "#" || safeHref.startsWith("http") || safeHref.startsWith("tel:") || safeHref.startsWith("mailto:")) return safeHref;
    const cleanHref = safeHref.startsWith("/") ? safeHref : `/${safeHref}`;
    const cleanNoLocale = cleanHref.replace(/^\/(ar|en)/, "");
    if (cleanNoLocale === "" || cleanNoLocale === "/" || cleanNoLocale.toLowerCase() === "/home") {
      return `/${language}`;
    }
    if (cleanNoLocale.startsWith("/#")) {
      return `/${language}${cleanNoLocale.substring(1)}`;
    }
    return `/${language}${cleanNoLocale}`;
  };

  const localizeLinks = (links) => {
    if (!links) return [];
    return links.map(link => {
      const rawName = link.name;
      const cleanName = (typeof rawName === "object" && rawName !== null)
        ? (rawName[language] || rawName.en || "")
        : (rawName || "");

      return {
        ...link,
        name: cleanName,
        href: getLocalizedHref(link.href),
        dropdown: link.dropdown ? link.dropdown.map(sub => {
          const rawSubName = sub.name;
          const cleanSubName = (typeof rawSubName === "object" && rawSubName !== null)
            ? (rawSubName[language] || rawSubName.en || "")
            : (rawSubName || "");
          return {
            ...sub,
            name: cleanSubName,
            href: getLocalizedHref(sub.href)
          };
        }) : undefined
      };
    });
  };

  useEffect(() => {
    const handleResize = () => {
      setWindowWidth(window.innerWidth);
    };
    const initialResizeFrame = window.requestAnimationFrame(handleResize);
    window.addEventListener("resize", handleResize);
    return () => {
      window.cancelAnimationFrame(initialResizeFrame);
      window.removeEventListener("resize", handleResize);
    };
  }, []);

  const getDefaultLinks = (t) => [
    { name: t("home") || "Home", href: "/" },
    {
      name: t("aboutUs") || "About Us",
      href: "#",
      dropdown: [
        { name: t("aboutUs") || "About Us", href: "/about-us" },
        { name: t("bankAccounts") || "Bank Accounts", href: "/bank-accounts" },
        { name: t("careers") || "Careers", href: "/careers" },
        { name: t("faq") || "FAQ", href: "/faq" },
      ],
    },
    {
      name: t("services") || "Services",
      href: "#",
      dropdown: [
        {
          name: t("webDevelopment") || "Web Development",
          href: "/services/services-web-development",
        },
        {
          name: t("webDesign") || "Web Design",
          href: "/services/services-ui-ux-design",
        },
        {
          name: t("socialMedia") || "Social Media",
          href: "/services/services-social-media",
        },
        {
          name: t("webHosting") || "Web Hosting",
          href: "/services/services-cloud-hosting",
        },
        {
          name: t("catOnlineStores") || "Online Stores",
          href: "/services/services-ecommerce",
        },
        {
          name: t("catMobileApps") || "Mobile Apps",
          href: "/services/services-mobile-apps",
        },
        {
          name: t("consultationHeroTitle") || "Consultation",
          href: "/project-consultation",
        },
        { name: t("requestQuote") || "Request Quote", href: "/request-quote" },
      ],
    },
    { name: t("portfolio") || "Portfolio", href: "/portfolio" },
    { name: t("blog") || "Blog", href: "/blog" },
    { name: t("ourProducts") || "Our Products", href: "/our-products" },
    { name: t("contactUs") || "Contact Us", href: "/contact-us" },
  ];

  const buildLinks = (dropdown) => [
    { name: t("home"), href: "/" },
    {
      name: t("aboutUs"),
      href: "#",
      dropdown: [
        { name: t("aboutUs"), href: "/about-us" },
        { name: t("bankAccounts"), href: "/bank-accounts" },
        { name: t("careers"), href: "/careers" },
        { name: t("faq"), href: "/faq" },
      ],
    },
    {
      name: t("services"),
      href: "#",
      dropdown: [
        ...dropdown,
        {
          name: t("consultationHeroTitle"),
          href: "/project-consultation",
        },
        { name: t("requestQuote"), href: "/request-quote" },
      ],
    },
    { name: t("portfolio"), href: "/portfolio" },
    { name: t("blog"), href: "/blog" },
    { name: t("ourProducts"), href: "/our-products" },
    { name: t("contactUs"), href: "/contact-us" },
  ];

  const serviceDropdown =
    initialServices && initialServices.length > 0
      ? initialServices.map((s) => ({
          name: s.label || s.title,
          href: `/services/services-${s.id}`,
        }))
      : [
          {
            name: t("webDevelopment") || "Web Development",
            href: "/services/services-web-development",
          },
          {
            name: t("webDesign") || "Web Design",
            href: "/services/services-ui-ux-design",
          },
          {
            name: t("socialMedia") || "Social Media",
            href: "/services/services-social-media",
          },
          {
            name: t("webHosting") || "Web Hosting",
            href: "/services/services-cloud-hosting",
          },
        ];

  let computedNavLinks = [];
  if (initialNavLinks && initialNavLinks.length > 0) {
    const enriched = initialNavLinks.map((link) => {
      if (link.dropdown && link.name === t("services")) {
        return {
          ...link,
          dropdown: [
            ...serviceDropdown,
            {
              name: t("consultationHeroTitle"),
              href: "/project-consultation",
            },
            { name: t("requestQuote"), href: "/request-quote" },
          ],
        };
      }
      if (link.href === "/about-us" || link.name === t("aboutUs")) {
        return {
          name: t("aboutUs"),
          href: "#",
          dropdown: [
            { name: t("aboutUs") || "About Us", href: "/about-us" },
            {
              name: t("bankAccounts") || "Bank Accounts",
              href: "/bank-accounts",
            },
            ...(link.dropdown || []),
          ],
        };
      }
      return link;
    });
    computedNavLinks = localizeLinks(enriched);
  } else {
    computedNavLinks = localizeLinks(buildLinks(serviceDropdown));
  }

  const activeLinks = computedNavLinks;

  const isUnder1300 = windowWidth < 1300;
  const mainLinks = isUnder1300 ? activeLinks.slice(0, 5) : activeLinks;
  const dropdownLinks = isUnder1300 ? activeLinks.slice(5) : [];

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);

      if (document.body.scrollHeight > window.innerHeight + 100) {
        const footer = document.querySelector("footer");
        if (footer) {
          const rect = footer.getBoundingClientRect();
          if (rect.top < window.innerHeight - 50) {
            setIsHidden(true);
          } else {
            setIsHidden(false);
          }
        }
      } else {
        setIsHidden(false);
      }

      // ScrollSpy Logic
      let currentHash = "";
      // Only look for hashes on the current page
      const pageHashLinks = activeLinks
        .filter((link) => link.href.startsWith(pathname + "#"))
        .map((link) => link.href.split("#")[1]);

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
  }, [pathname, activeLinks]);

  const isRtl = language === "ar";
  const isHomePageHero =
    (pathname === "/" || pathname === "/ar" || pathname === "/en") && !isScrolled;

  return (
    <nav
      className={`fixed top-0 left-0 w-full z-[1000] transition-all duration-500 ease-in-out ${
        isScrolled
          ? "py-4 bg-[#05050f]/5 backdrop-blur-md shadow-lg "
          : isHomePageHero
            ? "py-4 lg:py-5 bg-transparent"
            : "py-6 bg-transparent"
      } ${isHidden ? "-translate-y-full opacity-0 pointer-events-none" : "translate-y-0 opacity-100"}`}
    >
      <div className="max-w-[1400px] mx-auto px-6 md:px-10 flex items-center justify-between">
        {/* Logo */}
        <div className="flex-shrink-0">
          <Link href={getLocalizedHref("/")} prefetch={false}>
            <img
              src={settings?.site_logo ? getMediaUrl(settings.site_logo) : "/logo.svg"}
              alt="Serv5 Logo"
              className="h-12 md:h-16 w-auto object-contain transition-transform duration-300 hover:scale-105 cursor-pointer"
            />
          </Link>
        </div>

        {/* Desktop Links */}
        <div className="hidden lg:flex items-center gap-4 xl:gap-8">
          {mainLinks.map((link) => {
            const isHashLink = link.href.includes("#");
            const targetPath = link.href.split("#")[0];
            const targetHash = "#" + (link.href.split("#")[1] || "");

            let isActive = false;

            if (
              link.dropdown &&
              link.dropdown.some((sub) => pathname === sub.href)
            ) {
              isActive = true;
            } else if (isHashLink) {
              isActive = pathname === targetPath && activeHash === targetHash;
            } else {
              // It's a normal link. Only active if pathname matches AND we aren't currently in a known hash section.
              // Alternatively, if it's the exact pathname and activeHash is empty.
              const hasActiveHash = activeHash !== "";
              isActive = pathname === link.href && !hasActiveHash;
            }

            return (
              <div key={link.name} className="relative group">
                <Link
                  href={link.href}
                  prefetch={false}
                  className={`flex items-center gap-1 text-xs md:text-sm font-semibold tracking-[0.1em] uppercase transition-all duration-300 ${
                    isActive
                      ? "text-[#FF8A00]"
                      : "text-white/60 hover:text-white"
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
                    className={`absolute -bottom-1 ${isRtl ? "right-0" : "left-0"} h-[1px] bg-[#FF8A00] rounded-full transition-all duration-300 ${
                      isActive ? "w-full" : "w-0 group-hover:w-full"
                    }`}
                  />
                </Link>

                {/* Desktop Dropdown */}
                {link.dropdown && (
                  <div
                    className={`absolute top-full ${isRtl ? "right-0" : "left-0"} pt-6 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50`}
                  >
                    <div
                      className={`bg-[#0b0f2c]/95 backdrop-blur-md border border-white/10 rounded-xl p-4 shadow-2xl ${getDropdownGridClass(link.dropdown)}`}
                    >
                      {link.dropdown.map((subLink) => (
                        <Link
                          key={subLink.name}
                          href={subLink.href}
                          prefetch={false}
                          className={`block px-4 py-3 text-xs md:text-sm font-medium tracking-wide rounded-lg transition-all duration-200 ${
                            pathname === subLink.href
                              ? "text-[#FF8A00] bg-white/5 font-bold"
                              : `text-white/50 hover:text-white hover:bg-white/5 ${isRtl ? "hover:-translate-x-1" : "hover:translate-x-1"}`
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

          {/* More Dropdown */}
          {dropdownLinks.length > 0 && (
            <div className="relative group">
              <button
                className={`flex items-center gap-1 text-xs md:text-sm font-semibold tracking-[0.1em] uppercase transition-all duration-300 border-none bg-transparent cursor-pointer relative ${
                  dropdownLinks.some((link) => {
                    const isHashLink = link.href.includes("#");
                    const targetPath = link.href.split("#")[0];
                    const targetHash = "#" + (link.href.split("#")[1] || "");
                    if (
                      link.dropdown &&
                      link.dropdown.some((sub) => pathname === sub.href)
                    )
                      return true;
                    if (isHashLink)
                      return (
                        pathname === targetPath && activeHash === targetHash
                      );
                    const hasActiveHash = activeHash !== "";
                    return pathname === link.href && !hasActiveHash;
                  })
                    ? "text-[#FF8A00]"
                    : "text-white/60 hover:text-white"
                }`}
              >
                {t("more") || (isRtl ? "المزيد" : "More")}
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
                <span
                  className={`absolute -bottom-1 ${isRtl ? "right-0" : "left-0"} h-[1px] bg-[#FF8A00] rounded-full transition-all duration-300 ${
                    dropdownLinks.some((link) => {
                      const isHashLink = link.href.includes("#");
                      const targetPath = link.href.split("#")[0];
                      const targetHash = "#" + (link.href.split("#")[1] || "");
                      if (
                        link.dropdown &&
                        link.dropdown.some((sub) => pathname === sub.href)
                      )
                        return true;
                      if (isHashLink)
                        return (
                          pathname === targetPath && activeHash === targetHash
                        );
                      const hasActiveHash = activeHash !== "";
                      return pathname === link.href && !hasActiveHash;
                    })
                      ? "w-full"
                      : "w-0 group-hover:w-full"
                  }`}
                />
              </button>

              {/* More Dropdown Content */}
              <div
                className={`absolute top-full ${isRtl ? "left-0" : "right-0"} pt-6 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50`}
              >
                <div className="bg-[#0b0f2c]/95 backdrop-blur-md border border-white/10 rounded-xl p-4 shadow-2xl min-w-[220px] flex flex-col gap-1">
                  {dropdownLinks.map((link) => {
                    const isHashLink = link.href.includes("#");
                    const targetPath = link.href.split("#")[0];
                    const targetHash = "#" + (link.href.split("#")[1] || "");

                    let isActive = false;

                    if (
                      link.dropdown &&
                      link.dropdown.some((sub) => pathname === sub.href)
                    ) {
                      isActive = true;
                    } else if (isHashLink) {
                      isActive =
                        pathname === targetPath && activeHash === targetHash;
                    } else {
                      const hasActiveHash = activeHash !== "";
                      isActive = pathname === link.href && !hasActiveHash;
                    }

                    return (
                      <Link
                        key={link.name}
                        href={link.href}
                        prefetch={false}
                        className={`block px-4 py-3 text-xs md:text-sm font-medium tracking-wide rounded-lg transition-all duration-200 ${
                          isActive
                            ? "text-[#FF8A00] bg-white/5 font-bold"
                            : `text-white/50 hover:text-white hover:bg-white/5 ${isRtl ? "hover:-translate-x-1" : "hover:translate-x-1"}`
                        }`}
                      >
                        {link.name}
                      </Link>
                    );
                  })}
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Actions (Globe & Menu) */}
        <div className="flex items-center gap-3 md:gap-5">
          <button
            onClick={toggleLanguage}
            className="flex items-center gap-1.5 text-white/90 hover:text-white px-3 py-1.5 rounded-full hover:bg-white/10 transition-all duration-300 hover:-translate-y-0.5 cursor-pointer border border-white/10 bg-white/5 text-xs font-semibold"
            aria-label="Toggle Language"
          >
            <svg
              width="16"
              height="16"
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
            <span className="font-medium tracking-wide">
              {language === "ar" ? "English" : "العربية"}
            </span>
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
              initial={{ x: isRtl ? "-100%" : "100%" }}
              animate={{ x: 0 }}
              exit={{ x: isRtl ? "-100%" : "100%" }}
              transition={{ type: "spring", damping: 25, stiffness: 200 }}
              className={`fixed top-0 ${isRtl ? "left-0 border-r" : "right-0 border-l"} w-[300px] h-screen bg-[#05050f] border-white/10 z-[1002] p-8 shadow-2xl flex flex-col lg:hidden overflow-y-auto`}
            >
              <div
                className={`flex ${isRtl ? "justify-start" : "justify-end"} mb-10`}
              >
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
                {activeLinks.map((link) => {
                  const isHashLink = link.href.includes("#");
                  const targetPath = link.href.split("#")[0];
                  const targetHash = "#" + (link.href.split("#")[1] || "");

                  let isActive = false;
                  if (
                    link.dropdown &&
                    link.dropdown.some((sub) => pathname === sub.href)
                  ) {
                    isActive = true;
                  } else if (isHashLink) {
                    isActive =
                      pathname === targetPath && activeHash === targetHash;
                  } else {
                    const hasActiveHash = activeHash !== "";
                    isActive = pathname === link.href && !hasActiveHash;
                  }

                  return (
                    <div key={link.name} className="flex flex-col gap-4">
                      <div
                        className="flex items-center justify-between cursor-pointer"
                        onClick={() => {
                          if (link.dropdown) {
                            setOpenMobileDropdown(
                              openMobileDropdown === link.name
                                ? null
                                : link.name,
                            );
                          } else {
                            setIsMobileMenuOpen(false);
                          }
                        }}
                      >
                        <Link
                          href={link.dropdown ? "#" : link.href}
                          prefetch={false}
                          className={`text-xl font-semibold tracking-wide transition-colors ${
                            isActive
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
                              className={`w-6 h-6 transition-transform duration-300 ${openMobileDropdown === link.name ? "rotate-180" : ""}`}
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
                          {openMobileDropdown === link.name && (
                            <motion.div
                              initial={{ height: 0, opacity: 0 }}
                              animate={{ height: "auto", opacity: 1 }}
                              exit={{ height: 0, opacity: 0 }}
                              className={`flex flex-col gap-4 ${isRtl ? "pr-4 border-r-2 mr-2" : "pl-4 border-l-2 ml-2"} overflow-hidden border-white/10`}
                            >
                              {link.dropdown.map((subLink) => (
                                <Link
                                  key={subLink.name}
                                  href={subLink.href}
                                  prefetch={false}
                                  className={`text-lg py-1 transition-colors ${
                                    pathname === subLink.href
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
