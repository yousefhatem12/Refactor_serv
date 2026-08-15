"use client";
import React, { useState, useRef, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "@/app/context/LanguageContext";
import { useAuth } from "@/app/context/AuthContext";
import { fetchAPI } from "@/lib/api";
import {
  FaChevronRight,
  FaChevronLeft,
  FaCheckCircle,
  FaUpload,
  FaUser,
  FaBuilding,
  FaPhone,
  FaEnvelope,
  FaGlobe,
  FaMapMarkerAlt,
  FaLaptopCode,
  FaMoneyBillWave,
  FaLink,
  FaTimes,
} from "react-icons/fa";

const QuotationForm = () => {
  const { t, language } = useLanguage();
  const { user, login, register } = useAuth();
  const [authMode, setAuthMode] = useState("login"); // 'login' or 'register'
  const [authData, setAuthData] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });
  const [authStatus, setAuthStatus] = useState(null);

  const [step, setStep] = useState(1);
  const totalSteps = 3;

  const [formData, setFormData] = useState({
    name: "",
    company: "",
    phone: "",
    email: "",
    country: "",
    city: "",
    requestType: "",
    budget: "",
    websiteLink: "",
    contentManagers: "",
    preDesignedLogo: "",
    designImages: "",
    visionGoals: "",
    productBrief: "",
    colorPreferences: "",
    referenceSites: "",
    additionalNotes: "",
  });

  const [prevUser, setPrevUser] = useState(user);
  if (user !== prevUser) {
    setPrevUser(user);
    if (user) {
      setFormData(prev => {
        const nextName = prev.name || user.name || "";
        const nextEmail = prev.email || user.email || "";
        if (prev.name === nextName && prev.email === nextEmail) {
          return prev;
        }
        return {
          ...prev,
          name: nextName,
          email: nextEmail
        };
      });
    }
  }

  const [files, setFiles] = useState([]);
  const [errors, setErrors] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [isLoaded, setIsLoaded] = useState(false);

  // Load initial data from localStorage on mount
  useEffect(() => {
    if (typeof window !== "undefined") {
      try {
        const savedData = localStorage.getItem("quotation_form_data");
        const savedStep = localStorage.getItem("quotation_form_step");
        if (savedData) {
          setFormData(JSON.parse(savedData));
        }
        if (savedStep) {
          setStep(Number(savedStep));
        }
      } catch (e) {
        console.error("Failed to load saved quotation form:", e);
      } finally {
        setIsLoaded(true);
      }
    }
  }, []);

  // Save to localStorage when step or formData changes, but only after initial loading
  useEffect(() => {
    if (!isLoaded) return;
    if (typeof window !== "undefined") {
      try {
        if (isSubmitted) {
          localStorage.removeItem("quotation_form_data");
          localStorage.removeItem("quotation_form_step");
        } else {
          localStorage.setItem("quotation_form_data", JSON.stringify(formData));
          localStorage.setItem("quotation_form_step", String(step));
        }
      } catch (e) {
        console.error("Failed to save quotation form:", e);
      }
    }
  }, [formData, step, isSubmitted, isLoaded]);

  const fileInputRef = useRef(null);

  const isRtl = language === "ar";

  const handleAuthChange = (e) => {
    const { name, value } = e.target;
    setAuthData((prev) => ({ ...prev, [name]: value }));
  };

  const handleAuthSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setAuthStatus(null);
    let res;
    if (authMode === "login") {
      res = await login(authData.email, authData.password);
    } else {
      res = await register(authData.name, authData.email, authData.password, authData.password_confirmation);
    }
    
    if (res.success) {
      setAuthStatus({ type: 'success', message: 'Logged in successfully!' });
    } else {
      setAuthStatus({ type: 'error', message: res.message });
    }
    setIsSubmitting(false);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) {
      setErrors((prev) => ({ ...prev, [name]: "" }));
    }
  };

  const handleSelect = (name, value) => {
    setFormData((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) {
      setErrors((prev) => ({ ...prev, [name]: "" }));
    }
  };

  const validateStep = () => {
    const newErrors = {};
    if (step === 1) {
      if (!formData.name.trim()) {
        newErrors.name = isRtl ? "اسم العميل مطلوب" : "Client Name is required";
      }
      if (!formData.email.trim()) {
        newErrors.email = isRtl ? "البريد الإلكتروني مطلوب" : "Email is required";
      } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
        newErrors.email = isRtl ? "البريد الإلكتروني غير صحيح" : "Invalid email format";
      }
      if (!formData.phone.trim()) {
        newErrors.phone = isRtl ? "رقم الجوال مطلوب" : "Mobile number is required";
      }
    }
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleNext = () => {
    if (validateStep() && step < totalSteps) {
      setStep(step + 1);
    }
  };

  const handlePrev = () => {
    if (step > 1) {
      setStep(step - 1);
    }
  };

  const handleFileChange = (e) => {
    if (e.target.files) {
      const selectedFiles = Array.from(e.target.files);
      // Validate file types: jpeg, png, jpg, gif
      const validFiles = selectedFiles.filter(file => 
        /\.(jpe?g|png|gif)$/i.test(file.name)
      );
      
      if (validFiles.length < selectedFiles.length) {
        alert(isRtl ? "بعض الملفات غير صالحة. الرجاء رفع صور فقط (JPEG, PNG, GIF)" : "Some files were invalid. Please upload images only (JPEG, PNG, GIF)");
      }
      
      setFiles((prev) => [...prev, ...validFiles]);
    }
  };

  const handleRemoveFile = (index) => {
    setFiles((prev) => prev.filter((_, i) => i !== index));
  };

  const handleDragOver = (e) => {
    e.preventDefault();
  };

  const handleDrop = (e) => {
    e.preventDefault();
    if (e.dataTransfer.files) {
      const droppedFiles = Array.from(e.dataTransfer.files);
      const validFiles = droppedFiles.filter(file => 
        /\.(jpe?g|png|gif)$/i.test(file.name)
      );
      
      if (validFiles.length < droppedFiles.length) {
        alert(isRtl ? "بعض الملفات غير صالحة. الرجاء رفع صور فقط (JPEG, PNG, GIF)" : "Some files were invalid. Please upload images only (JPEG, PNG, GIF)");
      }
      
      setFiles((prev) => [...prev, ...validFiles]);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validateStep()) return;

    if (step < totalSteps) {
      handleNext();
      return;
    }

    setIsSubmitting(true);
    setErrors({});

    try {
      const data = new FormData();
      
      data.append("name", formData.name);
      data.append("company", formData.company || "");
      data.append("phone", formData.phone);
      data.append("email", formData.email);
      data.append("country", formData.country || "");
      data.append("city", formData.city || "");
      data.append("request_type", formData.requestType || "");
      data.append("budget", formData.budget || "");
      data.append("website_link", formData.websiteLink || "");
      data.append("content_managers", formData.contentManagers || "");
      data.append("pre_designed_logo", formData.preDesignedLogo || "");
      data.append("design_images", formData.designImages || "");
      data.append("vision_goals", formData.visionGoals || "");
      data.append("product_brief", formData.productBrief || "");
      data.append("color_preferences", formData.colorPreferences || "");
      data.append("reference_sites", formData.referenceSites || "");
      data.append("additional_notes", formData.additionalNotes || "");

      files.forEach((file) => {
        data.append("files[]", file);
      });

      const response = await fetchAPI("/quotations", {
        method: "POST",
        body: data,
      });

      if (response) {
        setIsSubmitted(true);
      } else {
        setErrors({ submit: isRtl ? "حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى." : "An error occurred while sending the request. Please try again." });
      }
    } catch (error) {
      console.error("Failed to submit quotation:", error);
      setErrors({ submit: isRtl ? "فشل الاتصال بالخادم. يرجى المحاولة لاحقاً." : "Server connection failed. Please try again later." });
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleReset = () => {
    setFormData({
      name: "",
      company: "",
      phone: "",
      email: "",
      country: "",
      city: "",
      requestType: "",
      budget: "",
      websiteLink: "",
      contentManagers: "",
      preDesignedLogo: "",
      designImages: "",
      visionGoals: "",
      productBrief: "",
      colorPreferences: "",
      referenceSites: "",
      additionalNotes: "",
    });
    setFiles([]);
    setStep(1);
    setIsSubmitted(false);
  };

  const stepTitles = {
    1: t("quoteStep1Title"),
    2: t("quoteStep2Title"),
    3: t("quoteStep3Title"),
  };

  const renderStepContent = () => {
    switch (step) {
      case 1:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <InputField
              label={t("quoteClientName")}
              name="name"
              value={formData.name}
              onChange={handleChange}
              placeholder={isRtl ? "اكتب اسمك الكامل" : "Enter your full name"}
              icon={<FaUser />}
              error={errors.name}
              required
              isRtl={isRtl}
            />
            <InputField
              label={t("quoteCompanyName")}
              name="company"
              value={formData.company}
              onChange={handleChange}
              placeholder={isRtl ? "اكتب اسم شركتك" : "Enter company name"}
              icon={<FaBuilding />}
              isRtl={isRtl}
            />
            <InputField
              label={t("quoteMobile")}
              name="phone"
              type="tel"
              value={formData.phone}
              onChange={handleChange}
              placeholder="+966 50 000 0000"
              icon={<FaPhone />}
              error={errors.phone}
              required
              isRtl={isRtl}
            />
            <InputField
              label={t("quoteEmail")}
              name="email"
              type="email"
              value={formData.email}
              onChange={handleChange}
              placeholder="example@domain.com"
              icon={<FaEnvelope />}
              error={errors.email}
              required
              isRtl={isRtl}
            />
            <InputField
              label={t("quoteCountry")}
              name="country"
              value={formData.country}
              onChange={handleChange}
              placeholder={isRtl ? "مثال: السعودية" : "e.g. Saudi Arabia"}
              icon={<FaGlobe />}
              isRtl={isRtl}
            />
            <InputField
              label={t("quoteCity")}
              name="city"
              value={formData.city}
              onChange={handleChange}
              placeholder={isRtl ? "مثال: الرياض" : "e.g. Riyadh"}
              icon={<FaMapMarkerAlt />}
              isRtl={isRtl}
            />
          </div>
        );
      case 2:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <InputField
              label={t("quoteRequestType")}
              name="requestType"
              value={formData.requestType}
              onChange={handleChange}
              placeholder={isRtl ? "مثال: موقع تعريفي، متجر إلكتروني" : "e.g. Corporate website, E-commerce Store"}
              icon={<FaLaptopCode />}
              isRtl={isRtl}
            />
            <InputField
              label={t("quoteProposedBudget")}
              name="budget"
              value={formData.budget}
              onChange={handleChange}
              placeholder={isRtl ? "مثال: 5,000 - 10,000 ريال" : "e.g. 5,000 - 10,000 SAR"}
              icon={<FaMoneyBillWave />}
              isRtl={isRtl}
            />
            <InputField
              label={t("quoteWebsiteLink")}
              name="websiteLink"
              value={formData.websiteLink}
              onChange={handleChange}
              placeholder="https://example.com"
              icon={<FaLink />}
              isRtl={isRtl}
            />
            
            <SelectField
              label={t("quoteContentManagers")}
              name="contentManagers"
              value={formData.contentManagers}
              onSelect={handleSelect}
              options={[
                { value: "yes", label: isRtl ? "نعم، لدي من يساعدني" : "Yes, I have content managers" },
                { value: "no", label: isRtl ? "لا، أحتاج مساعدة في إدخال البيانات" : "No, I need data entry support" },
                { value: "self", label: isRtl ? "سأقوم بإدارة المحتوى بنفسي" : "I will manage content myself" },
              ]}
              placeholder={isRtl ? "اختر إجابة..." : "Select an answer..."}
              isRtl={isRtl}
            />

            <SelectField
              label={t("quotePreDesignedLogo")}
              name="preDesignedLogo"
              value={formData.preDesignedLogo}
              onSelect={handleSelect}
              options={[
                { value: "yes", label: isRtl ? "نعم، لدي شعار مصمم" : "Yes, I have a designed logo" },
                { value: "no", label: isRtl ? "لا، أحتاج تصميم شعار جديد" : "No, I need a logo designed" },
              ]}
              placeholder={isRtl ? "اختر إجابة..." : "Select an answer..."}
              isRtl={isRtl}
            />

            <SelectField
              label={t("quoteDesignImages")}
              name="designImages"
              value={formData.designImages}
              onSelect={handleSelect}
              options={[
                { value: "yes", label: isRtl ? "نعم، لدي صور خاصة بالتصميم" : "Yes, I have design images" },
                { value: "no", label: isRtl ? "لا، يرجى توفير صور مناسبة" : "No, please provide suitable images" },
              ]}
              placeholder={isRtl ? "اختر إجابة..." : "Select an answer..."}
              isRtl={isRtl}
            />

            <div className="col-span-1 md:col-span-2 flex flex-col gap-2">
              <label className="text-gray-400 text-sm font-medium px-2">
                {t("quoteUploadFile")}
              </label>
              
              <div
                onDragOver={handleDragOver}
                onDrop={handleDrop}
                onClick={() => fileInputRef.current?.click()}
                className="w-full min-h-[140px] rounded-2xl border-2 border-dashed border-white/10 hover:border-blue-500 bg-white/5 flex flex-col items-center justify-center p-6 text-center cursor-pointer transition-all duration-300 gap-3"
              >
                <input
                  type="file"
                  ref={fileInputRef}
                  onChange={handleFileChange}
                  multiple
                  accept="image/jpeg,image/png,image/jpg,image/gif"
                  className="hidden"
                />
                <FaUpload className="text-3xl text-blue-400" />
                <div>
                  <p className="text-white text-sm font-semibold">
                    {t("quoteUploadDragDrop")}
                  </p>
                  <p className="text-gray-500 text-xs mt-1">
                    {t("quoteUploadAccept")}
                  </p>
                </div>
              </div>

              {files.length > 0 && (
                <div className="mt-3 flex flex-wrap gap-2">
                  {files.map((file, idx) => (
                    <div
                      key={idx}
                      className="flex items-center gap-2 bg-white/10 border border-white/5 rounded-xl px-3 py-1.5 text-xs text-white"
                    >
                      <span className="truncate max-w-[150px]">{file.name}</span>
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          handleRemoveFile(idx);
                        }}
                        className="text-red-400 hover:text-red-300 transition-colors p-0.5 border-none bg-transparent cursor-pointer"
                      >
                        <FaTimes />
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        );
      case 3:
        return (
          <div className="flex flex-col gap-6">
            <TextareaField
              label={t("quoteVisionGoals")}
              name="visionGoals"
              value={formData.visionGoals}
              onChange={handleChange}
              placeholder={isRtl ? "اكتب رؤيتك ورسالتك وأهدافك من الموقع..." : "Describe your vision and desired goals..."}
            />
            <TextareaField
              label={t("quoteProductBrief")}
              name="productBrief"
              value={formData.productBrief}
              onChange={handleChange}
              placeholder={isRtl ? "اكتب نبذة مختصرة عن المنتجات أو الخدمات المطلوبة..." : "Write a brief description of services/products..."}
            />
            <TextareaField
              label={t("quoteColorPreferences")}
              name="colorPreferences"
              value={formData.colorPreferences}
              onChange={handleChange}
              placeholder={isRtl ? "مثال: أفضل اللون الأزرق الفاتح والأسود، وأستبعد اللون الأحمر..." : "e.g. Prefer light blue and black, exclude red..."}
            />
            <TextareaField
              label={t("quoteReferenceSites")}
              name="referenceSites"
              value={formData.referenceSites}
              onChange={handleChange}
              placeholder={isRtl ? "مثال: https://google.com أو مواقع منافسة نالت إعجابك..." : "Paste competitor links or design inspiration sites..."}
            />
            <TextareaField
              label={t("quoteAdditionalNotes")}
              name="additionalNotes"
              value={formData.additionalNotes}
              onChange={handleChange}
              placeholder={isRtl ? "أي إضافات أو ميزات خاصة تود إضافتها للمشروع..." : "Any other features or custom requests..."}
            />
          </div>
        );
      default:
        return null;
    }
  };

  return (
    <div className="relative z-20 max-w-4xl mx-auto px-6 pb-20">
      {user && (
        <div className="flex justify-between items-center max-w-4xl mx-auto mb-6 px-4">
          <p className="text-gray-400 text-sm">
            {isRtl ? "مرحباً، " : "Welcome, "}<span className="text-white font-bold">{user.name}</span>
          </p>
          <button 
            onClick={() => {
              localStorage.removeItem('auth_token');
              window.location.reload();
            }} 
            className="text-xs text-[#FF8A00] hover:underline bg-transparent border-none cursor-pointer font-semibold"
          >
            {isRtl ? "تسجيل الخروج" : "Logout"}
          </button>
        </div>
      )}

      <div className="bg-white/[0.03] backdrop-blur-xl border border-white/10 rounded-[40px] p-5 md:p-12 shadow-2xl relative overflow-hidden">
        
        {/* Progress bar */}
        <div className="absolute top-0 left-0 w-full h-1 bg-white/5">
          <motion.div
            className="h-full bg-gradient-to-r from-blue-500 via-[#FF8A00] to-purple-500"
            initial={{ width: "33.3%" }}
            animate={{ width: `${(step / totalSteps) * 100}%` }}
            transition={{ duration: 0.5 }}
          />
        </div>

        <AnimatePresence mode="wait">
          {!isSubmitted ? (
            <motion.form
              key="quotation-form-content"
              onSubmit={handleSubmit}
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="flex flex-col h-full"
            >
              {/* Header */}
              <div className="flex justify-between items-center mb-6 md:mb-10">
                <h2 className="text-2xl md:text-3xl font-bold text-white">
                  {stepTitles[step]}
                </h2>
                <span className="text-gray-500 font-semibold text-sm">
                  {step} / {totalSteps}
                </span>
              </div>

              {/* Form Content */}
              <div className="min-h-[360px]">
                <AnimatePresence mode="wait">
                  <motion.div
                    key={step}
                    initial={{ opacity: 0, x: isRtl ? -20 : 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: isRtl ? 20 : -20 }}
                    transition={{ duration: 0.3 }}
                  >
                    {renderStepContent()}
                  </motion.div>
                </AnimatePresence>
              </div>

              {errors.submit && (
                <div className="mt-6 p-4 rounded-xl text-sm bg-red-500/20 text-red-400 text-center">
                  {errors.submit}
                </div>
              )}

              {/* Navigation Controls */}
              <div className="flex items-center justify-between mt-8 md:mt-12 border-t border-white/5 pt-6 md:pt-8">
                <button
                  type="button"
                  onClick={handlePrev}
                  disabled={step === 1}
                  className={`flex items-center justify-center gap-2 px-4 py-2.5 md:px-6 md:py-3 rounded-xl border border-white/10 text-white font-medium transition-all whitespace-nowrap text-sm md:text-base ${
                    step === 1 ? "opacity-0 pointer-events-none absolute" : "hover:bg-white/5"
                  }`}
                >
                  {isRtl ? <FaChevronRight /> : <FaChevronLeft />}
                  {t("prevStep")}
                </button>

                {step < totalSteps ? (
                  <button
                    key="next-btn"
                    type="button"
                    onClick={handleNext}
                    className="flex items-center justify-center gap-2 px-6 py-2.5 md:px-8 md:py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all shadow-lg shadow-blue-600/20 cursor-pointer border-none whitespace-nowrap text-sm md:text-base"
                  >
                    {t("nextStep")}
                    {isRtl ? <FaChevronLeft /> : <FaChevronRight />}
                  </button>
                ) : (
                  <button
                    key="submit-btn"
                    type="submit"
                    disabled={isSubmitting}
                    className="px-6 py-2.5 md:px-10 md:py-3 rounded-xl bg-gradient-to-r from-blue-600 to-[#FF8A00] hover:scale-[1.03] text-white font-bold transition-all shadow-xl shadow-blue-600/20 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer border-none whitespace-nowrap text-sm md:text-base"
                  >
                    {isSubmitting ? t("quoteSending") : t("quoteSubmit")}
                  </button>
                )}
              </div>
            </motion.form>
          ) : (
            <motion.div
              key="success-screen"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0 }}
              className="flex flex-col items-center justify-center text-center py-12 px-4"
            >
              <motion.div
                initial={{ scale: 0 }}
                animate={{ scale: 1 }}
                transition={{ type: "spring", damping: 15, delay: 0.2 }}
                className="w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-4xl shadow-xl shadow-green-500/25 mb-8"
              >
                <FaCheckCircle />
              </motion.div>

              <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">
                {t("quoteSuccessTitle")}
              </h2>

              <p className="text-gray-400 text-lg leading-relaxed max-w-lg mb-10">
                {t("quoteSuccessDesc")}
              </p>

              <button
                type="button"
                onClick={handleReset}
                className="px-10 py-3 rounded-xl bg-white text-[#040A37] font-bold hover:bg-gray-200 transition-all shadow-lg hover:scale-105 cursor-pointer border-none"
              >
                {t("close")}
              </button>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </div>
  );
};

/* Sub-components for Form Inputs */

const InputField = ({
  label,
  name,
  value,
  onChange,
  placeholder,
  type = "text",
  icon,
  error,
  required,
  isRtl,
}) => {
  return (
    <div className="flex flex-col gap-2 w-full text-start">
      <label className="text-gray-400 text-sm font-medium px-2 flex items-center gap-1 w-full justify-start">
        {label} {required && <span className="text-red-500">*</span>}
      </label>
      <div className="relative flex items-center w-full">
        {icon && (
          <div className={`absolute text-gray-500 pointer-events-none text-base z-10 ${isRtl ? "right-4" : "left-4"}`}>
            {icon}
          </div>
        )}
        <input
          type={type}
          name={name}
          value={value}
          onChange={onChange}
          placeholder={placeholder}
          className={`w-full py-3 md:py-4 rounded-xl bg-white/5 border text-white transition-all duration-300 outline-none backdrop-blur-md text-sm ${
            error ? "border-red-500/50 focus:border-red-500" : "border-white/10 focus:border-blue-500"
          } ${icon ? (isRtl ? "pr-12 pl-4 text-right" : "pl-12 pr-4 text-left") : "px-4"}`}
          style={{ direction: isRtl ? "rtl" : "ltr" }}
        />
      </div>
      {error && (
        <span className={`text-red-500 text-xs px-2 mt-1 ${isRtl ? "text-right" : "text-left"}`}>
          {error}
        </span>
      )}
    </div>
  );
};

const SelectField = ({
  label,
  name,
  value,
  onSelect,
  options,
  placeholder,
  isRtl,
}) => {
  const [isOpen, setIsOpen] = useState(false);
  const containerRef = useRef(null);

  const selectedOption = options.find((opt) => opt.value === value);

  return (
    <div className="flex flex-col gap-2 w-full text-start relative" ref={containerRef}>
      <label className="text-gray-400 text-sm font-medium px-2 w-full">
        {label}
      </label>
      
      <div className="relative">
        <button
          type="button"
          onClick={() => setIsOpen(!isOpen)}
          className={`w-full p-3 md:p-4 rounded-xl bg-white/5 border border-white/10 text-white transition-all duration-300 outline-none backdrop-blur-md text-sm flex items-center justify-between cursor-pointer ${
            isRtl ? "text-right flex-row-reverse" : "text-left"
          }`}
        >
          <span className={selectedOption ? "text-white" : "text-gray-500"}>
            {selectedOption ? selectedOption.label : placeholder}
          </span>
          <svg
            className={`w-4 h-4 text-gray-500 transition-transform duration-300 ${isOpen ? "rotate-180" : ""}`}
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <AnimatePresence>
          {isOpen && (
            <>
              {/* Overlay to close on outside click */}
              <div className="fixed inset-0 z-30" onClick={() => setIsOpen(false)} />
              
              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: 10 }}
                className="absolute z-40 w-full mt-2 bg-[#0c123d] border border-white/10 rounded-xl overflow-hidden shadow-2xl"
              >
                {options.map((opt) => (
                  <button
                    key={opt.value}
                    type="button"
                    onClick={() => {
                      onSelect(name, opt.value);
                      setIsOpen(false);
                    }}
                    className={`w-full px-4 py-3 text-sm hover:bg-white/5 transition-colors cursor-pointer border-none text-white block ${
                      isRtl ? "text-right" : "text-left"
                    } ${value === opt.value ? "bg-blue-600/20 text-blue-400 font-semibold" : ""}`}
                  >
                    {opt.label}
                  </button>
                ))}
              </motion.div>
            </>
          )}
        </AnimatePresence>
      </div>
    </div>
  );
};

const TextareaField = ({ label, name, value, onChange, placeholder }) => {
  return (
    <div className="flex flex-col gap-2 w-full text-start">
      <label className="text-gray-400 text-sm font-medium px-2 w-full">
        {label}
      </label>
      <textarea
        name={name}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        rows={4}
        className="w-full p-3 md:p-4 rounded-xl bg-white/5 border border-white/10 text-white focus:border-blue-500 transition-colors outline-none resize-none backdrop-blur-md text-sm min-h-[100px]"
      />
    </div>
  );
};

export default QuotationForm;
