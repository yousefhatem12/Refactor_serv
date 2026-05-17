"use client";
import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../../context/LanguageContext";
import { FaMobileAlt, FaLaptopCode, FaCogs, FaLightbulb, FaCheckCircle, FaChevronRight, FaChevronLeft } from "react-icons/fa";

const ConsultationForm = () => {
  const { t, language } = useLanguage();
  const [step, setStep] = useState(1);
  const [formData, setFormData] = useState({
    projectType: "",
    hasDocs: "",
    budget: "",
    timeline: "",
    decisionMaker: "",
    references: "",
    name: "",
    phone: "",
    email: "",
    company: "",
  });

  const totalSteps = 7;

  const handleNext = () => {
    if (step < totalSteps) setStep(step + 1);
  };

  const handlePrev = () => {
    if (step > 1) setStep(step - 1);
  };

  const handleSelect = (field, value) => {
    setFormData({ ...formData, [field]: value });
    // Auto-advance for simple selections
    if (step < 6) setTimeout(handleNext, 300);
  };

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const renderStep = () => {
    switch (step) {
      case 1:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {[
              { id: "mobile", label: t("typeMobile"), icon: <FaMobileAlt /> },
              { id: "website", label: t("typeWebsite"), icon: <FaLaptopCode /> },
              { id: "erp", label: t("typeERP"), icon: <FaCogs /> },
              { id: "other", label: t("typeOther"), icon: <FaLightbulb /> },
            ].map((item) => (
              <SelectionCard
                key={item.id}
                selected={formData.projectType === item.id}
                onClick={() => handleSelect("projectType", item.id)}
                icon={item.icon}
                label={item.label}
              />
            ))}
          </div>
        );
      case 2:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {[
              { id: "yes", label: t("docYes") },
              { id: "no", label: t("docNo") },
            ].map((item) => (
              <SelectionCard
                key={item.id}
                selected={formData.hasDocs === item.id}
                onClick={() => handleSelect("hasDocs", item.id)}
                label={item.label}
              />
            ))}
          </div>
        );
      case 3:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {[
              { id: "under5k", label: t("budgetUnder5k") },
              { id: "12to35", label: t("budget12to35") },
              { id: "35to80", label: t("budget35to80") },
              { id: "over80", label: t("budgetOver80") },
            ].map((item) => (
              <SelectionCard
                key={item.id}
                selected={formData.budget === item.id}
                onClick={() => handleSelect("budget", item.id)}
                label={item.label}
              />
            ))}
          </div>
        );
      case 4:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {[
              { id: "1week", label: t("time1Week") },
              { id: "1month", label: t("time1Month") },
              { id: "3months", label: t("time3Months") },
              { id: "none", label: t("timeNone") },
            ].map((item) => (
              <SelectionCard
                key={item.id}
                selected={formData.timeline === item.id}
                onClick={() => handleSelect("timeline", item.id)}
                label={item.label}
              />
            ))}
          </div>
        );
      case 5:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {[
              { id: "me", label: t("decisionMe") },
              { id: "team", label: t("decisionTeam") },
              { id: "company", label: t("decisionCompany") },
            ].map((item) => (
              <SelectionCard
                key={item.id}
                selected={formData.decisionMaker === item.id}
                onClick={() => handleSelect("decisionMaker", item.id)}
                label={item.label}
              />
            ))}
          </div>
        );
      case 6:
        return (
          <div className="w-full">
            <textarea
              name="references"
              value={formData.references}
              onChange={handleChange}
              placeholder={t("refPlaceholder")}
              className="w-full h-40 p-4 rounded-2xl bg-white/5 border border-white/10 text-white focus:border-blue-500 transition-colors outline-none resize-none backdrop-blur-md"
            />
          </div>
        );
      case 7:
        return (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InputField
              label={t("contactFirstName")}
              name="name"
              value={formData.name}
              onChange={handleChange}
              placeholder={t("namePlaceholder")}
            />
            <InputField
              label={t("contactPhone")}
              name="phone"
              value={formData.phone}
              onChange={handleChange}
              placeholder={t("phonePlaceholder")}
            />
            <InputField
              label={t("contactEmail")}
              name="email"
              type="email"
              value={formData.email}
              onChange={handleChange}
              placeholder={t("emailPlaceholder")}
            />
            <InputField
              label={t("company")}
              name="company"
              value={formData.company}
              onChange={handleChange}
              placeholder={t("company")}
            />
          </div>
        );
      default:
        return null;
    }
  };

  return (
    <div className="relative z-20 max-w-4xl mx-auto px-6 pb-20">
      <div className="bg-white/[0.03] backdrop-blur-xl border border-white/10 rounded-[40px] p-8 md:p-12 shadow-2xl relative overflow-hidden">
        {/* Progress bar */}
        <div className="absolute top-0 left-0 w-full h-1 bg-white/5">
          <motion.div
            className="h-full bg-gradient-to-r from-blue-500 to-purple-500"
            initial={{ width: "0%" }}
            animate={{ width: `${(step / totalSteps) * 100}%` }}
            transition={{ duration: 0.5 }}
          />
        </div>

        <div className="flex justify-between items-center mb-10">
          <h2 className="text-2xl md:text-3xl font-bold text-white">
            {t(`consultationStep${step}Title`)}
          </h2>
          <span className="text-gray-500 font-medium">
            {step} / {totalSteps}
          </span>
        </div>

        <div className="min-h-[300px]">
          <AnimatePresence mode="wait">
            <motion.div
              key={step}
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              transition={{ duration: 0.3 }}
            >
              {renderStep()}
            </motion.div>
          </AnimatePresence>
        </div>

        <div className="flex items-center justify-between mt-12 border-t border-white/5 pt-8">
          <button
            onClick={handlePrev}
            disabled={step === 1}
            className={`flex items-center gap-2 px-6 py-3 rounded-xl border border-white/10 text-white font-medium transition-all ${
              step === 1 ? "opacity-0 pointer-events-none" : "hover:bg-white/5"
            }`}
          >
            {language === "ar" ? <FaChevronRight /> : <FaChevronLeft />}
            {t("prevStep")}
          </button>

          {step < totalSteps ? (
            <button
              onClick={handleNext}
              className="flex items-center gap-2 px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all shadow-lg shadow-blue-600/20"
            >
              {t("nextStep")}
              {language === "ar" ? <FaChevronLeft /> : <FaChevronRight />}
            </button>
          ) : (
            <button
              className="px-10 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 hover:scale-105 text-white font-bold transition-all shadow-xl shadow-purple-600/20"
            >
              {t("submitConsultation")}
            </button>
          )}
        </div>
      </div>
    </div>
  );
};

const SelectionCard = ({ selected, onClick, icon, label }) => {
  return (
    <motion.div
      whileHover={{ scale: 1.02 }}
      whileTap={{ scale: 0.98 }}
      onClick={onClick}
      className={`relative cursor-pointer p-6 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-4 text-center ${
        selected
          ? "bg-blue-600/10 border-blue-500 shadow-[0_0_20px_rgba(59,130,246,0.2)]"
          : "bg-white/5 border-white/5 hover:border-white/20"
      }`}
    >
      {selected && (
        <div className="absolute top-3 right-3 text-blue-500">
          <FaCheckCircle />
        </div>
      )}
      {icon && <div className="text-3xl text-blue-400">{icon}</div>}
      <span className={`text-lg font-medium ${selected ? "text-white" : "text-gray-400"}`}>
        {label}
      </span>
    </motion.div>
  );
};

const InputField = ({ label, name, value, onChange, placeholder, type = "text" }) => {
  return (
    <div className="flex flex-col gap-2">
      <label className="text-gray-400 text-sm font-medium px-2">{label}</label>
      <input
        type={type}
        name={name}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        className="w-full p-4 rounded-xl bg-white/5 border border-white/10 text-white focus:border-blue-500 transition-colors outline-none backdrop-blur-md"
      />
    </div>
  );
};

export default ConsultationForm;
