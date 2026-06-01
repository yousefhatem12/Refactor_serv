"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { useParams } from "next/navigation";
import { fetchAPI } from "@/lib/api";
import Loading from "../../loading";
import { useLanguage } from "../../context/LanguageContext";

const ProjectImage = ({ src, alt }) => {
  const defaultImage = "/our-portfolio/Rectangle 6.png";
  const [imgSrc, setImgSrc] = useState(src || defaultImage);

  useEffect(() => {
    setImgSrc(src || defaultImage);
  }, [src]);

  return (
    <Image
      src={imgSrc}
      alt={alt}
      fill
      className="object-cover object-center group-hover:scale-102 transition-transform duration-700"
      priority
      onError={() => setImgSrc(defaultImage)}
    />
  );
};

export default function ProjectDetails() {
  const { language } = useLanguage();
  const params = useParams();
  const id = params?.id;
  const [project, setProject] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  // Sync state with dynamic params
  const [prevId, setPrevId] = useState(id);
  const [prevLang, setPrevLang] = useState(language);

  if (id !== prevId || language !== prevLang) {
    setPrevId(id);
    setPrevLang(language);
    setIsLoading(true);
  }

  useEffect(() => {
    if (id) {
      fetchAPI(`/portfolio/${id}?lang=${language}`)
        .then((data) => {
          setProject(data);
          setIsLoading(false);
        })
        .catch((err) => {
          console.error(err);
          setIsLoading(false);
        });
    }
  }, [id, language]);

  if (isLoading) {
    return <Loading />;
  }

  if (!project) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center text-white bg-[#040A37]">
        <h1 className="text-2xl font-bold mb-4">
          {language === "ar" ? "المشروع غير موجود" : "Project not found"}
        </h1>
        <Link href="/portfolio" className="text-[#FF8A00] hover:underline">
          {language === "ar" ? "العودة للمعرض" : "Back to Portfolio"}
        </Link>
      </div>
    );
  }

  const isRtl = language === "ar";

  // Translate category key to readable labels
  const getCategoryLabel = (cat) => {
    const map = {
      websites: language === "ar" ? "تصميم وتطوير المواقع" : "Websites & Web Apps",
      mobile: language === "ar" ? "تطبيقات الهواتف الذكية" : "Mobile Applications",
      motion: language === "ar" ? "تصاميم الموشن جرافيك" : "Motion Graphics",
      digital: language === "ar" ? "الهويات البصرية والرقمية" : "Digital Identity & Branding",
      social: language === "ar" ? "تصاميم شبكات التواصل" : "Social Media Designs",
    };
    return map[cat] || cat;
  };

  return (
    <main
      className="flex min-h-screen flex-col relative overflow-x-hidden pt-32 pb-24"
      dir={isRtl ? "rtl" : "ltr"}
    >
      {/* Background Layer */}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      <div className="w-full relative z-10 flex flex-col items-center text-white px-6 lg:px-8">
        <section className="w-full max-w-7xl mx-auto">
          {/* Back Button */}
          <Link
            href="/portfolio"
            className="inline-flex items-center gap-2 text-gray-400 hover:text-white mb-10 transition-colors group"
          >
            <svg
              className={`w-5 h-5 transition-transform ${isRtl ? "group-hover:translate-x-1" : "group-hover:-translate-x-1"}`}
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                d={isRtl ? "M14 5l7 7m0 0l-7 7m7-7H3" : "M10 19l-7-7m0 0l7-7m-7 7h18"}
              />
            </svg>
            <span className="font-semibold">
              {language === "ar" ? "العودة إلى المعرض" : "Back to Portfolio"}
            </span>
          </Link>

          <div className="flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">
            {/* Left Column - Details */}
            <div className="w-full lg:w-1/2">
              <span className="text-[#FF8A00] font-extrabold text-sm mb-4 block uppercase tracking-widest">
                {getCategoryLabel(project.category)}
              </span>
              <h1 className="text-3xl md:text-5xl font-black mb-8 leading-tight text-white uppercase">
                {project.name}
              </h1>

              {/* Description */}
              <div className="mb-10 bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 backdrop-blur-sm">
                <h2 className="text-xl font-bold mb-4 text-white border-b border-white/10 pb-3">
                  {language === "ar" ? "نظرة عامة على المشروع" : "Project Overview"}
                </h2>
                <p className="text-gray-300 text-base md:text-lg leading-relaxed">
                  {project.description}
                </p>
              </div>

              {/* Project Meta Info */}
              <div className="border border-white/10 rounded-2xl p-6 md:p-8 bg-white/5 backdrop-blur-sm">
                <h2 className="text-xl font-bold mb-6 text-white border-b border-white/10 pb-3">
                  {language === "ar" ? "تفاصيل المشروع" : "Project Details"}
                </h2>
                <div className="space-y-4">
                  <div className="flex justify-between items-center py-2 border-b border-white/5">
                    <span className="text-gray-400 font-medium">
                      {language === "ar" ? "العميل" : "Client"}
                    </span>
                    <span className="text-white font-semibold">
                      {project.client || "—"}
                    </span>
                  </div>
                  <div className="flex justify-between items-center py-2 border-b border-white/5">
                    <span className="text-gray-400 font-medium">
                      {language === "ar" ? "القسم" : "Category"}
                    </span>
                    <span className="text-white font-semibold">
                      {getCategoryLabel(project.category)}
                    </span>
                  </div>

                  {/* Tags */}
                  {project.tags && project.tags.length > 0 && (
                    <div className="flex flex-col gap-2 py-2">
                      <span className="text-gray-400 font-medium">
                        {language === "ar" ? "التقنيات المستخدمة" : "Technologies"}
                      </span>
                      <div className="flex flex-wrap gap-2 mt-2">
                        {project.tags.map((tag, idx) => (
                          <span
                            key={idx}
                            className="text-xs font-semibold px-3 py-1.5 rounded-full bg-[#FF8A00]/10 text-[#FF8A00] border border-[#FF8A00]/20"
                          >
                            {tag}
                          </span>
                        ))}
                      </div>
                    </div>
                  )}
                </div>

                {/* Visit Live Website Link */}
                {project.url && (
                  <div className="mt-8">
                    <a
                      href={project.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-flex w-full items-center justify-center gap-2 px-6 py-4 bg-[#FF8A00] hover:bg-[#ff9c24] text-white font-extrabold rounded-xl transition-all shadow-[0_4px_15px_rgba(255,138,0,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(255,138,0,0.5)] cursor-pointer text-center"
                    >
                      <span>
                        {language === "ar" ? "زيارة موقع المشروع" : "Visit Live Project"}
                      </span>
                      <svg
                        className="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                        />
                      </svg>
                    </a>
                  </div>
                )}
              </div>
            </div>

            {/* Right Column - Media */}
            <div className="w-full lg:w-1/2 sticky top-32">
              <div className="relative w-full aspect-[1.6/1] md:aspect-[1.5/1] rounded-2xl overflow-hidden shadow-2xl border border-white/10 bg-gray-800/80 group">
                <ProjectImage src={project.image} alt={project.name} />
                {/* Visual Glass Effect Border */}
                <div className="absolute inset-0 border border-white/10 pointer-events-none rounded-2xl" />
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  );
}
