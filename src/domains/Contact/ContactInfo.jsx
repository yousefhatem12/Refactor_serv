"use client";
import React from "react";
import { motion } from "framer-motion";
import {
  FaFacebook,
  FaInstagram,
  FaLinkedin,
  FaYoutube,
  FaTiktok,
} from "react-icons/fa";
import { RiTwitterXFill } from "react-icons/ri";
import { useLanguage } from "@/app/context/LanguageContext";
import { fetchAPI } from "@/lib/api";

const ContactInfo = ({ data }) => {
  const { t, language } = useLanguage();
  const [settings, setSettings] = React.useState({});
  const [socials, setSocials] = React.useState([]);

  React.useEffect(() => {
    fetchAPI(`/settings?lang=${language}`)
      .then((data) => {
        if (data) setSettings(data);
      })
      .catch(console.error);

    fetchAPI(`/footer-data?lang=${language}`)
      .then((data) => {
        if (data && data.socials) setSocials(data.socials);
      })
      .catch(console.error);
  }, [language]);

  // Split title if it has two parts in translation, or just use as is
  const title =
    data?.title || t("contactGetInTouch1") + " " + t("contactGetInTouch2");

  const getIcon = (platform) => {
    switch (platform.toLowerCase()) {
      case "facebook":
        return <FaFacebook />;
      case "instagram":
        return <FaInstagram />;
      case "linkedin":
        return <FaLinkedin />;
      case "youtube":
        return <FaYoutube />;
      case "tiktok":
        return <FaTiktok />;
      case "twitter":
      case "x":
        return <RiTwitterXFill />;
      default:
        return null;
    }
  };

  const getColor = (platform) => {
    switch (platform.toLowerCase()) {
      case "facebook":
        return "bg-[#1877F2]";
      case "instagram":
        return "bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600";
      case "linkedin":
        return "bg-[#0077B5]";
      case "youtube":
        return "bg-[#FF0000]";
      case "tiktok":
        return "bg-black";
      case "twitter":
      case "x":
        return "bg-black";
      default:
        return "bg-gray-600";
    }
  };

  return (
    <div className="w-full lg:w-1/2 text-white pr-0 lg:pr-12 mb-12 lg:mb-0 relative z-10">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true }}
        className="flex flex-col gap-6"
      >
        <div>
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-black mb-4 leading-tight">
            {data?.title ? (
              data.title
            ) : (
              <>
                {t("contactGetInTouch1")}{" "}
                <span className="text-[#FF8A00] drop-shadow-[0_0_20px_rgba(255,138,0,0.2)]">{t("contactGetInTouch2")}</span>
              </>
            )}
          </h1>
          <p className="text-white/60 text-sm md:text-base max-w-md leading-relaxed font-light mt-2">
            {data?.subtitle || t("contactInfoDesc")}
          </p>
        </div>

        {/* Flags Section */}
        {(() => {
          const branches = settings.site_branches || [
            { flag: "eg", name_en: "Egypt", name_ar: "مصر", is_active: true },
            { flag: "sa", name_en: "Saudi Arabia", name_ar: "السعودية", is_active: false },
            { flag: "kw", name_en: "Kuwait", name_ar: "الكويت", is_active: false }
          ];
          const getBranchName = (branch) => {
            return language === 'ar'
              ? (branch.name_ar || branch.name || branch.flag)
              : (branch.name_en || branch.name || branch.flag);
          };
          // Convert flag emoji (🇪🇬) → ISO code (eg) using Regional Indicator Symbols
          const emojiToCode = (str) => {
            if (!str) return null;
            const chars = [...str]; // spread handles multi-codepoint emoji correctly
            if (chars.length === 2) {
              const c0 = chars[0].codePointAt(0);
              const c1 = chars[1].codePointAt(0);
              if (c0 >= 0x1F1E6 && c0 <= 0x1F1FF && c1 >= 0x1F1E6 && c1 <= 0x1F1FF) {
                return (
                  String.fromCharCode(c0 - 0x1F1E6 + 65) +
                  String.fromCharCode(c1 - 0x1F1E6 + 65)
                ).toLowerCase();
              }
            }
            return null;
          };
          const getFlagSrc = (branch) => {
            const raw = branch.flag || "";
            // Support: 2-letter ISO code ("eg"), emoji flag ("🇪🇬"), or full URL
            const code = raw.length === 2
              ? raw.toLowerCase()
              : emojiToCode(raw);
            return code
              ? `https://flagcdn.com/w80/${code}.png`
              : raw; // fallback: use as-is (URL or emoji)
          };
          const isImageFlag = (branch) => {
            const raw = branch.flag || "";
            return raw.length === 2 || emojiToCode(raw) !== null;
          };
          return (
            <div className="flex items-center gap-4 py-2 border-y border-white/5 my-2">
              <span className="text-xs font-bold tracking-widest text-[#FF8A00] uppercase">
                {t("findUsIn")}
              </span>
              <div className="flex items-center gap-4">
                {branches.map((branch, index) => (
                  <div
                    key={index}
                    className={`relative cursor-pointer group ${
                      branch.is_active ? '' : 'opacity-60 hover:opacity-100'
                    }`}
                    title={getBranchName(branch)}
                  >
                    <div className={`w-10 h-10 rounded-full overflow-hidden border-2 relative flex items-center justify-center bg-white shadow-xl group-hover:scale-110 transition-all duration-300 ${branch.is_active ? 'border-[#FF8A00] shadow-[0_0_12px_rgba(255,138,0,0.5)]' : 'border-white/10 group-hover:border-white/30'}`}>
                      {isImageFlag(branch) ? (
                        <img
                          src={getFlagSrc(branch)}
                          alt={getBranchName(branch)}
                          className="w-full h-full object-cover"
                        />
                      ) : (
                        <span className="text-xl leading-none">{branch.flag}</span>
                      )}
                    </div>
                    {/* Active Pin */}
                    {branch.is_active && (
                      <div className="absolute -bottom-1 -right-1 bg-[#FF8A00] rounded-full p-1 border border-black shadow-[0_0_10px_rgba(255,138,0,0.8)]">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          width="8"
                          height="8"
                          viewBox="0 0 24 24"
                          fill="white"
                          stroke="none"
                        >
                          <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                        </svg>
                      </div>
                    )}
                    {/* Tooltip */}
                    <div className="absolute -bottom-7 left-1/2 -translate-x-1/2 bg-black/80 text-white text-[10px] px-2 py-0.5 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                      {getBranchName(branch)}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          );
        })()}

        {/* Contact Details */}
        <div className="flex flex-col gap-5 my-2">
          {settings.site_phone && (
            <div className="flex items-center gap-4 group cursor-pointer w-max">
              <div className="w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 group-hover:border-[#FF8A00]/50 group-hover:bg-[#FF8A00]/10 flex items-center justify-center text-[#FF8A00] transition-all duration-300">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                >
                  <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.03 21c.73 0 .97-.67.97-1.22v-3.41c0-.54-.45-.99-.99-.99z" />
                </svg>
              </div>
              <span className="text-white/80 group-hover:text-[#FF8A00] transition-colors duration-300 text-sm font-medium">{settings.site_phone}</span>
            </div>
          )}

          {settings.site_whatsapp && (
            <div className="flex items-center gap-4 group cursor-pointer w-max">
              <div className="w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 group-hover:border-[#25D366]/50 group-hover:bg-[#25D366]/10 flex items-center justify-center text-[#25D366] transition-all duration-300">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                >
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.102.824z" />
                </svg>
              </div>
              <span className="text-white/80 group-hover:text-[#25D366] transition-colors duration-300 text-sm font-medium">{settings.site_whatsapp}</span>
            </div>
          )}

          {settings.site_email && (
            <div className="flex items-center gap-4 group cursor-pointer w-max">
              <div className="w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 group-hover:border-orange-400/50 group-hover:bg-orange-400/10 flex items-center justify-center text-orange-400 transition-all duration-300">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                  <polyline points="22,6 12,13 2,6" />
                </svg>
              </div>
              <span className="text-white/80 group-hover:text-orange-400 transition-colors duration-300 text-sm font-medium">{settings.site_email}</span>
            </div>
          )}

          {settings.site_skype && (
            <div className="flex items-center gap-4 group cursor-pointer w-max">
              <div className="w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 group-hover:border-[#00AFF0]/50 group-hover:bg-[#00AFF0]/10 flex items-center justify-center text-[#00AFF0] transition-all duration-300">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                >
                  <path d="M24 11.5c0-1.65-1.35-3-3-3-.2 0-.39.02-.58.06-.9-2.02-3.05-3.56-5.42-3.56-3.31 0-6 2.69-6 6 0 .2.02.39.06.58-2.02.9-3.56 3.05-3.56 5.42 0 3.31 2.69 6 6 6 .2 0 .39-.02.58-.06.9 2.02 3.05 3.56 5.42 3.56 3.31 0 6-2.69 6-6 0-.2-.02-.39-.06-.58 2.02-.9 3.56-3.05 3.56-5.42zm-12.5 6.5c-3.03 0-5.5-2.47-5.5-5.5s2.47-5.5 5.5-5.5 5.5 2.47 5.5 5.5-2.47 5.5-5.5 5.5z" />
                </svg>
              </div>
              <span className="text-white/80 group-hover:text-[#00AFF0] transition-colors duration-300 text-sm font-medium">{settings.site_skype}</span>
            </div>
          )}

          {settings.site_address && (
            <div className="flex items-start gap-4 group cursor-pointer w-max">
              <div className="w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 group-hover:border-blue-500/50 group-hover:bg-blue-500/10 flex items-center justify-center text-blue-500 transition-all duration-300 mt-1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                >
                  <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                </svg>
              </div>
              <span className="text-white/80 group-hover:text-blue-500 transition-colors duration-300 text-sm font-medium max-w-[250px] leading-relaxed">
                {settings.site_address}
              </span>
            </div>
          )}
        </div>

        {/* Social Links */}
        <div className="flex items-center gap-4 mt-4 border-t border-white/5 pt-6">
          <span className="text-xs font-bold tracking-widest text-white/50 uppercase">{t("followUs")}</span>
          <div className="flex gap-3">
            {socials.map((social) => (
              <a
                key={social.id}
                href={social.url}
                target="_blank"
                rel="noopener noreferrer"
                className={`${getColor(social.platform)} w-9 h-9 rounded-xl flex items-center justify-center text-white shadow-lg hover:-translate-y-1 hover:scale-105 transition-all duration-300`}
              >
                {getIcon(social.platform)}
              </a>
            ))}
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default ContactInfo;
