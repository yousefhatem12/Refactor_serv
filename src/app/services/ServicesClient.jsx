"use client";
import React, { useState, useEffect, useRef } from "react";
import Link from "next/link";
import Image from "next/image";
import { motion } from "framer-motion";
import { useLanguage } from "../context/LanguageContext";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import CTA from "../components/Home/CTA/CTA";
import { 
  HiOutlineCode, 
  HiOutlineColorSwatch, 
  HiOutlineShare, 
  HiOutlineCloud, 
  HiOutlineShoppingCart, 
  HiOutlineDeviceMobile 
} from "react-icons/hi";

const DEFAULT_SERVICES = [
  {
    id: "web-development",
    slug: "services-web-development",
    iconComponent: HiOutlineCode,
    titleEn: "Web Development",
    titleAr: "تطوير المواقع وتطبيقات الويب",
    descEn: "Custom web development using modern frameworks, scalable architecture, and secure databases.",
    descAr: "تطوير مواقع وتطبيقات ويب متقدمة باستخدام أحدث التقنيات مع بنية تحتية آمنة وقابلة للتوسع.",
    color: "#3B82F6",
  },
  {
    id: "ui-ux-design",
    slug: "services-ui-ux-design",
    iconComponent: HiOutlineColorSwatch,
    titleEn: "UI / UX Design",
    titleAr: "تصميم واجهات وتجربة المستخدم",
    descEn: "User-centric design solutions delivering intuitive, visually stunning, and conversion-focused interfaces.",
    descAr: "تصميم واجهات مستخدم وتجارب تفاعلية استثنائية تركز على راحة المستخدم وزيادة معدل التحويل.",
    color: "#A855F7",
  },
  {
    id: "social-media",
    slug: "services-social-media",
    iconComponent: HiOutlineShare,
    titleEn: "Digital Marketing & SEO",
    titleAr: "التسويق الرقمي والسوشيال ميديا",
    descEn: "Data-driven marketing strategies, SEO optimization, and social media campaigns to elevate your brand.",
    descAr: "استراتيجيات تسويق رقمي مدروسة وتحسين محركات البحث وإدارة الحملات الإعلانية لنمو علامتك التجارية.",
    color: "#EC4899",
  },
  {
    id: "cloud-hosting",
    slug: "services-cloud-hosting",
    iconComponent: HiOutlineCloud,
    titleEn: "Cloud Hosting & DevOps",
    titleAr: "الاستضافة السحابية والدعم الفني",
    descEn: "High-speed, enterprise-grade cloud hosting with 99.9% uptime, SSD servers, and 24/7 technical support.",
    descAr: "استضافة سحابية فائقة السرعة مع حماية متطورة وضمان تشغيل 99.9% ودعم فني متواصل على مدار الساعة.",
    color: "#10B981",
  },
  {
    id: "ecommerce",
    slug: "services-ecommerce",
    iconComponent: HiOutlineShoppingCart,
    titleEn: "E-Commerce Solutions",
    titleAr: "حلول المتاجر الإلكترونية",
    descEn: "Feature-rich online stores with secure payment gateways, inventory management, and fast checkout.",
    descAr: "متاجر إلكترونية احترافية مجهزة ببوابات دفع آمنة وإدارة شاملة للمنتجات والمخزون وسرعة فائقة.",
    color: "#F59E0B",
  },
  {
    id: "mobile-apps",
    slug: "services-mobile-apps",
    iconComponent: HiOutlineDeviceMobile,
    titleEn: "Mobile Applications",
    titleAr: "برمجة تطبيقات الجوال",
    descEn: "High-performance native and cross-platform mobile apps for iOS and Android with smooth user experiences.",
    descAr: "تطوير تطبيقات هواتف ذكية لأنظمة iOS وأندرويد بأداء سلس وتصميم جذاب يلبي احتياجات عملائك.",
    color: "#06B6D4",
  },
];

export default function ServicesClient({ initialServices = [], initialLang = "en" }) {
  const { language, t } = useLanguage();
  const isAr = language === "ar";
  const [services, setServices] = useState(initialServices);
  const initialLangRef = useRef(initialLang);
  const isInitialMountRef = useRef(true);

  useEffect(() => {
    if (isInitialMountRef.current) {
      isInitialMountRef.current = false;
      if (initialServices && initialServices.length > 0 && language === initialLangRef.current) {
        return;
      }
    }

    let isMounted = true;
    fetchAPI(`/services?lang=${language}`)
      .then((res) => {
        if (!isMounted) return;
        const list = Array.isArray(res) ? res : res?.data || [];
        if (list.length > 0) setServices(list);
      })
      .catch(console.error);

    return () => {
      isMounted = false;
    };
  }, [language, initialServices]);

  // Combine or fallback to default services
  const displayServices = DEFAULT_SERVICES.map((def) => {
    const apiMatch = services.find(
      (s) => s.id === def.id || s.slug === def.slug || s.slug === def.id
    );
    if (!apiMatch) return def;
    return {
      ...def,
      titleEn: apiMatch.title || def.titleEn,
      titleAr: apiMatch.title || def.titleAr,
      descEn: apiMatch.hero_subtitle || apiMatch.description || def.descEn,
      descAr: apiMatch.hero_subtitle || apiMatch.description || def.descAr,
      image: apiMatch.image || apiMatch.default_hero_image,
      icon: apiMatch.icon,
    };
  });

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden bg-[#040A37]">
      {/* Background radial glows */}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />
      <div className="absolute top-0 left-1/4 w-[500px] h-[500px] bg-[#FF8A00]/10 rounded-full filter blur-[140px] pointer-events-none z-0" />
      <div className="absolute top-1/3 right-1/4 w-[600px] h-[600px] bg-blue-600/10 rounded-full filter blur-[160px] pointer-events-none z-0" />

      {/* Hero section */}
      <section className="w-full max-w-7xl mx-auto px-6 pt-32 pb-16 text-center relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
        >
          <span className="text-[#FF8A00] font-semibold text-sm md:text-base tracking-wider uppercase mb-3 inline-block">
            {isAr ? "خدماتنا المتكاملة" : "WHAT WE OFFER"}
          </span>
          <h1 className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">
            {isAr ? "حلول رقمية مبتكرة تصنع الفارق" : "Innovative Digital Solutions for Your Growth"}
          </h1>
          <p className="text-gray-300 text-base sm:text-lg md:text-xl max-w-3xl mx-auto leading-relaxed">
            {isAr
              ? "نقدم باقة شاملة من الخدمات الرقمية التي تلبي تطلعات عملك من تصميم وتطوير المواقع وتطبيقات الجوال إلى التسويق وحلول السحابة."
              : "We deliver a comprehensive suite of digital services designed to accelerate your business growth from web & mobile development to marketing and cloud infrastructure."}
          </p>
        </motion.div>
      </section>

      {/* Services Grid */}
      <section className="w-full max-w-7xl mx-auto px-6 pb-24 relative z-10">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {displayServices.map((service, index) => {
            const Icon = service.iconComponent;
            const targetUrl = `/${language}/services/${service.slug}`;
            const title = isAr ? service.titleAr : service.titleEn;
            const description = isAr ? service.descAr : service.descEn;

            return (
              <motion.div
                key={service.id}
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="group relative rounded-2xl bg-white/[0.04] border border-white/10 hover:border-[#FF8A00]/50 transition-all duration-300 p-8 flex flex-col justify-between hover:bg-white/[0.07] shadow-lg hover:shadow-2xl hover:shadow-[#FF8A00]/10"
              >
                <div>
                  <div
                    className="w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-inner"
                    style={{ backgroundColor: `${service.color}20` }}
                  >
                    {service.icon ? (
                      <Image
                        src={getMediaUrl(service.icon)}
                        alt={title}
                        width={36}
                        height={36}
                        className="object-contain"
                      />
                    ) : (
                      <Icon className="w-8 h-8" style={{ color: service.color }} />
                    )}
                  </div>

                  <h2 className="text-2xl font-bold text-white mb-3 group-hover:text-[#FF8A00] transition-colors">
                    {title}
                  </h2>

                  <p className="text-gray-300 text-sm sm:text-base leading-relaxed mb-6">
                    {description}
                  </p>
                </div>

                <div>
                  <Link
                    href={targetUrl}
                    className="inline-flex items-center gap-2 text-sm font-semibold text-[#FF8A00] hover:text-white transition-colors group-hover:translate-x-1 duration-300"
                    aria-label={`${title} - ${isAr ? "عرض التفاصيل" : "Learn More"}`}
                  >
                    <span>{isAr ? "عرض التفاصيل" : "Learn More"}</span>
                    <span aria-hidden="true">{isAr ? "←" : "→"}</span>
                  </Link>
                </div>
              </motion.div>
            );
          })}
        </div>
      </section>

      {/* Bottom Call to Action */}
      <CTA />
    </main>
  );
}

