"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";
import { useAuth } from "@/app/context/AuthContext";

const ContactForm = () => {
  const { t, language } = useLanguage();
  const { user, login, register, logout } = useAuth();
  const [authMode, setAuthMode] = useState("login"); // 'login' or 'register'
  const [authData, setAuthData] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    phone: "",
    subject: "inquiry",
    message: "",
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [status, setStatus] = useState(null);
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const handleAuthChange = (e) => {
    const { name, value } = e.target;
    setAuthData((prev) => ({ ...prev, [name]: value }));
  };

  const handleAuthSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setStatus(null);
    let res;
    if (authMode === "login") {
      res = await login(authData.email, authData.password);
    } else {
      res = await register(authData.name, authData.email, authData.password, authData.password_confirmation);
    }
    
    if (res.success) {
      setStatus({ type: 'success', message: language === 'ar' ? 'تم تسجيل الدخول بنجاح!' : 'Success!' });
    } else {
      setStatus({ type: 'error', message: res.message });
    }
    setIsSubmitting(false);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setStatus(null);

    try {
      await fetchAPI('/contact', {
        method: 'POST',
        body: JSON.stringify({
          name: `${formData.firstName} ${formData.lastName}`,
          email: formData.email,
          phone: formData.phone,
          message: formData.message,
          type: formData.subject,
        }),
      });
      setStatus({ type: 'success', message: t("contactSuccess") || (language === 'ar' ? 'تم إرسال رسالتك بنجاح!' : 'Message sent successfully!') });
      setFormData({
        firstName: "",
        lastName: "",
        email: "",
        phone: "",
        subject: "inquiry",
        message: "",
      });
    } catch (error) {
      console.error("Failed to submit message:", error);
      setStatus({ type: 'error', message: t("contactError") || (language === 'ar' ? 'فشل إرسال الرسالة. يرجى المحاولة مرة أخرى.' : 'Failed to send message. Please try again.') });
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  if (!user) {
    return (
      <div className="w-full lg:w-1/2 mt-12 lg:mt-0 relative z-10" dir={language === "ar" ? "rtl" : "ltr"}>
        <motion.div
          initial={{ opacity: 0, scale: 0.95 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.5 }}
          className="bg-[#0b0f2c]/40 backdrop-blur-md border border-white/10 rounded-3xl p-8 lg:p-12 shadow-[0_20px_50px_rgba(0,0,0,0.3)] shadow-[#FF8A00]/5 flex flex-col hover:border-white/20 transition-all duration-300"
        >
          <h2 className="text-2xl font-bold text-white mb-6 text-center">{t("mustLogin")}</h2>
          
          <div className="inline-flex p-1 rounded-full bg-white/[0.03] border border-white/10 mb-8 self-center">
            <button 
              type="button"
              onClick={() => { setAuthMode("login"); setStatus(null); }}
              className={`px-6 py-2 rounded-full text-xs md:text-sm font-semibold transition-all duration-300 ${
                authMode === 'login' 
                  ? 'bg-gradient-to-r from-[#FF8A00] to-[#FF6A00] text-white shadow-[0_4px_15px_rgba(255,138,0,0.3)]' 
                  : 'text-white/60 hover:text-white hover:bg-white/5'
              }`}
            >
              {t("loginNow")}
            </button>
            <button 
              type="button"
              onClick={() => { setAuthMode("register"); setStatus(null); }}
              className={`px-6 py-2 rounded-full text-xs md:text-sm font-semibold transition-all duration-300 ${
                authMode === 'register' 
                  ? 'bg-gradient-to-r from-[#FF8A00] to-[#FF6A00] text-white shadow-[0_4px_15px_rgba(255,138,0,0.3)]' 
                  : 'text-white/60 hover:text-white hover:bg-white/5'
              }`}
            >
              {t("registerNow")}
            </button>
          </div>

          <form onSubmit={handleAuthSubmit} className="flex flex-col gap-5 text-start w-full">
            {status && (
              <div className={`p-4 rounded-xl text-sm font-medium ${status.type === 'success' ? 'bg-green-500/10 border border-green-500/20 text-green-400' : 'bg-red-500/10 border border-red-500/20 text-red-400'}`}>
                {status.message}
              </div>
            )}
            
            {authMode === "register" && (
              <div className="flex flex-col gap-2">
                <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{language === 'ar' ? 'الاسم' : 'Name'}</label>
                <input
                  type="text"
                  name="name"
                  value={authData.name}
                  onChange={handleAuthChange}
                  required
                  className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl px-4 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                  placeholder={language === 'ar' ? 'الاسم الكامل' : 'Full Name'}
                />
              </div>
            )}

            <div className="flex flex-col gap-2">
              <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{language === 'ar' ? 'البريد الإلكتروني' : 'Email'}</label>
              <input
                type="email"
                name="email"
                value={authData.email}
                onChange={handleAuthChange}
                required
                className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl px-4 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                placeholder="example@email.com"
              />
            </div>

            <div className="flex flex-col gap-2">
              <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{language === 'ar' ? 'كلمة المرور' : 'Password'}</label>
              <div className="relative w-full">
                <input
                  type={showPassword ? "text" : "password"}
                  name="password"
                  value={authData.password}
                  onChange={handleAuthChange}
                  required
                  className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl pl-4 pr-12 rtl:pr-4 rtl:pl-12 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-4 rtl:right-auto rtl:left-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors"
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
            </div>

            {authMode === "register" && (
              <div className="flex flex-col gap-2">
                <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{language === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password'}</label>
                <div className="relative w-full">
                  <input
                    type={showConfirmPassword ? "text" : "password"}
                    name="password_confirmation"
                    value={authData.password_confirmation}
                    onChange={handleAuthChange}
                    required
                    className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl pl-4 pr-12 rtl:pr-4 rtl:pl-12 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                    placeholder="••••••••"
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                    className="absolute right-4 rtl:right-auto rtl:left-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors"
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
              </div>
            )}

            <motion.button
              whileHover={{ scale: 1.02, boxShadow: "0 0 20px rgba(255, 138, 0, 0.4)" }}
              whileTap={{ scale: 0.98 }}
              type="submit"
              disabled={isSubmitting}
              className="w-full bg-gradient-to-r from-[#FF8A00] to-[#FF6A00] text-white py-3.5 rounded-xl font-bold mt-4 hover:brightness-110 disabled:opacity-50 transition-all shadow-[0_4px_20px_rgba(255,138,0,0.25)]"
            >
              {isSubmitting ? '...' : (authMode === 'login' ? t("loginNow") : t("registerNow"))}
            </motion.button>

            <p className="text-white/40 text-xs text-center mt-4">
              {authMode === 'login' ? t("donthaveAccount") : t("alreadyhaveAccount")}{' '}
              <span 
                onClick={() => setAuthMode(authMode === 'login' ? 'register' : 'login')}
                className="text-[#FF8A00] font-semibold cursor-pointer hover:underline"
              >
                {authMode === 'login' ? t("registerNow") : t("loginNow")}
              </span>
            </p>
          </form>
        </motion.div>
      </div>
    );
  }

  return (
    <div className="w-full lg:w-1/2 mt-12 lg:mt-0 relative z-10" dir={language === "ar" ? "rtl" : "ltr"}>
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 0.5 }}
        className="bg-[#0b0f2c]/40 backdrop-blur-md border border-white/10 rounded-3xl p-8 lg:p-12 shadow-[0_20px_50px_rgba(0,0,0,0.3)] shadow-[#FF8A00]/5 flex flex-col hover:border-white/20 transition-all duration-300"
      >
        <div className="flex justify-between items-center mb-8 pb-4 border-b border-white/5">
          <p className="text-white/60 text-sm">
            {language === 'ar' ? 'مرحباً، ' : 'Welcome, '} 
            <span className="text-white font-semibold">{user.name}</span>
          </p>
          <button 
            type="button"
            onClick={logout} 
            className="text-xs font-semibold text-[#FF8A00] hover:text-[#FF6A00] transition-colors border border-[#FF8A00]/30 hover:border-[#FF8A00] px-3 py-1.5 rounded-lg bg-[#FF8A00]/5 hover:bg-[#FF8A00]/10"
          >
            {language === 'ar' ? 'تسجيل الخروج' : 'Logout'}
          </button>
        </div>

        <form onSubmit={handleSubmit} className="flex flex-col gap-6">
          {status && (
            <div className={`p-4 rounded-xl text-sm font-medium ${status.type === 'success' ? 'bg-green-500/10 border border-green-500/20 text-green-400' : 'bg-red-500/10 border border-red-500/20 text-red-400'}`}>
              {status.message}
            </div>
          )}

          {/* Row 1 */}
          <div className="flex flex-col md:flex-row gap-6">
            <div className="w-full md:w-1/2 flex flex-col gap-2">
              <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{t("contactFirstName")}</label>
              <input
                type="text"
                name="firstName"
                value={formData.firstName}
                onChange={handleChange}
                required
                className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl px-4 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                placeholder={language === 'ar' ? 'الاسم الأول' : 'First Name'}
              />
            </div>
            <div className="w-full md:w-1/2 flex flex-col gap-2">
              <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{t("contactLastName")}</label>
              <input
                type="text"
                name="lastName"
                value={formData.lastName}
                onChange={handleChange}
                className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl px-4 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                placeholder={language === 'ar' ? 'الاسم الأخير' : 'Last Name'}
              />
            </div>
          </div>

          {/* Row 2 */}
          <div className="flex flex-col md:flex-row gap-6">
            <div className="w-full md:w-1/2 flex flex-col gap-2">
              <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{t("contactEmail")}</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
                className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl px-4 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                placeholder="example@email.com"
              />
            </div>
            <div className="w-full md:w-1/2 flex flex-col gap-2">
              <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{t("contactPhone")}</label>
              <input
                type="tel"
                name="phone"
                value={formData.phone}
                onChange={handleChange}
                className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl px-4 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] placeholder-white/20"
                placeholder="+1 012 3456 789"
              />
            </div>
          </div>

          {/* Subject Selector Chips */}
          <div className="flex flex-col gap-3">
            <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{t("contactSubject")}</label>
            <div className="flex flex-wrap gap-2 text-sm text-gray-300">
              {[
                { value: 'inquiry', label: language === 'ar' ? 'استفسار عام' : 'General Inquiry' },
                { value: 'pricing', label: language === 'ar' ? 'أسعار ومبيعات' : 'Pricing & Sales' },
                { value: 'complaint', label: language === 'ar' ? 'شكاوى' : 'Complaints' },
                { value: 'support', label: language === 'ar' ? 'دعم فني' : 'Technical Support' },
                { value: 'job', label: language === 'ar' ? 'طلب توظيف' : 'Job Application' },
              ].map((sub) => {
                const isSelected = formData.subject === sub.value;
                return (
                  <label 
                    key={sub.value} 
                    className={`flex items-center justify-center px-4 py-2.5 rounded-xl border text-xs md:text-sm font-medium cursor-pointer transition-all duration-300 ${
                      isSelected 
                        ? 'border-[#FF8A00] bg-[#FF8A00]/10 text-white shadow-[0_0_15px_rgba(255,138,0,0.15)]' 
                        : 'border-white/10 bg-white/[0.02] text-white/60 hover:border-white/20 hover:bg-white/[0.05]'
                    }`}
                  >
                    <input
                      type="radio"
                      name="subject"
                      value={sub.value}
                      checked={isSelected}
                      onChange={handleChange}
                      className="sr-only"
                    />
                    {sub.label}
                  </label>
                );
              })}
            </div>
          </div>

          {/* Message */}
          <div className="flex flex-col gap-2">
            <label className="text-white/80 text-xs font-semibold uppercase tracking-wider">{t("contactMessageLabel")}</label>
            <textarea
              name="message"
              value={formData.message}
              onChange={handleChange}
              required
              className="w-full bg-white/[0.03] border border-white/10 focus:border-[#FF8A00] focus:bg-[#FF8A00]/5 rounded-xl px-4 py-3 outline-none text-white text-sm transition-all duration-300 focus:shadow-[0_0_15px_rgba(255,138,0,0.2)] resize-none h-32 placeholder-white/20"
              placeholder={language === 'ar' ? 'اكتب رسالتك هنا..' : 'Write your message..'}
            ></textarea>
          </div>

          {/* Submit Button */}
          <div className="flex justify-end mt-4">
            <motion.button
              whileHover={{ scale: 1.05, boxShadow: "0 0 25px rgba(255, 138, 0, 0.5)" }}
              whileTap={{ scale: 0.95 }}
              type="submit"
              disabled={isSubmitting}
              className="bg-gradient-to-r from-[#FF8A00] to-[#FF6A00] text-white px-8 py-3.5 rounded-xl font-bold text-sm hover:brightness-110 transition-all shadow-[0_4px_20px_rgba(255,138,0,0.25)] disabled:opacity-50"
            >
              {isSubmitting ? (t("contactSending") || 'Sending...') : (t("contactSendMessage") || 'Send Message')}
            </motion.button>
          </div>
        </form>
      </motion.div>
    </div>
  );
};

export default ContactForm;
