"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { fetchAPI } from "@/lib/api";
import { useLanguage } from "@/app/context/LanguageContext";

const PricingModal = ({ isOpen, onClose, serviceId = null }) => {
  const [step, setStep] = useState(1); // 1: Form, 2: Plans
  const [services, setServices] = useState([]);
  const [plans, setPlans] = useState([]);
  const [loading, setLoading] = useState(false);
  const { language } = useLanguage();
  const isRtl = language === "ar";
  const [activeServiceSlug, setActiveServiceSlug] = useState(serviceId || null);

  useEffect(() => {
    if (serviceId) {
      setActiveServiceSlug(serviceId);
    }
  }, [serviceId]);

  useEffect(() => {
    if (isOpen) {
      fetchAPI('/services', { cache: "no-store" })
        .then((data) => {
          if (data && data.length > 0) {
             setServices(data);
             setActiveServiceSlug((prev) => prev || data[0].id);
          }
        })
        .catch(err => console.error("Failed to fetch services", err));
    }
  }, [isOpen, language]);

  useEffect(() => {
    if (isOpen && activeServiceSlug) {
      setLoading(true);
      fetchAPI(`/pricing-plans?service_id=${activeServiceSlug}`, { cache: "no-store" })
        .then((data) => {
          if (data) {
             setPlans(data);
          } else {
             setPlans([]);
          }
          setLoading(false)
        })
        .catch((err) => {
          console.error("Failed to fetch pricing plans", err);
          setLoading(false);
        });
    }
  }, [isOpen, activeServiceSlug, language]);

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6 md:p-10">
          {/* Backdrop */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="absolute inset-0 bg-black/95 backdrop-blur-md cursor-pointer"
          />

          {/* Modal Container */}
          <motion.div
            initial={{ scale: 0.9, opacity: 0, y: 20 }}
            animate={{ scale: 1, opacity: 1, y: 0 }}
            exit={{ scale: 0.9, opacity: 0, y: 20 }}
            transition={{ type: "spring", damping: 25, stiffness: 300 }}
            className="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto bg-[#0a0a0a] rounded-2xl shadow-[0_0_50px_rgba(217,139,80,0.2)] border border-white/10 z-10 custom-scrollbar"
          >
            {/* Close Button */}
            <button
              onClick={onClose}
              className="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all hover:rotate-90 group border-none cursor-pointer"
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
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>

            {/* Content Container */}
            <div className="p-8 md:p-12">
              <div className="text-center mb-12">
                <h2 className="text-3xl md:text-5xl font-bold text-white mb-4">
                  {isRtl ? "خطط الأسعار والاشتراكات" : "Pricing & Subscriptions"}
                </h2>
                <p className="text-gray-400 text-lg max-w-2xl mx-auto mb-8">
                  {isRtl 
                    ? "اختر الخطة المناسبة لاحتياجات عملك وابدأ النجاح اليوم." 
                    : "Choose the right plan for your business needs and start succeeding today."}
                </p>
                
                {/* Services Tabs — only shown when not locked to a specific service */}
                {services.length > 0 && !serviceId && (
                  <div className="flex flex-wrap justify-center gap-3">
                    {services.map(service => (
                      <button
                        key={service.id}
                        onClick={() => setActiveServiceSlug(service.id)}
                        className={`px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300 border-none cursor-pointer ${
                          activeServiceSlug === service.id
                            ? "bg-[#D98B50] text-white shadow-[0_0_15px_rgba(217,139,80,0.4)] transform scale-105"
                            : "bg-white/10 text-gray-300 hover:bg-white/20 hover:text-white"
                        }`}
                      >
                        {service.label}
                      </button>
                    ))}
                  </div>
                )}

                {/* Show current service name as badge when locked */}
                {serviceId && services.length > 0 && (
                  <div className="flex justify-center">
                    <span className="px-6 py-2.5 rounded-full text-sm font-bold bg-[#D98B50] text-white shadow-[0_0_15px_rgba(217,139,80,0.4)]">
                      {services.find(s => s.id === serviceId)?.label || serviceId}
                    </span>
                  </div>
                )}

              </div>

              {loading ? (
                <div className="flex justify-center items-center py-20">
                  <div className="loader"></div>
                </div>
              ) : (
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                >

                  {plans.length === 0 ? (
                    <div className="text-center text-white/50 py-20 text-lg">
                      {isRtl ? "لا توجد خطط متاحة لهذه الخدمة حالياً." : "No pricing plans available for this service right now."}
                    </div>
                  ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-center">
                      {plans.map((plan, idx) => (
                        <motion.div
                          key={plan.id}
                          initial={{ opacity: 0, y: 30 }}
                          animate={{ opacity: 1, y: 0 }}
                          transition={{ delay: idx * 0.1 }}
                          className={`relative flex flex-col p-8 rounded-3xl backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 h-full ${
                            plan.is_popular
                              ? "bg-gradient-to-b from-[#D98B50]/20 to-black border-2 border-[#D98B50] shadow-[0_0_30px_rgba(217,139,80,0.3)] transform lg:scale-105 z-10"
                              : "bg-white/5 border border-white/10 hover:border-white/20"
                          }`}
                        >
                          {plan.is_popular && (
                            <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#D98B50] text-white px-4 py-1 rounded-full text-sm font-bold tracking-wide shadow-lg whitespace-nowrap">
                              {isRtl ? "الأكثر طلباً" : "MOST POPULAR"}
                            </div>
                          )}

                          <div className="mb-8">
                            <h3 className="text-2xl font-bold text-white mb-2">{plan.name}</h3>
                            <div className="flex items-end gap-1">
                              <span className="text-4xl font-extrabold text-white">
                                {plan.currency}
                                {plan.price}
                              </span>
                              {plan.period && <span className="text-gray-400 mb-1">{plan.period}</span>}
                            </div>
                          </div>

                          <div className="flex-grow">
                            <ul className="space-y-4 mb-8">
                              {plan.features?.map((feature, fIdx) => (
                                <li key={fIdx} className="flex items-start gap-3 text-gray-300">
                                  <svg
                                    className="w-5 h-5 text-[#D98B50] shrink-0 mt-0.5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    strokeWidth="3"
                                  >
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                  </svg>
                                  <span>{feature}</span>
                                </li>
                              ))}
                            </ul>
                          </div>

                          <a
                            href={plan.button_url || "/contact-us"}
                            className={`w-full py-4 rounded-xl font-bold text-center transition-all duration-300 block ${
                              plan.is_popular
                                ? "bg-[#D98B50] text-white hover:bg-[#c47a45] shadow-lg hover:shadow-[#D98B50]/50"
                                : "bg-white/10 text-white hover:bg-white/20"
                            }`}
                          >
                            {plan.button_text || (isRtl ? "اشترك الآن" : "Subscribe Now")}
                          </a>
                        </motion.div>
                      ))}
                    </div>
                  )}
                </motion.div>
              )}
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
};

export default PricingModal;
