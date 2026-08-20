"use client";
import React from "react";
import { motion } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";

import { useAuth } from "@/app/context/AuthContext";
import { fetchAPI } from "@/lib/api";
import { getCaptchaRequiredMessage } from "@/lib/captcha";
import TurnstileWidget from "@/app/components/Shared/TurnstileWidget";
import GlobalReach from "../../AboutUs/GlobalReach";

const ContactUs = () => {
  const { t, language } = useLanguage();
  const { user, login, register } = useAuth();
  const isAr = language === "ar";

  const [authMode, setAuthMode] = React.useState("login"); // 'login' or 'register'
  const [authData, setAuthData] = React.useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });
  const [showPassword, setShowPassword] = React.useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = React.useState(false);

  const [formData, setFormData] = React.useState({
    name: "",
    email: "",
    phone: "",
    message: "",
    type: "inquiry",
  });
  const [isSubmitting, setIsSubmitting] = React.useState(false);
  const [status, setStatus] = React.useState(null);
  const [authCaptchaToken, setAuthCaptchaToken] = React.useState("");
  const [contactCaptchaToken, setContactCaptchaToken] = React.useState("");
  const [contactCaptchaVersion, setContactCaptchaVersion] = React.useState(0);

  const [settings, setSettings] = React.useState({});

  React.useEffect(() => {
    fetchAPI("/settings")
      .then((data) => {
        if (data) setSettings(data);
      })
      .catch(console.error);
  }, []);

  const handleAuthChange = (e) => {
    const { name, value } = e.target;
    setAuthData((prev) => ({ ...prev, [name]: value }));
  };

  const handleAuthSubmit = async (e) => {
    e.preventDefault();
    if (!authCaptchaToken) {
      setStatus({ type: "error", message: getCaptchaRequiredMessage(language) });
      return;
    }

    setIsSubmitting(true);
    setStatus(null);
    let res;
    if (authMode === "login") {
      res = await login(authData.email, authData.password, authCaptchaToken);
    } else {
      res = await register(
        authData.name,
        authData.email,
        authData.password,
        authData.password_confirmation,
        authCaptchaToken,
      );
    }

    if (res.success) {
      setStatus({ type: "success", message: "Success!" });
    } else {
      setStatus({ type: "error", message: res.message });
    }
    setIsSubmitting(false);
  };

  const handleContactSubmit = async (e) => {
    e.preventDefault();
    if (!contactCaptchaToken) {
      setStatus({ type: "error", message: getCaptchaRequiredMessage(language) });
      return;
    }

    setIsSubmitting(true);
    setStatus(null);

    try {
      const response = await fetchAPI("/contact", {
        method: "POST",
        body: JSON.stringify({
          name: formData.name,
          email: formData.email,
          phone: formData.phone,
          message: formData.message,
          type: formData.type,
          turnstile_token: contactCaptchaToken,
        }),
      });
      if (!response) throw new Error("Contact request failed");

      setContactCaptchaToken("");
      setContactCaptchaVersion((version) => version + 1);
      setStatus({
        type: "success",
        message: t("contactSuccess") || "Message sent successfully!",
      });
      setFormData({
        name: "",
        email: "",
        phone: "",
        message: "",
        type: "inquiry",
      });
    } catch (error) {
      console.error("Failed to submit message:", error);
      setStatus({
        type: "error",
        message:
          t("contactError") || "Failed to send message. Please try again.",
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  return (
    <section className="py-20 px-4 md:px-8 relative z-10">
      <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-start">
        {/* Left Side: Form & Info */}
        <motion.div
          initial={{ opacity: 0, x: -30 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.8 }}
          className="flex flex-col"
        >
          <div className="flex items-center gap-4 mb-4">
            <h2 className="text-lg md:text-3xl lg:text-4xl font-black text-white leading-tight">
              {t("getInTouch1")}{" "}
              <span className="text-[#FF8A00]">{t("getInTouch2")}</span>
            </h2>
            {/* Saturn Planet SVG */}
            <motion.svg
              animate={{ rotate: [0, 360] }}
              transition={{ duration: 20, repeat: Infinity, ease: "linear" }}
              width="40"
              height="40"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <circle cx="12" cy="12" r="5" fill="#A0A0A0" />
              <ellipse
                cx="12"
                cy="12"
                rx="10"
                ry="3"
                stroke="#A0A0A0"
                strokeWidth="1"
                transform="rotate(-15 12 12)"
              />
            </motion.svg>
          </div>

          <p className="text-sm md:text-base text-gray-400 mb-8 max-w-lg leading-relaxed">
            {t("contactDesc")}
          </p>

          {!user ? (
            <div className="mb-12 border border-white/10 rounded-md p-6 bg-[#0F172A]/40">
              <h2 className="text-base md:text-xl font-bold text-white mb-4 text-center">
                {t("mustLogin")}
              </h2>
              <div className="flex justify-center gap-4 mb-6 lg:flex-row flex-col">
                <button
                  onClick={() => {
                    setAuthMode("login");
                    setStatus(null);
                  }}
                  className={`px-4 py-2 rounded-full transition-all text-sm ${authMode === "login" ? "bg-[#FF8A00] text-white" : "bg-white/10 text-gray-400"}`}
                >
                  {t("loginNow")}
                </button>
                <button
                  onClick={() => {
                    setAuthMode("register");
                    setStatus(null);
                  }}
                  className={`px-4 py-2 rounded-full transition-all text-sm ${authMode === "register" ? "bg-[#FF8A00] text-white" : "bg-white/10 text-gray-400"}`}
                >
                  {t("registerNow")}
                </button>
              </div>

              <form onSubmit={handleAuthSubmit} className="flex flex-col gap-4">
                {status && (
                  <div
                    className={`p-3 rounded-md text-sm ${status.type === "success" ? "bg-green-500/20 text-green-400" : "bg-red-500/20 text-red-400"}`}
                  >
                    {status.message}
                  </div>
                )}

                {authMode === "register" && (
                  <input
                    type="text"
                    name="name"
                    value={authData.name}
                    onChange={handleAuthChange}
                    placeholder={isAr ? "الاسم" : "Name"}
                    required
                    className="bg-black/20 border border-white/10 rounded-md p-3 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors"
                  />
                )}

                <input
                  type="email"
                  name="email"
                  value={authData.email}
                  onChange={handleAuthChange}
                  placeholder={isAr ? "البريد الإلكتروني" : "Email"}
                  required
                  className="bg-black/20 border border-white/10 rounded-md p-3 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors"
                />

                <div className="relative w-full">
                  <input
                    type={showPassword ? "text" : "password"}
                    name="password"
                    value={authData.password}
                    onChange={handleAuthChange}
                    placeholder={isAr ? "كلمة المرور" : "Password"}
                    required
                    className="w-full bg-black/20 border border-white/10 rounded-md p-3 pl-3 pr-12 rtl:pr-3 rtl:pl-12 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 rtl:right-auto rtl:left-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors"
                  >
                    {showPassword ? (
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      </svg>
                    ) : (
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                      </svg>
                    )}
                  </button>
                </div>

                {authMode === "register" && (
                  <div className="relative w-full">
                    <input
                      type={showConfirmPassword ? "text" : "password"}
                      name="password_confirmation"
                      value={authData.password_confirmation}
                      onChange={handleAuthChange}
                      placeholder={isAr ? "تأكيد كلمة المرور" : "Confirm Password"}
                      required
                      className="w-full bg-black/20 border border-white/10 rounded-md p-3 pl-3 pr-12 rtl:pr-3 rtl:pl-12 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors"
                    />
                    <button
                      type="button"
                      onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                      className="absolute right-3 rtl:right-auto rtl:left-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors"
                    >
                      {showConfirmPassword ? (
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                          <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                      ) : (
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-5 h-5">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                      )}
                    </button>
                  </div>
                )}

                <TurnstileWidget
                  action={`auth-${authMode}`}
                  language={language}
                  onTokenChange={setAuthCaptchaToken}
                />

                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="bg-white text-black py-3 rounded-md font-bold hover:bg-gray-200 disabled:opacity-50 transition-all text-sm mt-2"
                >
                  {isSubmitting
                    ? "..."
                    : authMode === "login"
                      ? t("loginNow")
                      : t("registerNow")}
                </button>
              </form>
            </div>
          ) : (
            <form
              onSubmit={handleContactSubmit}
              className="flex flex-col gap-4 mb-12"
            >
              <div className="flex justify-between items-center bg-black/20 p-3 rounded-md border border-white/5 mb-2">
                <p className="text-gray-400 text-sm">
                  {isAr ? "مرحباً،" : "Welcome,"}{" "}
                  <span className="text-white font-bold">{user.name}</span>
                </p>
                <button
                  type="button"
                  onClick={() => window.location.reload()}
                  className="text-xs text-[#FF8A00] hover:underline"
                >
                  {isAr ? "تسجيل الخروج" : "Logout"}
                </button>
              </div>

              {status && (
                <div
                  className={`p-4 rounded-md text-sm ${status.type === "success" ? "bg-green-500/20 text-green-400" : "bg-red-500/20 text-red-400"}`}
                >
                  {status.message}
                </div>
              )}

              <input
                type="text"
                name="name"
                value={formData.name}
                onChange={handleChange}
                placeholder={t("namePlaceholder")}
                className="bg-[#0F172A]/40 border border-white/10 rounded-md p-3 md:p-4 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors"
                required
              />
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                placeholder={t("emailPlaceholder")}
                className="bg-[#0F172A]/40 border border-white/10 rounded-md p-3 md:p-4 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors"
                required
              />
              <input
                type="tel"
                name="phone"
                value={formData.phone}
                onChange={handleChange}
                placeholder={t("phonePlaceholder")}
                className="bg-[#0F172A]/40 border border-white/10 rounded-md p-3 md:p-4 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors"
                required
              />
              <select
                name="type"
                value={formData.type}
                onChange={handleChange}
                className="bg-[#0F172A]/40 border border-white/10 rounded-md p-3 md:p-4 text-white focus:outline-none focus:border-[#FF8A00] transition-colors appearance-none cursor-pointer"
                required
              >
                <option value="inquiry">
                  {language === "ar" ? "استفسار عام" : "General Inquiry"}
                </option>
                <option value="pricing">
                  {language === "ar" ? "أسعار ومبيعات" : "Pricing & Sales"}
                </option>
                <option value="complaint">
                  {language === "ar" ? "شكاوى" : "Complaints"}
                </option>
                <option value="support">
                  {language === "ar" ? "دعم فني" : "Technical Support"}
                </option>
                <option value="job">
                  {language === "ar" ? "طلب توظيف" : "Job Application"}
                </option>
              </select>
              <textarea
                name="message"
                value={formData.message}
                onChange={handleChange}
                placeholder={t("messagePlaceholder")}
                rows="4"
                className="bg-[#0F172A]/40 border border-white/10 rounded-md p-3 md:p-4 text-white placeholder:text-gray-500 focus:outline-none focus:border-[#FF8A00] transition-colors resize-none"
                required
              />
              <TurnstileWidget
                key={contactCaptchaVersion}
                action="contact"
                language={language}
                onTokenChange={setContactCaptchaToken}
              />
              <button
                type="submit"
                disabled={isSubmitting}
                className="bg-[#E1804E] hover:bg-[#c96d3f] text-white font-bold py-3 md:py-4 rounded-md transition-all shadow-[0_0_20px_rgba(225,128,78,0.2)] active:scale-95 tracking-widest text-sm disabled:opacity-50"
              >
                {isSubmitting ? "..." : t("send")}
              </button>
            </form>
          )}

          {/* Contact Info Footer */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mt-6">
            <div className="flex items-start gap-3">
              <div className="text-[#FF8A00] mt-1">
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.81 12.81 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
              </div>
              <div>
                <p className="text-white text-xs font-bold uppercase mb-1">
                  {t("phone")}
                </p>
                <p className="text-gray-400 text-xs">
                  {settings.site_phone || "00201288863631"}
                </p>
              </div>
            </div>

            <div className="flex items-start gap-3">
              <div className="text-[#FF8A00] mt-1">
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
              </div>
              <div>
                <p className="text-white text-xs font-bold uppercase mb-1">
                  {t("addressTitle")}
                </p>
                <p className="text-gray-400 text-xs whitespace-pre-line">
                  {settings.site_address || t("contactAddress")}
                </p>
              </div>
            </div>

            <div className="flex items-start gap-3">
              <div className="text-[#FF8A00] mt-1">
                <svg
                  width="20"
                  height="20"
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
              <div>
                <p className="text-white text-xs font-bold uppercase mb-1">
                  {t("email")}
                </p>
                <p className="text-gray-400 text-xs break-all">
                  {settings.site_email || "Sales@serv5.com"}
                </p>
              </div>
            </div>
          </div>
        </motion.div>

        {/* Right Side: Replaced Map with Contents.webp */}
        <div className="w-full h-full flex items-center justify-center relative min-h-[300px] md:min-h-[450px]">
          {/* Falling Meteor */}
          <motion.img
            src="/CTA/meteor2.webp"
            alt="Meteor"
            className="absolute z-30 w-32 md:w-48 pointer-events-none drop-shadow-[0_0_40px_rgba(255,138,0,0.8)]"
            initial={{
              opacity: 0,
              x: isAr ? 1000 : -1000,
              y: -200,
              scaleX: isAr ? -0.5 : 0.5,
              scaleY: 0.5,
              rotate: isAr ? -15 : 15,
            }}
            whileInView={{
              opacity: [0, 1, 1, 0],
              x: isAr ? [1000, 500, 0, 0] : [-1000, -500, 0, 0],
              y: [-200, -100, 0, 0],
              scaleX: isAr ? [-0.5, -1, -1.5, 0] : [0.5, 1, 1.5, 0],
              scaleY: [0.5, 1, 1.5, 0],
            }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{
              duration: 1.0,
              times: [0, 0.1, 0.98, 1],
              ease: "easeIn",
            }}
          />

          {/* Explosion */}
          <motion.img
            src="/CTA/explosion.webp"
            alt="Explosion"
            className="absolute z-40 w-64 md:w-96 pointer-events-none mix-blend-screen"
            initial={{ opacity: 0, scale: 0.2 }}
            whileInView={{
              opacity: [0, 1, 1, 0],
              scale: [0.2, 1.5, 2, 2.2],
            }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{ duration: 1, delay: 0.9, times: [0, 0.2, 0.8, 1] }}
          />

          {/* Global Reach Interactive Map */}
          <motion.div
            className="w-full relative z-20"
            initial={{
              opacity: 0,
              scale: 0.2,
              filter: "brightness(2) blur(10px)",
            }}
            whileInView={{
              opacity: 1,
              scale: 1,
              filter: "brightness(1) blur(0px)",
            }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{
              duration: 1.2,
              delay: 1.4, // Appears as explosion fades
              type: "spring",
              stiffness: 80,
            }}
          >
            <GlobalReach minimal={true} />
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default ContactUs;
