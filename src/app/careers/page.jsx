"use client";
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../context/LanguageContext";
import { fetchAPI } from "../../lib/api";
import Loading from "../loading";

export default function CareersPage() {
  const { language, t } = useLanguage();
  const isAr = language === "ar";

  // State variables
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedDept, setSelectedDept] = useState("all");
  const [selectedJob, setSelectedJob] = useState(null); // Active job for modal/drawer
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showSuccess, setShowSuccess] = useState(false);
  const [jobsList, setJobsList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isInitialLoading, setIsInitialLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [isLoadingMore, setIsLoadingMore] = useState(false);
  const [serverDepts, setServerDepts] = useState([]);

  // Form State
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    phone: "",
    portfolio: "",
    message: ""
  });
  const [formErrors, setFormErrors] = useState({});

  // Debounced search and filter effect
  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      setLoading(true);
      setCurrentPage(1);

      const deptQuery = selectedDept === "all" ? "all" : selectedDept;
      const searchQueryParam = searchQuery ? encodeURIComponent(searchQuery) : "";

      fetchAPI(`/careers?lang=${language}&page=1&per_page=9&department=${deptQuery}&search=${searchQueryParam}`)
        .then((res) => {
          if (res && Array.isArray(res.data)) {
            setJobsList(res.data);
            setHasMore(res.has_more);
            if (res.departments) {
              setServerDepts(res.departments);
            }
          } else {
            setJobsList([]);
            setHasMore(false);
          }
        })
        .catch((err) => {
          console.error("Failed to fetch careers:", err);
          setJobsList([]);
          setHasMore(false);
        })
        .finally(() => {
          setLoading(false);
          setIsInitialLoading(false);
        });
    }, 300);

    return () => clearTimeout(delayDebounceFn);
  }, [selectedDept, searchQuery, language]);

  if (isInitialLoading) {
    return <Loading />;
  }

  // Load more jobs
  const loadMoreJobs = () => {
    if (isLoadingMore || !hasMore) return;
    setIsLoadingMore(true);
    const nextPage = currentPage + 1;
    const deptQuery = selectedDept === "all" ? "all" : selectedDept;
    const searchQueryParam = searchQuery ? encodeURIComponent(searchQuery) : "";

    fetchAPI(`/careers?lang=${language}&page=${nextPage}&per_page=9&department=${deptQuery}&search=${searchQueryParam}`)
      .then((res) => {
        if (res && Array.isArray(res.data)) {
          setJobsList((prev) => {
            const newJobs = res.data.filter((item) => !prev.some((j) => j.id === item.id));
            return [...prev, ...newJobs];
          });
          setHasMore(res.has_more);
          setCurrentPage(nextPage);
        }
      })
      .catch((err) => {
        console.error("Failed to load more careers:", err);
      })
      .finally(() => {
        setIsLoadingMore(false);
      });
  };

  // Departments list for filtering
  const departments = [
    { id: "all", name: t("departmentAll") },
    ...serverDepts.map(dept => {
      const translationKey = `dep${dept}`;
      const translatedName = t(translationKey) !== translationKey ? t(translationKey) : dept;
      return { id: dept, name: translatedName };
    })
  ];

  // Handle Form Change
  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
    if (formErrors[name]) {
      setFormErrors((prev) => ({ ...prev, [name]: null }));
    }
  };

  // Validate and Submit
  const handleFormSubmit = (e) => {
    e.preventDefault();
    const errors = {};

    if (!formData.name.trim()) errors.name = t("nameRequired");
    if (!formData.email.trim()) {
      errors.email = t("emailRequired");
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      errors.email = t("emailInvalid");
    }
    if (!formData.phone.trim()) errors.phone = t("phoneRequired");
    if (!formData.portfolio.trim()) {
      errors.portfolio = t("portfolioRequired");
    }

    if (Object.keys(errors).length > 0) {
      setFormErrors(errors);
      return;
    }

    setIsSubmitting(true);

    fetchAPI('/careers/apply', {
      method: 'POST',
      body: JSON.stringify({
        career_id: selectedJob.id,
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        portfolio: formData.portfolio,
        message: formData.message,
      }),
    })
      .then((res) => {
        setIsSubmitting(false);
        if (res) {
          setShowSuccess(true);
          setFormData({
            name: "",
            email: "",
            phone: "",
            portfolio: "",
            message: ""
          });
          setFormErrors({});
        } else {
          setFormErrors({ submit: t("submitError") });
        }
      })
      .catch((err) => {
        console.error("Error submitting application:", err);
        setIsSubmitting(false);
        setFormErrors({ submit: t("connectionError") });
      });
  };

  return (
    <main className="min-h-screen pt-32 pb-24 flex flex-col items-center overflow-x-hidden text-white relative">
      {/* Visual background glows */}
      <div className="absolute top-0 left-1/4 w-[500px] h-[500px] bg-[#FF8A00]/5 rounded-full filter blur-[120px] pointer-events-none z-0" />
      <div className="absolute top-1/2 right-1/4 w-[600px] h-[600px] bg-blue-500/5 rounded-full filter blur-[150px] pointer-events-none z-0" />

      <div className="w-full max-w-[1200px] mx-auto px-6 md:px-10 relative z-10 flex flex-col items-center">
        
        {/* Hero Headers */}
        <div className="text-center flex flex-col items-center gap-4 max-w-3xl mb-12">
          <span className="px-4 py-1.5 rounded-full text-xs font-bold bg-[#FF8A00]/10 border border-[#FF8A00]/30 text-[#FF8A00] shadow-[0_0_15px_rgba(255,138,0,0.1)] uppercase tracking-widest">
            {t("careers")}
          </span>
          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
            className="text-3xl md:text-5xl lg:text-6xl font-black leading-tight tracking-tight text-white"
          >
            {t("careersHeroTitle")}
          </motion.h1>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.1 }}
            className="text-white/75 text-sm md:text-base lg:text-lg leading-relaxed font-light mt-2"
          >
            {t("careersHeroDesc")}
          </motion.p>
        </div>

        {/* Search & Department Filters Block */}
        <div className="w-full flex flex-col md:flex-row gap-4 items-center justify-between p-4 rounded-2xl bg-white/[0.02] border border-white/5 backdrop-blur-md mb-12 shadow-2xl">
          {/* Search bar */}
          <div className="relative w-full md:max-w-md">
            <span className={`absolute inset-y-0 ${isAr ? "left-4" : "right-4"} flex items-center`}>
              <svg className="w-5 h-5 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
            <input
              type="text"
              placeholder={t("searchPlaceholder")}
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className={`w-full px-5 py-3.5 bg-black/40 border border-white/10 rounded-xl text-sm focus:outline-none focus:border-[#FF8A00]/50 transition-all text-white placeholder-white/30 ${isAr ? "pl-12" : "pr-12"}`}
            />
          </div>

          {/* Department filter buttons */}
          <div className="flex flex-wrap gap-2 justify-center md:justify-end w-full md:w-auto mt-2 md:mt-0">
            {departments.map((dept) => (
              <button
                key={dept.id}
                onClick={() => setSelectedDept(dept.id)}
                className={`px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition-all cursor-pointer ${
                  selectedDept === dept.id
                    ? "bg-[#FF8A00] text-white shadow-[0_4px_15px_rgba(255,138,0,0.25)]"
                    : "bg-white/[0.03] border border-white/5 text-white/70 hover:text-white hover:bg-white/5"
                }`}
              >
                {dept.name}
              </button>
            ))}
          </div>
        </div>

        {/* Jobs Grid */}
        <AnimatePresence mode="wait">
          {loading ? (
            <motion.div
              key="loading"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="flex items-center justify-center py-20"
            >
              <div className="w-12 h-12 border-4 border-[#FF8A00] border-t-transparent rounded-full animate-spin"></div>
            </motion.div>
          ) : jobsList.length > 0 ? (
            <motion.div
              layout
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="grid grid-cols-1 md:grid-cols-2 gap-6 w-full"
            >
              {jobsList.map((job, idx) => {
                const title = job.title?.[language] || job.title?.en || "";
                const desc = job.description?.[language] || job.description?.en || "";
                const loc = job.location?.[language] || job.location?.en || "";
                const sal = job.salary?.[language] || job.salary?.en || "";

                return (
                  <motion.div
                    key={job.id}
                    layout
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.4, delay: (idx % 9) * 0.05 }}
                    className="p-6 rounded-2xl bg-white/[0.01] hover:bg-white/[0.03] border border-white/5 hover:border-[#FF8A00]/30 transition-all duration-300 flex flex-col h-full shadow-lg group relative overflow-hidden"
                  >
                    {/* Glowing corner hover decorator */}
                    <div className="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#FF8A00]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-bl-full pointer-events-none" />

                    {/* Card Header info */}
                    <div className="flex justify-between items-start mb-4">
                      <div className="flex flex-col gap-2">
                        <span className="px-3 py-1 rounded-lg text-[10px] font-bold bg-[#FF8A00]/10 text-[#FF8A00] border border-[#FF8A00]/20 tracking-wider uppercase w-max">
                          {job.department}
                        </span>
                        <h3 className="text-xl font-bold text-white group-hover:text-[#FF8A00] transition-colors leading-tight">
                          {title}
                        </h3>
                      </div>
                      
                      {/* Active Status Badge */}
                      <span className="flex items-center gap-1.5 bg-green-500/10 border border-green-500/20 text-green-400 text-[10px] font-semibold px-2.5 py-1 rounded-full shadow-[0_0_10px_rgba(34,197,94,0.05)]">
                        <span className="w-2 h-2 rounded-full bg-green-400 animate-pulse" />
                        {t("statusAvailable")}
                      </span>
                    </div>

                    {/* Description Paragraph */}
                    <p className="text-white/60 text-xs md:text-sm leading-relaxed mb-6 flex-grow line-clamp-3 font-light">
                      {desc}
                    </p>

                    {/* Job metadata footer inside card */}
                    <div className="border-t border-white/5 pt-4 mt-auto flex flex-wrap gap-4 text-xs text-white/50 items-center justify-between">
                      <div className="flex gap-4 flex-wrap">
                        {/* Location indicator */}
                        <span className="flex items-center gap-1.5">
                          <svg className="w-4 h-4 text-[#FF8A00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path strokeLinecap="round" strokeLinejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                          </svg>
                          {loc}
                        </span>

                        {/* Salary indicator */}
                        <span className="flex items-center gap-1.5">
                          <svg className="w-4 h-4 text-[#FF8A00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          {sal}
                        </span>
                      </div>

                      {/* View Details CTA Button */}
                      <button
                        onClick={() => setSelectedJob(job)}
                        className="text-[#FF8A00] font-bold text-xs flex items-center gap-1.5 group-hover:gap-2.5 transition-all duration-300 cursor-pointer bg-transparent border-none p-0"
                      >
                        <span>{t("jobDetails")}</span>
                        <svg className={`w-4 h-4 transform ${isAr ? "rotate-180" : ""}`} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                      </button>
                    </div>
                  </motion.div>
                );
              })}
              {isLoadingMore && [1, 2, 3].map((i) => (
                <div
                  key={`skeleton-more-${i}`}
                  className="p-6 rounded-2xl bg-white/[0.01] border border-white/5 animate-pulse aspect-video min-h-[300px]"
                />
              ))}
            </motion.div>
          ) : (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="py-20 flex flex-col items-center justify-center gap-4 text-center max-w-md bg-white/[0.01] border border-white/5 rounded-2xl p-8 w-full shadow-lg"
            >
              <svg className="w-12 h-12 text-[#FF8A00]/60 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <h3 className="text-xl font-bold text-white">
                {t("noMatchingJobs")}
              </h3>
              <p className="text-white/50 text-xs md:text-sm leading-relaxed font-light">
                {t("noMatchingJobsDesc")}
              </p>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Load More Button */}
        {hasMore && (
          <div className="flex justify-center mt-12 w-full">
            <button
              onClick={loadMoreJobs}
              disabled={isLoadingMore}
              className="flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-[#FF8A00] to-[#e07b00] hover:from-[#e07b00] hover:to-[#FF8A00] disabled:bg-gray-700 text-white font-bold rounded-xl shadow-[0_0_25px_rgba(255,138,0,0.2)] hover:shadow-[0_0_35px_rgba(255,138,0,0.35)] disabled:shadow-none transition-all duration-300 cursor-pointer border border-[#FF8A00]/40 text-sm tracking-wide transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isLoadingMore ? (
                <svg className="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
              ) : null}
              <span>{isLoadingMore ? (t("loading") || (language === 'ar' ? 'جاري التحميل...' : 'Loading...')) : t("loadMore")}</span>
            </button>
          </div>
        )}
      </div>

      {/* Slide-out / Expandable Job Details & Application Drawer */}
      <AnimatePresence>
        {selectedJob && (
          <>
            {/* Backdrop */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setSelectedJob(null)}
              className="fixed inset-0 bg-black/75 backdrop-blur-sm z-[2000] cursor-pointer"
            />

            {/* Main Drawer Panel */}
            <motion.div
              initial={{ x: isAr ? "-100%" : "100%" }}
              animate={{ x: 0 }}
              exit={{ x: isAr ? "-100%" : "100%" }}
              transition={{ type: "spring", damping: 25, stiffness: 220 }}
              className={`fixed top-0 ${isAr ? "left-0" : "right-0"} w-full md:w-[600px] h-screen bg-[#05050f] border-${isAr ? "r" : "l"} border-white/10 z-[2001] shadow-2xl flex flex-col overflow-y-auto`}
            >
              {/* Drawer Header Controls */}
              <div className="sticky top-0 bg-[#05050f]/90 backdrop-blur-md border-b border-white/5 p-6 flex justify-between items-center z-10">
                <div className="flex flex-col gap-1 items-start text-start">
                  <span className="px-3 py-1 rounded bg-[#FF8A00]/10 text-[#FF8A00] border border-[#FF8A00]/20 text-[10px] font-bold tracking-wider uppercase">
                    {selectedJob.department}
                  </span>
                  <h2 className="text-xl md:text-2xl font-black text-white leading-tight mt-1">
                    {selectedJob.title?.[language] || selectedJob.title?.en || ""}
                  </h2>
                </div>

                {/* Close Button */}
                <button
                  onClick={() => setSelectedJob(null)}
                  className="w-10 h-10 bg-white/5 hover:bg-[#FF8A00] text-white rounded-full flex items-center justify-center border border-white/10 hover:border-transparent transition-all duration-300 cursor-pointer"
                >
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              {/* Drawer Scrollable Content */}
              <div className="p-6 md:p-8 flex flex-col gap-8 text-start">
                
                {/* Meta details list */}
                <div className="grid grid-cols-2 gap-4 p-4 rounded-xl bg-white/[0.01] border border-white/5">
                  <div className="flex flex-col gap-1">
                    <span className="text-[10px] text-white/40 uppercase tracking-widest font-semibold">{t("jobLocation")}</span>
                    <span className="text-sm font-medium text-white/90 flex items-center gap-1.5">
                      <svg className="w-4 h-4 text-[#FF8A00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      </svg>
                      {selectedJob.location?.[language] || selectedJob.location?.en || ""}
                    </span>
                  </div>
                  <div className="flex flex-col gap-1">
                    <span className="text-[10px] text-white/40 uppercase tracking-widest font-semibold">{t("jobSalary")}</span>
                    <span className="text-sm font-medium text-white/90 flex items-center gap-1.5">
                      <svg className="w-4 h-4 text-[#FF8A00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2" />
                      </svg>
                      {selectedJob.salary?.[language] || selectedJob.salary?.en || ""}
                    </span>
                  </div>
                </div>

                {/* Role Description */}
                <div className="flex flex-col gap-3">
                  <h4 className="text-base font-bold text-[#FF8A00] flex items-center gap-2">
                    <span className="w-1.5 h-4 bg-[#FF8A00] rounded-full" />
                    {t("roleOverview")}
                  </h4>
                  <p className="text-white/80 text-sm leading-relaxed font-light">
                    {selectedJob.description?.[language] || selectedJob.description?.en || ""}
                  </p>
                </div>

                {/* Key Responsibilities */}
                <div className="flex flex-col gap-3">
                  <h4 className="text-base font-bold text-[#FF8A00] flex items-center gap-2">
                    <span className="w-1.5 h-4 bg-[#FF8A00] rounded-full" />
                    {t("keyResponsibilities")}
                  </h4>
                  <ul className="flex flex-col gap-2.5 list-none pl-0">
                    {(selectedJob.responsibilities?.[language] || selectedJob.responsibilities?.en || []).map((resp, idx) => {
                      const respText = typeof resp === 'object' ? (resp.responsibility || '') : resp;
                      return (
                        <li key={idx} className="text-white/75 text-sm leading-relaxed font-light flex items-start gap-3">
                          <span className="flex-shrink-0 w-5 h-5 rounded-full bg-[#FF8A00]/10 border border-[#FF8A00]/30 text-[#FF8A00] flex items-center justify-center text-[10px] font-bold mt-0.5">
                            {idx + 1}
                          </span>
                          <span>{respText}</span>
                        </li>
                      );
                    })}
                  </ul>
                </div>

                {/* Key Requirements */}
                <div className="flex flex-col gap-3">
                  <h4 className="text-base font-bold text-[#FF8A00] flex items-center gap-2">
                    <span className="w-1.5 h-4 bg-[#FF8A00] rounded-full" />
                    {t("skillsRequirements")}
                  </h4>
                  <ul className="flex flex-col gap-2.5 list-none pl-0">
                    {(selectedJob.requirements?.[language] || selectedJob.requirements?.en || []).map((req, idx) => {
                      const reqText = typeof req === 'object' ? (req.requirement || '') : req;
                      return (
                        <li key={idx} className="text-white/75 text-sm leading-relaxed font-light flex items-start gap-3">
                          <span className="flex-shrink-0 w-2 h-2 rounded-full bg-[#FF8A00] mt-2 shadow-[0_0_8px_rgba(255,138,0,0.8)]" />
                          <span>{reqText}</span>
                        </li>
                      );
                    })}
                  </ul>
                </div>

                {/* Dynamic application form inside drawer */}
                <div className="border-t border-white/10 pt-8 mt-4 flex flex-col gap-6">
                  <div className="flex flex-col gap-2">
                    <h3 className="text-lg font-black text-white">{t("applyNow")}</h3>
                    <p className="text-xs text-white/50 font-light">
                      {t("applyFormDesc")}
                    </p>
                  </div>

                  <form onSubmit={handleFormSubmit} className="flex flex-col gap-5">
                    {/* Full Name */}
                    <div className="flex flex-col gap-2">
                      <label className="text-xs text-white/60 font-semibold">{t("appName")}</label>
                      <input
                        type="text"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        className={`px-4 py-3 bg-black/40 border ${formErrors.name ? "border-red-500/50 focus:border-red-500" : "border-white/10 focus:border-[#FF8A00]/50"} rounded-xl text-sm focus:outline-none transition-all text-white`}
                        placeholder={t("fullNamePlaceholder")}
                      />
                      {formErrors.name && <span className="text-red-400 text-[10px] font-bold mt-1">{formErrors.name}</span>}
                    </div>

                    {/* Contact row (Email + Phone) */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      {/* Email */}
                      <div className="flex flex-col gap-2">
                        <label className="text-xs text-white/60 font-semibold">{t("appEmail")}</label>
                        <input
                          type="email"
                          name="email"
                          value={formData.email}
                          onChange={handleInputChange}
                          className={`px-4 py-3 bg-black/40 border ${formErrors.email ? "border-red-500/50 focus:border-red-500" : "border-white/10 focus:border-[#FF8A00]/50"} rounded-xl text-sm focus:outline-none transition-all text-white`}
                          placeholder="example@mail.com"
                        />
                        {formErrors.email && <span className="text-red-400 text-[10px] font-bold mt-1">{formErrors.email}</span>}
                      </div>

                      {/* Phone */}
                      <div className="flex flex-col gap-2">
                        <label className="text-xs text-white/60 font-semibold">{t("appPhone")}</label>
                        <input
                          type="tel"
                          name="phone"
                          value={formData.phone}
                          onChange={handleInputChange}
                          className={`px-4 py-3 bg-black/40 border ${formErrors.phone ? "border-red-500/50 focus:border-red-500" : "border-white/10 focus:border-[#FF8A00]/50"} rounded-xl text-sm focus:outline-none transition-all text-white`}
                          placeholder="01xxxxxxxxx"
                        />
                        {formErrors.phone && <span className="text-red-400 text-[10px] font-bold mt-1">{formErrors.phone}</span>}
                      </div>
                    </div>

                    {/* CV / Portfolio Link */}
                    <div className="flex flex-col gap-2">
                      <label className="text-xs text-white/60 font-semibold">{t("appPortfolio")}</label>
                      <input
                        type="url"
                        name="portfolio"
                        value={formData.portfolio}
                        onChange={handleInputChange}
                        className={`px-4 py-3 bg-black/40 border ${formErrors.portfolio ? "border-red-500/50 focus:border-red-500" : "border-white/10 focus:border-[#FF8A00]/50"} rounded-xl text-sm focus:outline-none transition-all text-white`}
                        placeholder="https://drive.google.com/... or https://behance.net/..."
                      />
                      {formErrors.portfolio && <span className="text-red-400 text-[10px] font-bold mt-1">{formErrors.portfolio}</span>}
                    </div>

                    {/* Why hire you / message */}
                    <div className="flex flex-col gap-2">
                      <label className="text-xs text-white/60 font-semibold">{t("appMessage")}</label>
                      <textarea
                        name="message"
                        rows="4"
                        value={formData.message}
                        onChange={handleInputChange}
                        className="px-4 py-3 bg-black/40 border border-white/10 focus:border-[#FF8A00]/50 rounded-xl text-sm focus:outline-none transition-all text-white resize-none"
                        placeholder={t("messagePlaceholder")}
                      />
                    </div>

                    {formErrors.submit && <span className="text-red-400 text-xs font-bold text-center">{formErrors.submit}</span>}

                    {/* Submit button */}
                    <button
                      type="submit"
                      disabled={isSubmitting}
                      className="w-full mt-2 py-4 bg-[#FF8A00] hover:bg-[#d17604] disabled:bg-gray-700 text-white rounded-xl font-bold shadow-[0_0_30px_rgba(255,138,0,0.25)] hover:shadow-[0_0_40px_rgba(255,138,0,0.4)] disabled:shadow-none transition-all flex items-center justify-center gap-3 cursor-pointer border-none"
                    >
                      {isSubmitting ? (
                        <>
                          <svg className="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                          </svg>
                          <span>{t("contactSending")}</span>
                        </>
                      ) : (
                        <span>{t("submitApplication")}</span>
                      )}
                    </button>
                  </form>
                </div>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>

      {/* Premium Framer Motion Success Popup Modal */}
      <AnimatePresence>
        {showSuccess && (
          <div className="fixed inset-0 flex items-center justify-center z-[3000] p-6">
            {/* Dark glass backdrop */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setShowSuccess(false)}
              className="fixed inset-0 bg-black/85 backdrop-blur-md cursor-pointer"
            />

            {/* Content card */}
            <motion.div
              initial={{ scale: 0.9, opacity: 0, y: 20 }}
              animate={{ scale: 1, opacity: 1, y: 0 }}
              exit={{ scale: 0.9, opacity: 0, y: 20 }}
              transition={{ type: "spring", damping: 25, stiffness: 220 }}
              className="relative bg-[#0b0b14] border border-white/10 rounded-2xl p-8 max-w-md w-full shadow-3xl text-center flex flex-col items-center gap-6 z-10 overflow-hidden"
            >
              {/* Neon border pulse accent decoration */}
              <div className="absolute inset-0 bg-gradient-to-tr from-[#FF8A00]/5 via-transparent to-blue-500/5 pointer-events-none" />

              {/* Animated Glowing Checkmark Circle */}
              <div className="w-16 h-16 rounded-full bg-[#FF8A00]/10 border border-[#FF8A00]/30 flex items-center justify-center shadow-[0_0_20px_rgba(255,138,0,0.2)] animate-pulse">
                <svg className="w-8 h-8 text-[#FF8A00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="3">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                </svg>
              </div>

              {/* Success Texts */}
              <div className="flex flex-col gap-2">
                <h3 className="text-2xl font-black text-white">{t("successTitle")}</h3>
                <p className="text-white/60 text-xs md:text-sm leading-relaxed font-light">
                  {t("successDesc")}
                </p>
              </div>

              {/* Close Button */}
              <button
                onClick={() => {
                  setShowSuccess(false);
                  setSelectedJob(null);
                }}
                className="w-full mt-2 py-3 bg-white/5 hover:bg-[#FF8A00] text-white font-bold rounded-xl border border-white/10 hover:border-transparent transition-all duration-300 cursor-pointer"
              >
                {t("close")}
              </button>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </main>
  );
}
