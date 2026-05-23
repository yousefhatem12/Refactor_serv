"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI } from "@/lib/api";
import { 
  FaCopy, 
  FaCheck, 
  FaWhatsapp, 
  FaPhoneAlt
} from "react-icons/fa";

const BankAccountsContent = () => {
  const { t, language } = useLanguage();
  const [activeTab, setActiveTab] = useState("egypt"); // "egypt" or "ksa"
  const [copiedId, setCopiedId] = useState(null);
  const [methods, setMethods] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAPI(`/bank-accounts?lang=${language}`)
      .then((data) => {
        if (data && Array.isArray(data)) {
          setMethods(data);
        }
      })
      .catch((err) => console.error("Error fetching bank accounts:", err))
      .finally(() => setLoading(false));
  }, [language]);

  const handleCopy = (id, textToCopy) => {
    navigator.clipboard.writeText(textToCopy);
    setCopiedId(id);
    setTimeout(() => {
      setCopiedId(null);
    }, 2000);
  };

  const defaultEgyptMethods = [
    {
      id: "eg_ahly",
      name: {
        ar: "البنك الأهلي المصري",
        en: "National Bank of Egypt"
      },
      owner: {
        ar: "سيرف فايف لخدمات الويب",
        en: "Serv5 Web Services"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/ahli_egypt.png",
      type: "bank",
      brandColor: "from-[#006A4E] to-[#128C7E]",
      shadow: "rgba(0, 106, 78, 0.25)"
    },
    {
      id: "eg_misr",
      name: {
        ar: "بنك مصر",
        en: "Banque Misr"
      },
      owner: {
        ar: "سيرف فايف لخدمات الويب",
        en: "Serv5 Web Services"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/misr_egypt.jpg",
      type: "bank",
      brandColor: "from-[#8B0000] to-[#D4AF37]",
      shadow: "rgba(139, 0, 0, 0.25)"
    },
    {
      id: "eg_credit",
      name: {
        ar: "كريدي أجريكول",
        en: "Credit Agricole"
      },
      owner: {
        ar: "سيرف فايف لخدمات الويب",
        en: "Serv5 Web Services"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/credit_agricole.jpg",
      type: "bank",
      brandColor: "from-[#005c3d] to-[#009257]",
      shadow: "rgba(0, 92, 61, 0.25)"
    },
    {
      id: "eg_united",
      name: {
        ar: "المصرف المتحد",
        en: "The United Bank"
      },
      owner: {
        ar: "سيرف فايف لخدمات الويب",
        en: "Serv5 Web Services"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/united_bank.jpg",
      type: "bank",
      brandColor: "from-[#112E51] to-[#1D70B8]",
      shadow: "rgba(17, 46, 81, 0.25)"
    },
    {
      id: "eg_post",
      name: {
        ar: "البريد المصري",
        en: "Egyptian Post"
      },
      owner: {
        ar: "سيرف فايف لخدمات الويب",
        en: "Serv5 Web Services"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/egypt_post.jpg",
      type: "bank",
      brandColor: "from-[#00875A] to-[#F1C40F]",
      shadow: "rgba(0, 135, 90, 0.25)"
    },
    {
      id: "eg_orange",
      name: {
        ar: "أورنج كاش",
        en: "Orange Cash"
      },
      owner: {
        ar: "تحويل مباشر للمحفظة",
        en: "Direct Wallet Transfer"
      },
      account: {
        ar: "تواصل مع الإدارة لتحديد الرقم",
        en: "Contact administration for wallet number"
      },
      image: "/images/banks/orange_cash.png",
      type: "wallet",
      brandColor: "from-[#FF6600] to-[#FF8533]",
      shadow: "rgba(255, 102, 0, 0.25)"
    },
    {
      id: "eg_etisalat",
      name: {
        ar: "اتصالات كاش",
        en: "Etisalat Cash"
      },
      owner: {
        ar: "تحويل مباشر للمحفظة",
        en: "Direct Wallet Transfer"
      },
      account: {
        ar: "تواصل مع الإدارة لتحديد الرقم",
        en: "Contact administration for wallet number"
      },
      image: "/images/banks/etisalat_cash.jpg",
      type: "wallet",
      brandColor: "from-[#71B000] to-[#9CD62C]",
      shadow: "rgba(113, 176, 0, 0.25)"
    },
    {
      id: "eg_vodafone",
      name: {
        ar: "فودافون كاش",
        en: "Vodafone Cash"
      },
      owner: {
        ar: "تحويل مباشر للمحفظة",
        en: "Direct Wallet Transfer"
      },
      account: {
        ar: "تواصل مع الإدارة لتحديد الرقم",
        en: "Contact administration for wallet number"
      },
      image: "/images/banks/vodafone_cash.png",
      type: "wallet",
      brandColor: "from-[#E60000] to-[#FF3333]",
      shadow: "rgba(230, 0, 0, 0.25)"
    }
  ];

  const defaultKsaMethods = [
    {
      id: "ksa_rajhi",
      name: {
        ar: "مصرف الراجحي",
        en: "Al Rajhi Bank"
      },
      owner: {
        ar: "إسماعيل أحمد محمد",
        en: "Ismail Ahmad Muhammad"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/rajhi_ksa.png",
      type: "bank",
      brandColor: "from-[#005A9C] to-[#008BE6]",
      shadow: "rgba(0, 90, 156, 0.25)"
    },
    {
      id: "ksa_ahly",
      name: {
        ar: "البنك الأهلي السعودي (SNB)",
        en: "Al Ahly Saudi Bank (SNB)"
      },
      owner: {
        ar: "إسماعيل أحمد محمد",
        en: "Ismail Ahmad Muhammad"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/ahli_ksa.jpg",
      type: "bank",
      brandColor: "from-[#004F34] to-[#007F54]",
      shadow: "rgba(0, 79, 52, 0.25)"
    },
    {
      id: "ksa_bilad",
      name: {
        ar: "بنك البلاد",
        en: "Bank Albilad"
      },
      owner: {
        ar: "إسماعيل أحمد محمد",
        en: "Ismail Ahmad Muhammad"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/bilad_ksa.jpg",
      type: "bank",
      brandColor: "from-[#003E7E] to-[#DCAE1D]",
      shadow: "rgba(0, 62, 126, 0.25)"
    },
    {
      id: "ksa_united",
      name: {
        ar: "المصرف المتحد (تحويل دولي)",
        en: "The United Bank (International)"
      },
      owner: {
        ar: "سيرف فايف لخدمات التسويق الإلكتروني",
        en: "Serv5 for E-Marketing Services"
      },
      account: {
        ar: "تواصل مع الإدارة للحصول عليه",
        en: "Contact the administration"
      },
      image: "/images/banks/united_bank.jpg",
      type: "international",
      brandColor: "from-[#112E51] to-[#3B71CA]",
      shadow: "rgba(17, 46, 81, 0.25)"
    },
    {
      id: "ksa_wu",
      name: {
        ar: "ويسترن يونيون",
        en: "Western Union"
      },
      owner: {
        ar: "سيرف فايف لخدمات التسويق الإلكتروني",
        en: "Serv5 for E-Marketing Services"
      },
      account: {
        ar: "تواصل مع الإدارة لتفاصيل المستلم",
        en: "Contact administration for recipient details"
      },
      image: "/images/banks/western_union.jpg",
      type: "international",
      brandColor: "from-[#FFCC00] to-[#E6B800]",
      shadow: "rgba(255, 204, 0, 0.25)"
    },
    {
      id: "ksa_moneygram",
      name: {
        ar: "موني جرام",
        en: "MoneyGram"
      },
      owner: {
        ar: "سيرف فايف لخدمات التسويق الإلكتروني",
        en: "Serv5 for E-Marketing Services"
      },
      account: {
        ar: "تواصل مع الإدارة لتفاصيل المستلم",
        en: "Contact administration for recipient details"
      },
      image: "/images/banks/moneygram.jpg",
      type: "international",
      brandColor: "from-[#E31B23] to-[#FF4D55]",
      shadow: "rgba(227, 27, 35, 0.25)"
    }
  ];

  const egyptMethods = methods.filter(m => m.country === "egypt");

  const ksaMethods = methods.filter(m => m.country === "ksa");

  const currentMethods = activeTab === "egypt" ? egyptMethods : ksaMethods;

  return (
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
              className={`relative px-6 py-2.5 rounded-full text-xs md:text-sm font-bold tracking-wider transition-all duration-300 cursor-pointer ${
                activeTab === "egypt" ? "text-black z-10" : "text-white/60 hover:text-white"
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
              className={`relative px-6 py-2.5 rounded-full text-xs md:text-sm font-bold tracking-wider transition-all duration-300 cursor-pointer ${
                activeTab === "ksa" ? "text-black z-10" : "text-white/60 hover:text-white"
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
            const nameText = language === "ar" ? method.name.ar : method.name.en;
            const ownerText = language === "ar" ? method.owner.ar : method.owner.en;
            const accountText = language === "ar" ? method.account.ar : method.account.en;
            
            const clipboardString = `${nameText}\n${t("accName")}: ${ownerText}\n${t("accNumber")}: ${accountText}`;

            return (
              <motion.div
                key={method.id}
                layout
                initial={{ opacity: 0, scale: 0.9, y: 30 }}
                animate={{ opacity: 1, scale: 1, y: 0 }}
                exit={{ opacity: 0, scale: 0.9, y: 30 }}
                transition={{ duration: 0.4, delay: index * 0.05 }}
                whileHover={{ y: -8 }}
                style={{ "--shadow-glow": method.shadow }}
                className="group relative bg-[#05050f]/80 border border-white/5 hover:border-white/10 rounded-2xl p-6 md:p-8 backdrop-blur-md transition-all duration-300 flex flex-col justify-between overflow-hidden shadow-2xl hover:shadow-[0_15px_35px_var(--shadow-glow)]"
              >
                {/* Glowing border line */}
                <div className={`absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r ${method.brandColor}`} />

                <div>
                  {/* Card Header */}
                  <div className="flex items-center justify-between mb-8">
                    <div className="w-14 h-14 rounded-xl bg-white p-2.5 flex items-center justify-center shadow-md shadow-black/10 group-hover:scale-105 transition-all duration-300">
                      <img
                        src={imageUrl}
                        alt={nameText}
                        className="max-w-full max-h-full object-contain"
                      />
                    </div>
                    <span className="text-[10px] uppercase tracking-[0.15em] font-semibold bg-white/5 border border-white/10 px-3 py-1 rounded-full text-white/50">
                      {method.type}
                    </span>
                  </div>

                  {/* Bank/Method Title */}
                  <h3 className="text-xl font-bold text-white mb-6 group-hover:text-[#FF8A00] transition-colors duration-300">
                    {nameText}
                  </h3>

                  {/* Card Fields */}
                  <div className="space-y-4 mb-8">
                    <div className="flex flex-col gap-1 border-b border-white/5 pb-3">
                      <span className="text-xs text-white/40 font-medium tracking-wide">
                        {t("accName")}
                      </span>
                      <span className="text-sm font-semibold text-white/90">
                        {ownerText}
                      </span>
                    </div>

                    <div className="flex flex-col gap-1">
                      <span className="text-xs text-white/40 font-medium tracking-wide">
                        {t("accNumber")}
                      </span>
                      <span className={`text-sm font-bold ${method.account.ar.includes("تواصل") ? "text-[#FF8A00]/80 italic" : "text-white"}`}>
                        {accountText}
                      </span>
                    </div>
                  </div>
                </div>

                {/* Action Copy Button */}
                <button
                  onClick={() => handleCopy(method.id, clipboardString)}
                  className={`w-full py-3.5 px-4 rounded-xl border font-semibold text-xs md:text-sm tracking-wider flex items-center justify-center gap-2 transition-all duration-300 cursor-pointer ${
                    isCopied
                      ? "bg-green-500/10 border-green-500/30 text-green-400"
                      : "bg-white/5 border-white/10 text-white hover:bg-white/10 hover:border-white/20"
                  }`}
                >
                  {isCopied ? (
                    <>
                      <FaCheck className="w-4 h-4" />
                      <span>{t("detailsCopied")}</span>
                    </>
                  ) : (
                    <>
                      <FaCopy className="w-3.5 h-3.5" />
                      <span>{t("copyDetails")}</span>
                    </>
                  )}
                </button>
              </motion.div>
            );
          })}
        </AnimatePresence>
      </motion.div>

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
          <a
            href="https://wa.me/201288863631"
            target="_blank"
            rel="noopener noreferrer"
            className="flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20ba5a] text-black font-bold text-xs md:text-sm tracking-wide py-4 px-8 rounded-2xl shadow-lg shadow-[#25D366]/20 transition-all duration-300 hover:-translate-y-1"
          >
            <FaWhatsapp className="w-5 h-5" />
            <span>{t("confirmTransfer")}</span>
          </a>
          
          <a
            href="tel:00201288863631"
            className="flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-xs md:text-sm tracking-wide py-4 px-8 rounded-2xl transition-all duration-300 hover:-translate-y-1"
          >
            <FaPhoneAlt className="w-4 h-4" />
            <span>{t("callUs")}</span>
          </a>
        </div>
      </motion.div>
    </div>
  );
};

export default BankAccountsContent;
