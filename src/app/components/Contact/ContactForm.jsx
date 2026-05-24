"use client";
import React, { useState } from "react";
import { motion } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";

import { useAuth } from "../../context/AuthContext";

const ContactForm = () => {
  const { t, language } = useLanguage();
  const { user, login, register } = useAuth();
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
    phone: "",
    subject: "inquiry",
    message: "",
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [status, setStatus] = useState(null);

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
      setStatus({ type: 'success', message: 'Success!' });
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
      setStatus({ type: 'success', message: t("contactSuccess") || 'Message sent successfully!' });
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
      setStatus({ type: 'error', message: t("contactError") || 'Failed to send message. Please try again.' });
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
      <div className="w-full lg:w-1/2 mt-12 lg:mt-0">
        <motion.div
          initial={{ opacity: 0, scale: 0.95 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-[#0b0f2c]/60 backdrop-blur-md border border-white/10 rounded-2xl p-8 lg:p-12 shadow-2xl text-center"
        >
          <h2 className="text-2xl font-bold text-white mb-4">{t("mustLogin")}</h2>
          <div className="flex justify-center gap-4 mb-8">
            <button 
              onClick={() => { setAuthMode("login"); setStatus(null); }}
              className={`px-6 py-2 rounded-full transition-all ${authMode === 'login' ? 'bg-[#D98B50] text-white' : 'bg-white/10 text-gray-400'}`}
            >
              {t("loginNow")}
            </button>
            <button 
              onClick={() => { setAuthMode("register"); setStatus(null); }}
              className={`px-6 py-2 rounded-full transition-all ${authMode === 'register' ? 'bg-[#D98B50] text-white' : 'bg-white/10 text-gray-400'}`}
            >
              {t("registerNow")}
            </button>
          </div>

          <form onSubmit={handleAuthSubmit} className="flex flex-col gap-6 text-left">
            {status && (
              <div className={`p-4 rounded-md text-sm ${status.type === 'success' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'}`}>
                {status.message}
              </div>
            )}
            
            {authMode === "register" && (
              <div className="flex flex-col gap-2">
                <label className="text-white text-xs opacity-70">{language === 'ar' ? 'الاسم' : 'Name'}</label>
                <input
                  type="text"
                  name="name"
                  value={authData.name}
                  onChange={handleAuthChange}
                  required
                  className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors"
                />
              </div>
            )}

            <div className="flex flex-col gap-2">
              <label className="text-white text-xs opacity-70">{language === 'ar' ? 'البريد الإلكتروني' : 'Email'}</label>
              <input
                type="email"
                name="email"
                value={authData.email}
                onChange={handleAuthChange}
                required
                className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors"
              />
            </div>

            <div className="flex flex-col gap-2">
              <label className="text-white text-xs opacity-70">{language === 'ar' ? 'كلمة المرور' : 'Password'}</label>
              <input
                type="password"
                name="password"
                value={authData.password}
                onChange={handleAuthChange}
                required
                className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors"
              />
            </div>

            {authMode === "register" && (
              <div className="flex flex-col gap-2">
                <label className="text-white text-xs opacity-70">{language === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password'}</label>
                <input
                  type="password"
                  name="password_confirmation"
                  value={authData.password_confirmation}
                  onChange={handleAuthChange}
                  required
                  className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors"
                />
              </div>
            )}

            <button
              type="submit"
              disabled={isSubmitting}
              className="w-full bg-white text-black py-3 rounded-md font-bold mt-4 hover:bg-gray-200 disabled:opacity-50 transition-all"
            >
              {isSubmitting ? '...' : (authMode === 'login' ? t("loginNow") : t("registerNow"))}
            </button>

            <p className="text-gray-400 text-xs text-center mt-2">
              {authMode === 'login' ? t("donthaveAccount") : t("alreadyhaveAccount")}{' '}
              <span 
                onClick={() => setAuthMode(authMode === 'login' ? 'register' : 'login')}
                className="text-[#D98B50] cursor-pointer hover:underline"
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
    <div className="w-full lg:w-1/2 mt-12 lg:mt-0">
      <motion.div
        initial={{ opacity: 0, x: 20 }}
        whileInView={{ opacity: 1, x: 0 }}
        viewport={{ once: true }}
        className="bg-[#0b0f2c]/60 backdrop-blur-md border border-white/10 rounded-2xl p-8 lg:p-12 shadow-2xl"
      >
        <div className="flex justify-between items-center mb-6">
          <p className="text-gray-400 text-sm">Welcome, <span className="text-white font-bold">{user.name}</span></p>
          <button onClick={() => window.location.reload()} className="text-xs text-[#D98B50] hover:underline">Logout</button>
        </div>
        <form
          onSubmit={handleSubmit}
          className="flex flex-col gap-8"
        >
          {status && (
            <div className={`p-4 rounded-md ${status.type === 'success' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'}`}>
              {status.message}
            </div>
          )}
          {/* Row 1 */}
          <div className="flex flex-col md:flex-row gap-8">
            <div className="w-full md:w-1/2 flex flex-col gap-2">
              <label className="text-white text-sm">{t("contactFirstName")}</label>
              <input
                type="text"
                name="firstName"
                value={formData.firstName}
                onChange={handleChange}
                required
                className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors"
                placeholder=""
              />
            </div>
            <div className="w-full md:w-1/2 flex flex-col gap-2">
              <label className="text-white text-sm">{t("contactLastName")}</label>
              <input
                type="text"
                name="lastName"
                value={formData.lastName}
                onChange={handleChange}
                className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors"
                placeholder="Doe"
              />
            </div>
          </div>

          {/* Row 2 */}
          <div className="flex flex-col md:flex-row gap-8">
            <div className="w-full md:w-1/2 flex flex-col gap-2">
              <label className="text-white text-sm">{t("contactEmail")}</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
                className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors"
                placeholder=""
              />
            </div>
            <div className="w-full md:w-1/2 flex flex-col gap-2 relative">
              <label className="text-white text-sm">{t("contactPhone")}</label>
              <input
                type="tel"
                name="phone"
                value={formData.phone}
                onChange={handleChange}
                className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors pr-8"
                placeholder="+1 012 3456 789"
              />
            </div>
          </div>

          {/* Subject Radio Buttons */}
          <div className="flex flex-col gap-4 mt-2">
            <label className="text-white font-semibold">{t("contactSubject")}</label>
            <div className="flex flex-wrap gap-6 text-sm text-gray-300">
              {[
                { value: 'inquiry', label: language === 'ar' ? 'استفسار عام' : 'General Inquiry' },
                { value: 'pricing', label: language === 'ar' ? 'أسعار ومبيعات' : 'Pricing & Sales' },
                { value: 'complaint', label: language === 'ar' ? 'شكاوى' : 'Complaints' },
                { value: 'support', label: language === 'ar' ? 'دعم فني' : 'Technical Support' },
                { value: 'job', label: language === 'ar' ? 'طلب توظيف' : 'Job Application' },
              ].map((sub) => (
                <label key={sub.value} className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="subject"
                    value={sub.value}
                    checked={formData.subject === sub.value}
                    onChange={handleChange}
                    className="accent-blue-500 w-4 h-4"
                  />
                  {sub.label}
                </label>
              ))}
            </div>
          </div>

          {/* Message */}
          <div className="flex flex-col gap-2 mt-4">
            <label className="text-white text-sm">{t("contactMessageLabel")}</label>
            <textarea
              name="message"
              value={formData.message}
              onChange={handleChange}
              required
              className="bg-transparent border-b border-gray-600 focus:border-blue-400 outline-none text-white pb-2 text-sm transition-colors resize-none h-10"
              placeholder={language === 'ar' ? 'اكتب رسالتك هنا..' : 'Write your message..'}
            ></textarea>
          </div>

          {/* Submit Button */}
          <div className="flex justify-end mt-4">
            <button
              type="submit"
              disabled={isSubmitting}
              className="bg-white text-black px-8 py-3 rounded-md font-semibold text-sm hover:bg-gray-200 transition-colors shadow-lg disabled:opacity-50"
            >
              {isSubmitting ? t("contactSending") || 'Sending...' : t("contactSendMessage") || 'Send Message'}
            </button>
          </div>
        </form>
      </motion.div>
    </div>
  );
};

export default ContactForm;
