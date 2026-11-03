"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useSettings } from "@/app/context/SettingsContext";
import Loading from "@/app/loading";
import { fetchAPI, getApiPage, getMediaUrl } from "@/lib/api";
import {
  FaCopy,
  FaCheck,
  FaWhatsapp,
  FaPhoneAlt
} from "react-icons/fa";

const BankAccountsContent = () => {
  const { t, language } = useLanguage();
  const settings = useSettings();
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : "/logo.svg";
  const [activeTab, setActiveTab] = useState("egypt"); // "egypt" or "ksa"
  const [copiedId, setCopiedId] = useState(null);
  const [methods, setMethods] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [loading, setLoading] = useState(true);
  const [isLoadingMore, setIsLoadingMore] = useState(false);

  const [prevTab, setPrevTab] = useState(activeTab);
  const [prevLang, setPrevLang] = useState(language);

  if (activeTab !== prevTab || language !== prevLang) {
    setPrevTab(activeTab);
    setPrevLang(language);
    setLoading(true);
    setCurrentPage(1);
  }

  useEffect(() => {
    fetchAPI(`/bank-accounts?lang=${language}&country=${activeTab}&page=1&per_page=6`)
      .then((res) => {
        const page = getApiPage(res);
        if (page.items.length > 0 || res) {
          setMethods(page.items);
          setHasMore(page.hasMore);
          setCurrentPage(page.currentPage);
        } else {
          setMethods([]);
          setHasMore(false);
        }
      })
      .catch((err) => {
        console.error("Error fetching bank accounts:", err);
        setMethods([]);
        setHasMore(false);
      })
      .finally(() => setLoading(false));
  }, [language, activeTab]);

  const loadMoreMethods = () => {
    if (isLoadingMore || !hasMore) return;
    setIsLoadingMore(true);
    const nextPage = currentPage + 1;
    fetchAPI(`/bank-accounts?lang=${language}&country=${activeTab}&page=${nextPage}&per_page=6`)
      .then((res) => {
        const page = getApiPage(res);
        if (page.items.length > 0 || res) {
          setMethods((prev) => {
            const newMethods = page.items.filter((item) => !prev.some((p) => p.id === item.id));
            return [...prev, ...newMethods];
          });
          setHasMore(page.hasMore);
          setCurrentPage(page.currentPage || nextPage);
        }
      })
      .catch((err) => {
        console.error("Failed to load more bank accounts:", err);
      })
      .finally(() => {
        setIsLoadingMore(false);
      });
  };

  const handleCopy = (id, textToCopy) => {
    navigator.clipboard.writeText(textToCopy);
    setCopiedId(id);
    setTimeout(() => {
      setCopiedId(null);
    }, 2000);
  };

  if (loading) {
    return <Loading />;
  }

  const currentMethods = methods;

  const whatsappNum = settings?.site_whatsapp ? settings.site_whatsapp.replace(/\D/g, '') : "";
  const phoneNum = settings?.site_phone ? settings.site_phone.replace(/\D/g, '') : "";

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-32 animate-fade-in">
        <div className="w-full max-w-[1280px] mx-auto px-6 md:px-12 py-8 relative">
          {/* Background Orbs */}
          <div className="absolute top-[-100px] left-[10%] w-[300px] h-[300px] bg-[#FF8A00]/5 rounded-full blur-[120px] pointer-events-none -z-10" />
          <div className="absolute bottom-[100px] right-[10%] w-[400px] h-[400px] bg-[#D98B50]/5 rounded-full blur-[150px] pointer-events-none -z-10" />

          {/* Hero Header */}
          <div className="text-center max-w-3xl mx-auto mb-16">
            <motion.div
              initial={{ opacity: 0, y: -20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
            >
              <span className="text-xs md:text-sm font-semibold tracking-[0.2em] text-[#FF8A00] uppercase mb-3 block">
                {t("paymentMethods")}
              </span>
              <h1 className="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight">
                {t("paymentMethodsTitle")}
              </h1>
              <p className="text-white/60 text-sm md:text-base leading-relaxed">
                {activeTab === "egypt" ? t("egyptBanksDesc") : t("ksaBanksDesc")}
              </p>
            </motion.div>

            {/* Custom Tabs */}
            <div className="flex justify-center mt-10">
              <div className="bg-white/5 border border-white/10 p-1.5 rounded-full flex items-center gap-1 backdrop-blur-md">
                <button
                  onClick={() => setActiveTab("egypt")}
                  className={`relative px-6 py-2.5 rounded-full text-xs md:text-sm font-bold tracking-wider transition-all duration-300 cursor-pointer ${activeTab === "egypt" ? "text-black z-10" : "text-white/60 hover:text-white"
                    }`}
                >
                  {activeTab === "egypt" && (
                    <motion.span
                      layoutId="activeTabGlow"
                      className="absolute inset-0 bg-[#FF8A00] rounded-full -z-10 shadow-lg shadow-[#FF8A00]/20"
                      transition={{ type: "spring", stiffness: 300, damping: 30 }}
                    />
                  )}
                  {t("payFromEgypt")}
                </button>
                <button
                  onClick={() => setActiveTab("ksa")}
                  className={`relative px-6 py-2.5 rounded-full text-xs md:text-sm font-bold tracking-wider transition-all duration-300 cursor-pointer ${activeTab === "ksa" ? "text-black z-10" : "text-white/60 hover:text-white"
                    }`}
                >
                  {activeTab === "ksa" && (
                    <motion.span
                      layoutId="activeTabGlow"
                      className="absolute inset-0 bg-[#FF8A00] rounded-full -z-10 shadow-lg shadow-[#FF8A00]/20"
                      transition={{ type: "spring", stiffness: 300, damping: 30 }}
                    />
                  )}
                  {t("payFromKSA")}
                </button>
              </div>
            </div>
          </div>

          {/* Grid of Bank Cards */}
          <motion.div
            layout
            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8"
          >
            <AnimatePresence mode="popLayout">
              {currentMethods.map((method, index) => {
                const imageUrl = method.image;
                const isCopied = copiedId === method.id;
                const nameText = language === "ar" ? (method.name?.ar || method.name?.en || "") : (method.name?.en || method.name?.ar || "");
                const ownerText = language === "ar" ? (method.owner?.ar || method.owner?.en || "") : (method.owner?.en || method.owner?.ar || "");
                const accountText = language === "ar" ? (method.account?.ar || method.account?.en || "") : (method.account?.en || method.account?.ar || "");

                const clipboardString = `${nameText}\n${t("accName")}: ${ownerText}\n${t("accNumber")}: ${accountText}`;

                const isContactAcc = accountText?.includes?.("تواصل") || accountText?.toLowerCase?.()?.includes?.("contact") || method.account?.ar?.includes?.("تواصل");

                return (
                  <motion.div
                    key={method.id}
                    layout
                    initial={{ opacity: 0, scale: 0.9, y: 30 }}
                    animate={{ opacity: 1, scale: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.9, y: 30 }}
                    transition={{ duration: 0.4, delay: index * 0.05 }}
                    whileHover={{ y: -6 }}
                    className="group relative bg-white/[0.04] border border-white/10 hover:border-[#FF8A00]/30 rounded-2xl overflow-hidden transition-all duration-300 shadow-xl hover:shadow-[0_10px_30px_rgba(255,138,0,0.1)] flex flex-col"
                  >
                    {/* Top accent line */}
                    <div className={`h-[2px] w-full bg-gradient-to-r ${method.brandColor || "from-[#FF8A00] to-[#D98B50]"}`} />

                    {/* Card Body - RTL: Logo on LEFT, info on RIGHT */}
                    <div className="flex items-center gap-4 p-5" dir="rtl">
                      {/* Right (start in RTL): Bank info */}
                      <div className="flex-1 min-w-0">
                        <h3 className="text-base font-bold text-white mb-3 group-hover:text-[#FF8A00] transition-colors duration-300 truncate">
                          {nameText}
                        </h3>
                        <div className="space-y-2">
                          <div className="flex items-start gap-1.5">
                            <span className="text-xs text-white/40 font-medium shrink-0 mt-0.5">{t("accName")} :</span>
                            <span className="text-xs font-semibold text-white/80 break-words">{ownerText}</span>
                          </div>
                          <div className="flex items-start gap-1.5">
                            <span className="text-xs text-white/40 font-medium shrink-0 mt-0.5">{t("accNumber")} :</span>
                            <span className={`text-xs font-bold break-all ${isContactAcc ? "text-[#FF8A00]/80 italic" : "text-white"}`}>
                              {accountText}
                            </span>
                          </div>
                        </div>
                      </div>

                      {/* Left (end in RTL): Bank Logo - bigger */}
                      <div className="shrink-0 w-20 h-20 rounded-xl bg-white flex items-center justify-center shadow-md shadow-black/20 p-2">
                        <img
                          src={method.image ? getMediaUrl(method.image) : siteLogo}
                          onError={(e) => { e.target.src = siteLogo; }}
                          alt={nameText}
                          className="max-w-full max-h-full object-contain"
                        />
                      </div>
                    </div>

                    {/* Copy Button - Full width at bottom */}
                    <button
                      onClick={() => handleCopy(method.id, clipboardString)}
                      className={`w-full py-3 px-4 font-semibold text-xs tracking-wider flex items-center justify-center gap-2 transition-all duration-300 cursor-pointer border-t mt-auto ${isCopied
                          ? "bg-green-500/10 border-green-500/20 text-green-400"
                          : "bg-white/[0.03] border-white/5 text-white/60 hover:bg-[#FF8A00]/10 hover:text-[#FF8A00] hover:border-[#FF8A00]/20"
                        }`}
                    >
                      {isCopied ? (
                        <>
                          <FaCheck className="w-3.5 h-3.5" />
                          <span>{t("detailsCopied")}</span>
                        </>
                      ) : (
                        <>
                          <FaCopy className="w-3 h-3" />
                          <span>{t("copyDetails")}</span>
                        </>
                      )}
                    </button>
                  </motion.div>
                );
              })}
            </AnimatePresence>
            {isLoadingMore && [1, 2, 3].map((i) => (
              <div
                key={`skeleton-more-${i}`}
                className="group relative bg-[#05050f]/80 border border-white/5 rounded-2xl p-6 md:p-8 backdrop-blur-md flex flex-col justify-between overflow-hidden shadow-2xl animate-pulse min-h-[350px]"
              >
                <div className="absolute top-0 left-0 right-0 h-[3px] bg-white/10" />
                <div>
                  <div className="flex items-center justify-between mb-8">
                    <div className="w-14 h-14 rounded-xl bg-white/10" />
                    <div className="w-16 h-6 rounded-full bg-white/10" />
                  </div>
                  <div className="w-32 h-6 bg-white/10 rounded mb-6" />
                  <div className="space-y-4 mb-8">
                    <div className="flex flex-col gap-1 border-b border-white/5 pb-3">
                      <div className="w-16 h-3 bg-white/10 rounded mb-1" />
                      <div className="w-24 h-4 bg-white/10 rounded" />
                    </div>
                    <div className="flex flex-col gap-1">
                      <div className="w-16 h-3 bg-white/10 rounded mb-1" />
                      <div className="w-32 h-4 bg-white/10 rounded" />
                    </div>
                  </div>
                </div>
                <div className="w-full h-12 bg-white/5 rounded-xl" />
              </div>
            ))}
          </motion.div>

          {/* Load More Button */}
          {hasMore && (
            <div className="flex justify-center mt-12 w-full z-10">
              <button
                onClick={loadMoreMethods}
                disabled={isLoadingMore}
                className="flex items-center gap-2 px-8 py-3 text-white bg-transparent border border-[#FF8A00] hover:bg-[#FF8A00] rounded-full transition-all duration-300 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-base font-semibold"
              >
                {isLoadingMore ? (
                  <svg
                    className="w-5 h-5 animate-spin"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                ) : (
                  <svg
                    className="w-5 h-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M19 13H5v-2h14v2z"
                      fill="currentColor"
                    />
                    <path
                      d="M12 5v14M5 12h14"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                    />
                  </svg>
                )}
                {isLoadingMore ? t("loading") : t("loadMore")}
              </button>
            </div>
          )}

          {/* Global CTA Section */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.3 }}
            className="mt-16 bg-gradient-to-r from-[#05050f]/80 to-[#101025]/80 border border-white/5 rounded-3xl p-8 md:p-10 backdrop-blur-md relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8 shadow-2xl"
          >
            <div className="absolute top-0 right-0 w-[200px] h-[200px] bg-[#FF8A00]/5 rounded-full blur-[80px] pointer-events-none" />

            <div className="text-center md:text-right flex-1">
              <h4 className="text-xl md:text-2xl font-bold text-white mb-3">
                {t("haveCompletedTransfer")}
              </h4>
              <p className="text-white/60 text-xs md:text-sm max-w-xl leading-relaxed">
                {t("haveCompletedTransferDesc")}
              </p>
            </div>

            <div className="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
              {whatsappNum && (
                <a
                  href={`https://wa.me/${whatsappNum}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20ba5a] text-black font-bold text-xs md:text-sm tracking-wide py-4 px-8 rounded-2xl shadow-lg shadow-[#25D366]/20 transition-all duration-300 hover:-translate-y-1"
                >
                  <FaWhatsapp className="w-5 h-5" />
                  <span>{t("confirmTransfer")}</span>
                </a>
              )}

              {phoneNum && (
                <a
                  href={`tel:${phoneNum}`}
                  className="flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-xs md:text-sm tracking-wide py-4 px-8 rounded-2xl transition-all duration-300 hover:-translate-y-1"
                >
                  <FaPhoneAlt className="w-4 h-4" />
                  <span>{t("callUs")}</span>
                </a>
              )}
            </div>
          </motion.div>
        </div>
      </div>
    </main>
  );
};

export default BankAccountsContent;
