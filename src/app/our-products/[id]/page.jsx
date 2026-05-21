"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { useParams, notFound } from "next/navigation";
import { products } from "../../data/products";
import { useLanguage } from "../../context/LanguageContext";
import { fetchAPI } from "../../../lib/api";

// Local highly-focused bilingual details dictionary for the SaaS products
const productDetails = {
  athath: {
    en: {
      name: "Athath",
      category: "Furniture E-Commerce",
      title: "Athath Furniture E-Commerce Solution",
      description: "Athath is a high-end, premium e-commerce platform specifically optimized for furniture retailers and interior design studios. Combining minimalist aesthetics with incredibly powerful features, it delivers a smooth shopping experience that turns visitors into loyal customers.",
      features: [
        { title: "Dynamic High-Res Gallery", desc: "Showcase furniture textures and details with high-resolution responsive zooming." },
        { title: "Smart Advanced Filtering", desc: "Enable customers to instantly filter by collection, material, color, and price." },
        { title: "Seamless Glassmorphic Cart", desc: "A frictionless one-page checkout designed to reduce cart abandonment rates." },
        { title: "Intelligent Inventory Control", desc: "Real-time stock management and order notifications for business owners." }
      ],
      techSpecs: ["Next.js", "React", "TailwindCSS", "Node.js", "MongoDB", "Framer Motion"],
      whyChooseUs: "Ready-to-launch furniture store with ultra-fast page load speeds, pre-configured checkout, and SEO-optimized structure."
    },
    ar: {
      name: "أثاث",
      category: "تجارة إلكترونية للأثاث",
      title: "منصة أثاث المتكاملة للتجارة الإلكترونية",
      description: "منصة 'أثاث' هي حل تجارة إلكترونية راقٍ ومصمم خصيصاً لمعارض الأثاث واستوديوهات التصميم الداخلي. تجمع المنصة بين جماليات التصميم العصري والأداء البرمجي الفائق، لتوفر للعملاء تجربة تسوق تفاعلية سلسة تحول الزوار إلى عملاء دائمين.",
      features: [
        { title: "معرض صور تفاعلي دقيق", desc: "استعرض تفاصيل وخامات الأثاث بدقة متناهية مع خاصية التكبير والتفاعل الفائق." },
        { title: "نظام فلترة ذكي ومتقدم", desc: "يتيح للعملاء العثور على قطع الأثاث المطلوبة فوراً عبر تصفية الخامات، الألوان، والأسعار." },
        { title: "سلة دفع زجاجية ذكية", desc: "عملية دفع من صفحة واحدة خالية من التعقيد لزيادة نسبة إتمام المبيعات وتقليل مغادرة السلة." },
        { title: "لوحة تحكم ذكية للمخزون", desc: "إدارة المخزون والطلبات فورياً وتلقي تنبيهات المبيعات أولاً بأول لأصحاب الأعمال." }
      ],
      techSpecs: ["Next.js", "React", "TailwindCSS", "Node.js", "MongoDB", "Framer Motion"],
      whyChooseUs: "متجر أثاث متكامل جاهز للإطلاق الفوري، يتميز بسرعات تحميل فائقة، ونظام دفع مهيأ، وبنية برمجية مهيأة بالكامل لمحركات البحث (SEO)."
    }
  },
  clinic: {
    en: {
      name: "Clinic+",
      category: "Healthcare Management",
      title: "Clinic+ Digital Medical System",
      description: "Clinic+ is an enterprise-grade medical clinic management platform engineered to streamline patient booking, healthcare records, and daily medical workflows. Designed with clinical efficiency and patient ease of use in mind.",
      features: [
        { title: "Real-Time Scheduling", desc: "Avoid overlapping and booking conflicts with a dynamic smart medical calendar." },
        { title: "Secure Health Records (EHR)", desc: "Confidential digital patient histories, active prescriptions, and diagnosis logs." },
        { title: "Doctor & Receptionist Hub", desc: "Custom role-based logins with unique views to manage clinic operations." },
        { title: "Automated Smart Reminders", desc: "Reduce patient no-shows with scheduled, automated notifications." }
      ],
      techSpecs: ["React", "Next.js", "TailwindCSS", "Express.js", "PostgreSQL", "Node.js"],
      whyChooseUs: "HIPAA-compliant data handling structures, ultra-smooth clinic workflow management, and zero setup latency."
    },
    ar: {
      name: "كلينيك بلس",
      category: "إدارة الرعاية الصحية",
      title: "نظام كلينيك بلس الطبي المتكامل",
      description: "نظام 'كلينيك بلس' هو منصة متطورة لإدارة العيادات والمراكز الطبية بكافة تخصصاتها. تم تطويره بهدف تسهيل حجز مواعيد المرضى، وتنظيم الملفات الطبية الرقمية، وأتمتة المهام اليومية للأطباء والموظفين بكفاءة وسرية مطلقة.",
      features: [
        { title: "جدولة مواعيد ذكية وحية", desc: "حجز مواعيد متزامن بدون تعارض مع توفير جدول تقويمي ذكي للأطباء والمرضى." },
        { title: "ملفات صحية رقمية آمنة", desc: "حفظ مشفر لتاريخ المرضى الطبي، الروشتات العلاجية، وتقارير التشخيص." },
        { title: "بوابات مخصصة للكادر الطبي", desc: "لوحات تحكم بأدوار محددة وصلاحيات أمان صارمة لإدارة العيادة اليومية." },
        { title: "تنبيهات وتذكيرات تلقائية", desc: "تقليل غياب المرضى عبر إرسال تذكيرات آلية عبر الرسائل القصيرة والبريد الإلكتروني." }
      ],
      techSpecs: ["React", "Next.js", "TailwindCSS", "Express.js", "PostgreSQL", "Node.js"],
      whyChooseUs: "تصميم يتوافق مع معايير الأمان الطبية، إدارة مرنة للورديات، وتجربة مستخدم خالية من التعقيد للمرضى والأطباء."
    }
  },
  "edu-platform": {
    en: {
      name: "EduLearn",
      category: "E-Learning & LMS",
      title: "EduLearn Educational Platform",
      description: "EduLearn is a comprehensive Learning Management System (LMS) built to empower schools, academies, and private instructors. Create, host, and sell engaging courses in a modern virtual learning environment.",
      features: [
        { title: "Structured Course Builder", desc: "Organize lessons by chapters, upload video files, PDFs, and rich-text worksheets." },
        { title: "Interactive Quiz Engine", desc: "Test student knowledge with auto-graded multiple-choice, essay, and time-bound quizzes." },
        { title: "Progress & Analytics Tracker", desc: "Give students and parents real-time insights into course completion statistics." },
        { title: "Smart Certificate Generator", desc: "Instantly reward graduating students with custom, dynamically-generated PDF credentials." }
      ],
      techSpecs: ["Next.js", "React", "Node.js", "TailwindCSS", "GraphQL", "PostgreSQL"],
      whyChooseUs: "Robust video streaming integration, gamified learning dashboards, and seamless payment collection pipelines."
    },
    ar: {
      name: "إديوليرن",
      category: "المنصات التعليمية وأنظمة التعلم",
      title: "منصة إديوليرن التعليمية والتدريبية",
      description: "منصة 'إديوليرن' هي نظام إدارة تعلم (LMS) شامل ومبتكر لخدمة المدارس، الأكاديميات، والمحاضرين الخصوصيين. تتيح لك المنصة إنشاء، واستضافة، وبيع الكورسات التفاعلية بجودة ممتازة وبيئة تعليمية تواكب أحدث المعايير.",
      features: [
        { title: "منشئ كورسات منظم وهيكلي", desc: "تنظيم الدروس في فصول، ورفع مقاطع الفيديو، وملفات PDF، وأوراق العمل التفاعلية." },
        { title: "نظام اختبارات متطور", desc: "تقييم معرفة الطلاب باختبارات مؤقتة وتصحيح تلقائي فوري متعدد الخيارات." },
        { title: "تتبع التقدم والتحليلات للمتعلم", desc: "إحصائيات فورية وتفصيلية للطلاب وأولياء الأمور لمعرفة نسب الإنجاز وساعات التعلم." },
        { title: "توليد تلقائي للشهادات الذكية", desc: "مكافأة الطلاب المتفوقين تلقائياً عند إنهاء الكورس بشهادات PDF احترافية باسمائهم." }
      ],
      techSpecs: ["Next.js", "React", "Node.js", "TailwindCSS", "GraphQL", "PostgreSQL"],
      whyChooseUs: "حماية تامة لمحتوى الفيديو من السرقة، تصميم تفاعلي ممتع للطلاب، ونظام دفع آمن مدمج."
    }
  },
  "real-estate": {
    en: {
      name: "RealEstate Pro",
      category: "Property Directory",
      title: "RealEstate Pro Management System",
      description: "RealEstate Pro is an outstanding property portal and brokerage utility. Engineered with ultra-fast search engines and spatial maps, it serves as the ultimate digital gateway for buyers, sellers, and realtors.",
      features: [
        { title: "Interactive Spatial Maps", desc: "Locate houses and commercial properties instantly on a fluid GIS map." },
        { title: "Virtual 3D Home Tours", desc: "Allow potential buyers to experience high-fidelity, interactive digital open houses." },
        { title: "Automated Realtor CRM", desc: "Assign leads to agents, manage client inquiries, and track call histories easily." },
        { title: "Mortgage & Cost Calculator", desc: "Built-in finance simulator that calculates monthly payments, deposits, and interest." }
      ],
      techSpecs: ["React", "Next.js", "TailwindCSS", "Node.js", "MongoDB", "Mapbox API"],
      whyChooseUs: "SEO-friendly property directory, automated email alerts for new listings, and flawless responsiveness across mobile devices."
    },
    ar: {
      name: "عقارات برو",
      category: "منصات التسويق والوساطة العقارية",
      title: "منصة عقارات برو الاحترافية",
      description: "منصة 'عقارات برو' هي الحل الرقمي الشامل لشركات التطوير العقاري والوسطاء. مجهزة بمحركات بحث جغرافية فائقة السرعة على الخريطة وميزات عرض متطورة، تجعل رحلة البحث عن منزل أو عقار تجاري سهلة وممتعة للغاية للعملاء.",
      features: [
        { title: "البحث التفاعلي بالخرائط", desc: "تحديد مواقع الفلل والشقق والمحلات التجارية جغرافياً على خريطة تفاعلية ذكية." },
        { title: "جولات افتراضية ثلاثية الأبعاد", desc: "تمكين المشترين المحتملين من القيام بجولة حية وتفاعلية داخل العقار من هواتفهم." },
        { title: "نظام إدارة علاقات العملاء (CRM)", desc: "توزيع طلبات العملاء تلقائياً على الوكلاء، ومتابعة التواصل وتنظيم المبيعات." },
        { title: "حاسبة تمويل عقاري مدمجة", desc: "تتيح للعميل محاكاة الأقساط الشهرية، الدفعات الأولى، ونسب الفائدة تلقائياً." }
      ],
      techSpecs: ["React", "Next.js", "TailwindCSS", "Node.js", "MongoDB", "Mapbox API"],
      whyChooseUs: "تأهيل كامل لمحركات البحث لزيادة مبيعات عقاراتك، نظام إشعارات فوري للعملاء بالفرص الجديدة، وتوافق تام مع الجوال."
    }
  },
  restaurant: {
    en: {
      name: "FoodieMenu",
      category: "Digital Restaurant Ordering",
      title: "FoodieMenu Self-Ordering System",
      description: "FoodieMenu is a cutting-edge QR-based self-ordering ecosystem for restaurants, bistros, and coffee shops. Empower your guests to order and pay directly from their tables without app installs.",
      features: [
        { title: "No-Contact Table QR Ordering", desc: "Guests scan, order items, and checkout without waiting for a waiter." },
        { title: "Vibrant Visual Menu Builder", desc: "Upload gorgeous pictures, display ingredient labels, and manage daily specials." },
        { title: "Real-Time Kitchen KDS System", desc: "Direct order dispatch to kitchen screens ensuring lightning-fast preparation." },
        { title: "Apple Pay & Wallet Integration", desc: "Seamless instant digital checkouts directly from the dining table." }
      ],
      techSpecs: ["Next.js", "React", "TailwindCSS", "Socket.io", "Firebase", "Node.js"],
      whyChooseUs: "Increases average order value by up to 25% via smart upselling, minimizes waitstaff strain, and optimizes dining turnover."
    },
    ar: {
      name: "فودي منيو",
      category: "المنيو الرقمي والطلب الذاتي",
      title: "منصة فودي منيو المتكاملة للمطاعم",
      description: "منصة 'فودي منيو' هي نظام طلب ذاتي حديث يعتمد على رموز الـ QR ومصمم خصيصاً للمطاعم والمقاهي. تتيح المنصة لرواد مطعمك مسح الكود، طلب الوجبات، وتعديل إضافاتهم، ثم الدفع فوراً بكل سهولة ودون الحاجة لتنزيل أي تطبيق.",
      features: [
        { title: "الطلب الذاتي عبر QR الطاولة", desc: "يمسح العميل الكود، ويطلب ما يشاء مباشرة دون الحاجة لانتظار النادل لتسجيل الطلب." },
        { title: "منيو تفاعلي مصور وجذاب", desc: "عرض أطباقك بصور عالية الدقة وتفاصيل المكونات، مع التحكم في إتاحة الأطباق يومياً." },
        { title: "شاشة المطبخ الفورية (KDS)", desc: "إرسال الطلبات مباشرة لشاشات الطهاة في المطبخ لتسريع عملية التحضير وتقليل الأخطاء." },
        { title: "تكامل الدفع الإلكتروني المباشر", desc: "دعم Apple Pay والبطاقات البنكية للدفع السريع والآمن مباشرة من الطاولة." }
      ],
      techSpecs: ["Next.js", "React", "TailwindCSS", "Socket.io", "Firebase", "Node.js"],
      whyChooseUs: "يزيد متوسط قيمة الطلب بنسبة تصل لـ 25٪ بفضل الاقتراحات الذكية، ويوفر تكاليف المطبخ، ويسرع معدل تدوير الطاولات."
    }
  },
  gym: {
    en: {
      name: "FitLife",
      category: "Fitness & Club Utility",
      title: "FitLife Gym & Health Club Platform",
      description: "FitLife is a premium SaaS solution engineered for modern gym franchises, fitness centres, and martial arts clubs. Smoothly control your registrations, timetables, and staff with high efficiency.",
      features: [
        { title: "Dynamic Membership Plans", desc: "Create flexible subscription plans, auto-billing schedules, and digital gate entry." },
        { title: "Interactive Class Booking", desc: "Let members book yoga, crossfit, or swimming sessions online through their portals." },
        { title: "Trainer & Shift Schedulers", desc: "Distribute classes, schedule personal training shifts, and evaluate performance." },
        { title: "Auto-Billing & Expiry Warnings", desc: "Reduce churn by sending subscription expiry warnings and bill collection invoices." }
      ],
      techSpecs: ["React", "Next.js", "TailwindCSS", "Node.js", "MongoDB", "Framer Motion"],
      whyChooseUs: "Includes check-in QR scanner system, client progress trackers, and detailed financial reports for gym management."
    },
    ar: {
      name: "فت لايف",
      category: "إدارة الصالات والنوادي الرياضية",
      title: "نظام فت لايف لإدارة مراكز اللياقة",
      description: "نظام 'فت لايف' هو منصة برمجية متكاملة مخصصة لإدارة الصالات الرياضية ومراكز الصحة والجمال. ينظم النظام الاشتراكات، الجداول الزمنية للتمارين، وتتبع أداء المدربين والمنتسبين مع تقارير مالية تفصيلية ومؤتمتة.",
      features: [
        { title: "إدارة الاشتراكات والخطط المرنة", desc: "تخصيص باقات اشتراك مرنة، وتفعيل الفواتير المتكررة التلقائية، والدخول الإلكتروني." },
        { title: "جدولة وحجز الحصص التدريبية", desc: "تمكين المشتركين من حجز حصص الكارديو، اليوغا، أو السباحة عبر بواباتهم بكل سهولة." },
        { title: "بوابة المدربين والجداول الشخصية", desc: "تنظيم ورديات المدربين الشخصيين، وتعيين الحصص، وتتبع تقدم المتدربين معهم." },
        { title: "تنبيهات وتذكيرات التجديد", desc: "نظام آلي لإرسال رسائل التذكير بقرب انتهاء الاشتراك والفواتير المعلقة لضمان الاستمرارية." }
      ],
      techSpecs: ["React", "Next.js", "TailwindCSS", "Node.js", "MongoDB", "Framer Motion"],
      whyChooseUs: "مزود بنظام مسح باركود الدخول بالهاتف، وتتبع قياسات الجسم للأعضاء، مع لوحة إحصائيات مالية شاملة للإدارة."
    }
  }
};

const ProductDetailsPage = () => {
  const params = useParams();
  const productId = params?.id;
  const { language } = useLanguage();
  const isAr = language === "ar";
  const [activePreview, setActivePreview] = useState("desktop"); // 'desktop' or 'mobile'
  const [selectedImageIndex, setSelectedImageIndex] = useState(null); // Lightbox selected image index

  const [productData, setProductData] = useState(null);
  const [otherProductsList, setOtherProductsList] = useState([]);
  const [loading, setLoading] = useState(true);

  // Samples mapping for screenshots (SaaS screens showcasing the system)
  const productSamplesMap = {
    athath: ["/portfolio/image-1.png", "/portfolio/image-2.png", "/portfolio/image-3.png", "/portfolio/image-4.png"],
    clinic: ["/portfolio/image-2.png", "/portfolio/image-3.png", "/portfolio/image-4.png", "/portfolio/image-5.png"],
    "edu-platform": ["/portfolio/image-3.png", "/portfolio/image-4.png", "/portfolio/image-5.png", "/portfolio/image-6.png"],
    "real-estate": ["/portfolio/image-4.png", "/portfolio/image-5.png", "/portfolio/image-6.png", "/portfolio/image-1.png"],
    restaurant: ["/portfolio/image-5.png", "/portfolio/image-6.png", "/portfolio/image-1.png", "/portfolio/image-2.png"],
    gym: ["/portfolio/image-6.png", "/portfolio/image-1.png", "/portfolio/image-2.png", "/portfolio/image-3.png"]
  };

  useEffect(() => {
    if (!productId) return;

    setLoading(true);
    fetchAPI(`/products/${productId}`)
      .then((data) => {
        if (data) {
          setProductData(data);
        } else {
          const localProd = products.find((p) => p.id === productId);
          if (localProd) {
            const localDetails = productDetails[productId]?.[language] || {
              name: isAr ? localProd.name || "المنتج" : localProd.name || "Product",
              category: isAr ? localProd.category || "برمجيات SaaS" : localProd.category || "SaaS Solution",
              title: isAr ? localProd.title || "تصميم متكامل" : localProd.title || "Tailored Solution",
              description: localProd.description,
              features: [
                { title: isAr ? "دعم كامل" : "Full Support", desc: isAr ? "نقدم لك دعماً برمجياً وفنياً متكاملاً للمنصة." : "We offer complete technical and code support." },
                { title: isAr ? "تصميم متجاوب" : "Responsive Design", desc: isAr ? "يعمل بكفاءة مطلقة على كافة شاشات الهواتف والأجهزة." : "Works beautifully on all screen resolutions and devices." }
              ],
              techSpecs: ["React", "Next.js", "TailwindCSS"],
              whyChooseUs: isAr ? "حل جاهز ومتكامل مع تجربة مستخدم ممتازة." : "A complete ready-to-launch solution with superb UX."
            };
            const localSamples = productSamplesMap[productId] || ["/portfolio/image-1.png", "/portfolio/image-2.png", "/portfolio/image-3.png", "/portfolio/image-4.png"];
            setProductData({
              id: localProd.id,
              name: localDetails.name,
              category: localDetails.category,
              title: localDetails.title,
              description: localDetails.description,
              image: localProd.image,
              demoLink: localProd.demoLink || '#',
              orderLink: localProd.orderLink || '#',
              features: localDetails.features,
              techSpecs: localDetails.techSpecs,
              whyChooseUs: localDetails.whyChooseUs,
              samples: localSamples
            });
          }
        }
      })
      .catch((err) => {
        console.error("Error fetching product details:", err);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [productId, language]);

  useEffect(() => {
    fetchAPI('/products')
      .then((data) => {
        if (data && Array.isArray(data)) {
          setOtherProductsList(data.filter((p) => p.id !== productId).slice(0, 6));
        } else {
          setOtherProductsList(products.filter((p) => p.id !== productId).slice(0, 6));
        }
      })
      .catch(() => {
        setOtherProductsList(products.filter((p) => p.id !== productId).slice(0, 6));
      });
  }, [productId]);

  if (loading || !productData) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="w-16 h-16 border-4 border-[#F18805] border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  const product = productData;
  const details = productData;
  const samples = productData.samples;
  const otherProducts = otherProductsList;

  // Helper icons for the features grid (4 distinct modern SVG icons)
  const featureIcons = [
    // Icon 1: Grid/Dashboard
    <svg key="1" className="w-6 h-6 text-[#F18805]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
      <path strokeLinecap="round" strokeLinejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
    </svg>,
    // Icon 2: Settings/Sliders
    <svg key="2" className="w-6 h-6 text-[#F18805]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
      <path strokeLinecap="round" strokeLinejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
    </svg>,
    // Icon 3: Lightning/Bolt
    <svg key="3" className="w-6 h-6 text-[#F18805]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
      <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>,
    // Icon 4: Shield/Check
    <svg key="4" className="w-6 h-6 text-[#F18805]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
      <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
    </svg>
  ];

  return (
    <main className="min-h-screen pt-32 pb-16 flex flex-col items-center overflow-x-hidden text-white">
      {/* Background glow graphics */}
      <div className="absolute top-0 left-1/4 w-[500px] h-[500px] bg-[#F18805]/5 rounded-full filter blur-[120px] pointer-events-none z-0" />
      <div className="absolute top-1/3 right-1/4 w-[600px] h-[600px] bg-blue-500/5 rounded-full filter blur-[150px] pointer-events-none z-0" />

      {/* Main Showcase Section */}
      <section className="w-full max-w-[1400px] mx-auto px-6 md:px-10 py-8 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start relative z-10">
        
        {/* Left Side: Product Description & Features (Col spans 7 on large screens) */}
        <div className="lg:col-span-7 flex flex-col gap-8 text-start">
          
          {/* Category Badge & Title */}
          <div className="flex flex-col gap-4 items-start">
            <span className="px-4 py-1.5 rounded-full text-xs font-bold bg-[#F18805]/10 border border-[#F18805]/30 text-[#F18805] shadow-[0_0_15px_rgba(241,136,5,0.1)] tracking-wide uppercase">
              {details.category}
            </span>
            <motion.h1
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
              className="text-3xl md:text-4xl lg:text-5xl font-black leading-tight tracking-tight text-white"
            >
              {details.title}
            </motion.h1>
          </div>

          {/* Description Block */}
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.1 }}
            className="relative p-6 rounded-2xl bg-white/[0.02] border border-white/5 backdrop-blur-md"
          >
            <p className="text-white/80 text-base md:text-lg leading-relaxed font-light">
              {details.description}
            </p>
          </motion.div>

          {/* Product Key Features Grid */}
          <div className="flex flex-col gap-5">
            <h3 className="text-xl font-bold text-[#F18805] border-b border-[#F18805]/20 pb-3 flex items-center gap-2">
              <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
              {isAr ? "الخصائص البرمجية والمميزات" : "Key Solution Features"}
            </h3>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5 mt-2">
              {details.features.map((feature, idx) => (
                <motion.div
                  key={idx}
                  initial={{ opacity: 0, y: 15 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.4, delay: idx * 0.05 }}
                  className="flex gap-4 p-4 rounded-xl bg-white/[0.01] hover:bg-white/[0.03] border border-white/[0.03] hover:border-white/10 transition-all duration-300 group"
                >
                  <div className="flex-shrink-0 w-11 h-11 rounded-lg bg-[#F18805]/10 border border-[#F18805]/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {featureIcons[idx] || featureIcons[0]}
                  </div>
                  <div className="flex flex-col gap-1">
                    <h4 className="font-bold text-white text-base group-hover:text-[#F18805] transition-colors">
                      {feature.title}
                    </h4>
                    <p className="text-white/60 text-xs md:text-sm leading-relaxed font-light">
                      {feature.desc}
                    </p>
                  </div>
                </motion.div>
              ))}
            </div>
          </div>

          {/* Technology Specifications */}
          <div className="flex flex-col gap-3">
            <h4 className="text-xs font-semibold text-white/50 uppercase tracking-widest">
              {isAr ? "التقنيات المستخدمة في المنصة" : "Technology Stack Specs"}
            </h4>
            <div className="flex flex-wrap gap-2.5">
              {details.techSpecs.map((tech, idx) => (
                <span 
                  key={idx}
                  className="px-3.5 py-1.5 rounded-lg text-xs font-mono font-medium bg-white/[0.03] hover:bg-white/[0.06] border border-white/5 hover:border-white/10 text-white/95 transition-colors cursor-default"
                >
                  {tech}
                </span>
              ))}
            </div>
          </div>

          {/* Action CTAs */}
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="flex flex-wrap items-center gap-4 mt-4"
          >
            <Link
              href={product.demoLink}
              className="relative overflow-hidden bg-[#F18805] hover:bg-[#d17604] text-white px-10 py-4 rounded-xl font-bold transition-all duration-300 shadow-[0_0_30px_rgba(241,136,5,0.25)] hover:shadow-[0_0_40px_rgba(241,136,5,0.4)] flex items-center gap-3 hover:-translate-y-0.5 group"
            >
              <span>{isAr ? "معاينة النظام حياً" : "See Live Demo"}</span>
              <svg className="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </Link>
            <Link
              href={product.orderLink}
              className="bg-transparent border-[2px] border-white/10 hover:border-[#F18805] hover:bg-[#F18805]/10 text-white px-10 py-4 rounded-xl font-bold transition-all duration-300 flex items-center gap-2 hover:-translate-y-0.5"
            >
              <span>{isAr ? "اطلب نسختك الآن" : "Order Solution Now!"}</span>
            </Link>
          </motion.div>
        </div>

        {/* Right Side: Interactive Image Device Showcase (Col spans 5) */}
        <div className="lg:col-span-5 w-full flex flex-col items-center">
          
          {/* Desktop/Mobile Device Toggle Buttons */}
          <div className="flex items-center gap-1.5 p-1.5 bg-white/[0.02] border border-white/5 rounded-full mb-8 shadow-inner">
            <button
              onClick={() => setActivePreview("desktop")}
              className={`flex items-center gap-2 rounded-full px-5 py-2 text-xs md:text-sm font-semibold transition-all duration-300 ${
                activePreview === "desktop"
                  ? "bg-[#F18805] text-white shadow-[0_4px_15px_rgba(241,136,5,0.25)]"
                  : "text-white/60 hover:text-white hover:bg-white/5"
              }`}
            >
              <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              {isAr ? "معاينة الحاسوب" : "Desktop View"}
            </button>
            <button
              onClick={() => setActivePreview("mobile")}
              className={`flex items-center gap-2 rounded-full px-5 py-2 text-xs md:text-sm font-semibold transition-all duration-300 ${
                activePreview === "mobile"
                  ? "bg-[#F18805] text-white shadow-[0_4px_15px_rgba(241,136,5,0.25)]"
                  : "text-white/60 hover:text-white hover:bg-white/5"
              }`}
            >
              <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              {isAr ? "معاينة الجوال" : "Mobile View"}
            </button>
          </div>

          {/* Interactive Screen Device Mockup */}
          <div className="w-full relative flex items-center justify-center min-h-[300px] md:min-h-[420px]">
            <AnimatePresence mode="wait">
              {activePreview === "desktop" ? (
                /* Desktop Web Browser Bezel Mockup */
                <motion.div
                  key="desktop"
                  initial={{ opacity: 0, scale: 0.9, y: 15 }}
                  animate={{ opacity: 1, scale: 1, y: 0 }}
                  exit={{ opacity: 0, scale: 0.9, y: -15 }}
                  transition={{ duration: 0.4 }}
                  className="w-full relative z-10 max-w-[600px] group"
                >
                  {/* Slow neon pulse background glow */}
                  <div className="absolute inset-0 bg-[#F18805]/10 rounded-2xl filter blur-3xl opacity-60 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none" />

                  {/* Browser Container */}
                  <div className="relative w-full rounded-2xl overflow-hidden border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.8)] bg-[#101015]">
                    
                    {/* Browser Toolbar Controls */}
                    <div className="bg-[#16161F] border-b border-white/5 px-4 py-3.5 flex items-center gap-3">
                      {/* Window Dots */}
                      <div className="flex gap-1.5">
                        <span className="w-3 h-3 rounded-full bg-[#ff5f56]" />
                        <span className="w-3 h-3 rounded-full bg-[#ffbd2e]" />
                        <span className="w-3 h-3 rounded-full bg-[#27c93f]" />
                      </div>
                      
                      {/* Simulated URL bar */}
                      <div className="bg-white/[0.04] border border-white/5 rounded-lg px-4 py-1 text-[10px] md:text-xs text-white/40 w-full max-w-[320px] mx-auto text-center truncate select-none font-mono">
                        https://serv5.com/demo/{product.id}
                      </div>
                    </div>
                    
                    {/* Viewport content */}
                    <div className="relative w-full aspect-[16/10] overflow-hidden bg-[#1E1E28]">
                      <Image
                        src={product.image}
                        alt={product.title}
                        fill
                        className="object-cover transition-transform duration-700 group-hover:scale-105"
                        priority
                      />
                      {/* Premium elegant overlay reflection shine */}
                      <div className="absolute inset-0 bg-gradient-to-tr from-transparent via-white/[0.02] to-transparent pointer-events-none" />
                    </div>
                  </div>
                </motion.div>
              ) : (
                /* Mobile Phone Bezel Mockup */
                <motion.div
                  key="mobile"
                  initial={{ opacity: 0, scale: 0.9, y: 15 }}
                  animate={{ opacity: 1, scale: 1, y: 0 }}
                  exit={{ opacity: 0, scale: 0.9, y: -15 }}
                  transition={{ duration: 0.4 }}
                  className="relative z-10 w-full max-w-[290px] group"
                >
                  {/* Slow neon pulse background glow */}
                  <div className="absolute inset-0 bg-blue-500/10 rounded-[45px] filter blur-2xl opacity-60 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none" />

                  {/* Phone Bezel */}
                  <div className="relative w-full aspect-[9/19] rounded-[42px] border-[10px] border-[#16161F] shadow-[0_30px_60px_rgba(0,0,0,0.85)] bg-[#101015] overflow-hidden ring-1 ring-white/10">
                    
                    {/* Speaker Camera Notch */}
                    <div className="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-[#16161F] rounded-b-2xl z-20 flex items-center justify-between px-4">
                      {/* Simulated Speaker line */}
                      <span className="w-10 h-1 bg-white/15 rounded-full" />
                      {/* Camera lens */}
                      <span className="w-2.5 h-2.5 bg-[#0D0D12] rounded-full border border-white/5" />
                    </div>

                    {/* Content view screen */}
                    <div className="relative w-full h-full overflow-hidden bg-[#1E1E28]">
                      <Image
                        src={product.image}
                        alt={product.title}
                        fill
                        className="object-cover object-top transition-transform duration-700 group-hover:scale-105"
                      />
                      {/* Dynamic light reflection line overlay */}
                      <div className="absolute inset-0 bg-gradient-to-tr from-transparent via-white/[0.03] to-transparent pointer-events-none" />
                    </div>
                  </div>
                </motion.div>
              )}
            </AnimatePresence>
          </div>
        </div>
      </section>

      {/* Samples from Product Gallery Section */}
      <section className="w-full max-w-[1400px] mx-auto px-6 md:px-10 py-16 flex flex-col items-center gap-12 relative z-10 border-t border-white/5 mt-12">
        <div className="flex flex-col items-center text-center gap-4 max-w-3xl">
          <span className="px-4 py-1.5 rounded-full text-[10px] md:text-xs font-bold bg-[#F18805]/10 border border-[#F18805]/20 text-[#F18805] shadow-[0_0_15px_rgba(241,136,5,0.05)] uppercase tracking-widest">
            {isAr ? "لقطات حية من داخل النظام" : "Live Screenshots & Visuals"}
          </span>
          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5 }}
            className="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white tracking-tight"
          >
            {isAr ? "نماذج ولقطات حية من المنتج" : "Samples from Product (Screenshots)"}
          </motion.h2>
          <p className="text-white/60 text-xs md:text-sm leading-relaxed font-light">
            {isAr 
              ? "استكشف واجهات النظام والمنصات الحقيقية، لوحات تحكم الإدارة، وتصاميم الشاشات التي تم تصميمها وتطويرها بالكامل بأعلى كفاءة وجودة."
              : "Take a closer look at the actual interactive dashboards, administrative platforms, and high-fidelity layouts engineered within the solution."}
          </p>
        </div>

        {/* Gallery Grid */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 w-full">
          {samples.map((img, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.4, delay: idx * 0.05 }}
              onClick={() => setSelectedImageIndex(idx)}
              className="relative aspect-[16/10] rounded-xl overflow-hidden border border-white/5 hover:border-[#F18805]/40 bg-[#16161F] cursor-pointer group shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(241,136,5,0.15)]"
            >
              {/* Thumbnail Image */}
              <Image
                src={img}
                alt={`${details.name} Sample Screenshot ${idx + 1}`}
                fill
                className="object-cover transition-transform duration-700 group-hover:scale-105"
              />
              
              {/* Glassmorphic Overlay Hover Effect */}
              <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center p-4 text-center backdrop-blur-[2px]">
                <div className="w-10 h-10 rounded-full bg-[#F18805] text-white flex items-center justify-center shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                  </svg>
                </div>
                <span className="text-white text-xs font-semibold mt-3 tracking-wide transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                  {isAr ? "عرض الشاشة" : "View Screen"}
                </span>
              </div>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Fullscreen Spring Lightbox Modal */}
      <AnimatePresence>
        {selectedImageIndex !== null && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-md p-4 md:p-10"
            onClick={() => setSelectedImageIndex(null)}
          >
            {/* Top Close Button (Floating) */}
            <button
              className="absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-[#F18805] text-white rounded-full flex items-center justify-center border border-white/10 transition-colors z-50"
              onClick={(e) => {
                e.stopPropagation();
                setSelectedImageIndex(null);
              }}
            >
              <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            {/* Left Prev Navigation Button */}
            <button
              className="absolute left-4 md:left-8 w-12 h-12 bg-white/10 hover:bg-[#F18805] text-white rounded-full flex items-center justify-center border border-white/10 transition-colors z-50"
              onClick={(e) => {
                e.stopPropagation();
                setSelectedImageIndex((selectedImageIndex - 1 + samples.length) % samples.length);
              }}
            >
              <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
            </button>

            {/* Right Next Navigation Button */}
            <button
              className="absolute right-4 md:right-8 w-12 h-12 bg-white/10 hover:bg-[#F18805] text-white rounded-full flex items-center justify-center border border-white/10 transition-colors z-50"
              onClick={(e) => {
                e.stopPropagation();
                setSelectedImageIndex((selectedImageIndex + 1) % samples.length);
              }}
            >
              <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </button>

            {/* Main Lightbox Image Viewport */}
            <motion.div
              initial={{ scale: 0.92, y: 20 }}
              animate={{ scale: 1, y: 0 }}
              exit={{ scale: 0.92, y: 20 }}
              transition={{ type: "spring", damping: 25, stiffness: 220 }}
              className="relative aspect-[16/10] w-full max-w-[960px] rounded-2xl overflow-hidden border border-white/10 shadow-[0_0_60px_rgba(241,136,5,0.15)] z-40 bg-[#1E1E28]"
              onClick={(e) => e.stopPropagation()}
            >
              <Image
                src={samples[selectedImageIndex]}
                alt={`Sample screenshot ${selectedImageIndex + 1}`}
                fill
                className="object-cover"
              />
              
              {/* Info pill showing screenshot number */}
              <div className="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/10 text-xs font-medium tracking-wider text-white/90">
                {selectedImageIndex + 1} / {samples.length}
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Related Products Section */}
      <section className="w-full max-w-[1400px] mx-auto px-6 md:px-10 py-20 flex flex-col items-center gap-12 relative z-10 border-t border-white/5 mt-12">
        <motion.h2
          initial={{ opacity: 0, y: 15 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5 }}
          className="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white text-center tracking-tight"
        >
          {isAr ? "استكشف المزيد من حلولنا الذكية" : "More SaaS Products You Might Like"}
        </motion.h2>

        {/* Related grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full">
          {otherProducts.map((item, index) => (
            <Link href={`/our-products/${item.id}`} key={item.id} className="block group">
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: index * 0.05 }}
                className="flex flex-col h-full bg-[#16161F]/60 hover:bg-[#1C1C26]/80 backdrop-blur-md rounded-xl overflow-hidden border border-white/10 hover:border-[#F18805]/40 transition-all duration-300 shadow-lg group hover:-translate-y-1"
              >
                {/* Image Box */}
                <div className="relative w-full aspect-video overflow-hidden bg-[#2a2a35]">
                  <Image
                    src={item.image}
                    alt={item.title}
                    fill
                    className="object-cover transition-transform duration-700 group-hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#16161F] to-transparent opacity-80" />
                  <div className="absolute top-4 left-4 bg-[#F18805] text-white text-[10px] md:text-xs font-bold px-3 py-1 rounded-full shadow-md">
                    {productDetails[item.id]?.[language]?.category || item.category}
                  </div>
                </div>

                {/* Content Info */}
                <div className="p-6 flex flex-col flex-grow text-start">
                  <h3 className="text-xl font-bold text-white mb-2 group-hover:text-[#F18805] transition-colors">
                    {productDetails[item.id]?.[language]?.name || item.name}
                  </h3>
                  <p className="text-white/60 text-xs md:text-sm leading-relaxed mb-6 flex-grow line-clamp-2 font-light">
                    {productDetails[item.id]?.[language]?.description || item.description}
                  </p>
                  <div className="mt-auto flex items-center justify-between">
                    <span className="text-[#F18805] font-bold text-xs md:text-sm flex items-center gap-2 group-hover:gap-3 transition-all duration-300">
                      {isAr ? "عرض التفاصيل الكاملة" : "View Full Details"}
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                      </svg>
                    </span>
                  </div>
                </div>
              </motion.div>
            </Link>
          ))}
        </div>

        {/* View All Button */}
        <motion.div
          initial={{ opacity: 0, y: 15 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5 }}
          className="mt-6"
        >
          <Link
            href="/our-products"
            className="inline-block bg-transparent border border-white/10 hover:border-[#F18805] hover:bg-[#F18805]/10 text-white px-10 py-3.5 rounded-xl font-bold transition-all duration-300"
          >
            {isAr ? "استكشف كافة الحلول والمنتجات" : "Check All SaaS Products"}
          </Link>
        </motion.div>
      </section>
    </main>
  );
};

export default ProductDetailsPage;
