"use client";
import React, { useEffect, useState } from "react";
import Image from "next/image";
import { useParams, useRouter, notFound } from "next/navigation";
import { fetchAPI } from "../../lib/api";
import { sanitizeHtml } from "../../lib/sanitize-html";
import { safeUrl } from "../../lib/safe-url";
import { useLanguage } from "../context/LanguageContext";
import AboutHero from "../components/AboutUs/AboutHero";
import Experience from "../components/AboutUs/Experience";
import Solutions from "../components/AboutUs/Solutions";

// Contact Imports
import ContactInfo from "../components/Contact/ContactInfo";
import ContactForm from "../components/Contact/ContactForm";
import ContactMap from "../components/Contact/ContactMap";

// Portfolio Imports
import PortfolioGallery from "../components/Portfolio/PortfolioGallery";
import CTA from "../components/Home/CTA/CTA";

// Blog Imports
import BlogHero from "../components/Blog/BlogHero";
import BlogGrid from "../components/Blog/BlogGrid";
import Newsletter from "../components/Blog/Newsletter";

// FAQ Imports
import FAQContent from "../components/FAQ/FAQContent";

// Web Dev Layout Components
import WebDevHero from "../components/Services-web-dev/WebDevHero";
import WebDesignFeatures from "../components/Services-web-design/WebDesignFeatures";
import WebDevHowWeWork from "../components/Services-web-dev/WebDevHowWeWork";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

// UI/UX Design Layout Components
import WebDesignHero from "../components/Services-web-design/WebDesignHero";
import WebDesignPortfolio from "../components/Services-web-design/WebDesignPortfolio";
import WebDesignRequest from "../components/Services-web-design/WebDesignRequest";

// Social Media Layout Components
import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";
import DigitalMarketingServices from "../components/Services-social-media/DigitalMarketingServices";
import SocialMediaWhyChooseUs from "../components/Services-social-media/SocialMediaWhyChooseUs";
import SocialMediaHowWeWork from "../components/Services-social-media/SocialMediaHowWeWork";
import SocialMediaContact from "../components/Services-social-media/SocialMediaContact";

// Cloud Hosting Layout Components
import ServicesHero from "../components/Services-web-hosting/ServicesHero";
import ServicesFeatures from "../components/Services-web-hosting/ServicesFeatures";
import HostingOffers from "../components/Services-web-hosting/HostingOffers";

// Ecommerce Layout Components
import EcommerceHero from "../components/Services-ecommerce/EcommerceHero";
import EcommerceFeatures from "../components/Services-ecommerce/EcommerceFeatures";

// Mobile Apps Layout Components
import MobileAppsHero from "../components/Services-mobile-apps/MobileAppsHero";
import MobileAppsFeatures from "../components/Services-mobile-apps/MobileAppsFeatures";
import MobileAppHowWeWork from "../components/Services-web-dev/MobileAppHowWeWork";

const STORAGE_URL =
  process.env.NEXT_PUBLIC_API_URL?.replace("/api", "") ||
  "http://127.0.0.1:8000";

export default function DynamicPageClient({
  slug,
  initialPageData,
  initialServiceData,
  isServicePage,
  initialPosts,
}) {
  const { language, t } = useLanguage();
  const router = useRouter();
  const [pageData, setPageData] = useState(initialPageData);
  const [serviceData, setServiceData] = useState(initialServiceData);
  const [posts, setPosts] = useState(initialPosts || []);




  if (!isServicePage && !pageData) return null;
  if (isServicePage && !serviceData) return null;

  const template = isServicePage
    ? "service"
    : pageData?.sections?.template || "custom";

  const getServiceTemplate = (data) => {
    if (data.template) {
      return data.template;
    }
    const id = data.id || "";
    if (id.includes("web-development")) return "web-development";
    if (id.includes("ui-ux-design") || id.includes("web-design"))
      return "ui-ux-design";
    if (
      id.includes("social-media") ||
      id.includes("seo") ||
      id.includes("marketing")
    )
      return "social-media";
    if (
      id.includes("cloud-hosting") ||
      id.includes("web-hosting") ||
      id.includes("hosting")
    )
      return "cloud-hosting";
    if (
      id.includes("ecommerce") ||
      id.includes("online-stores") ||
      id.includes("shop")
    )
      return "ecommerce";
    if (id.includes("mobile-apps") || id.includes("app-development"))
      return "mobile-apps";
    if (id.includes("digital-identity")) return "digital-identity";
    return "web-development"; // default fallback
  };

  return (
    <main className="min-h-screen bg-[#020617] text-white flex flex-col relative overflow-x-hidden">
      {template === "service" && (
        <div className="w-full relative z-10 pb-10 pt-20">
          {serviceData.blocks &&
          Array.isArray(serviceData.blocks) &&
          serviceData.blocks.length > 0
            ? serviceData.blocks.map((block, index) => (
                <RenderBlock
                  key={index}
                  block={block}
                  heroStyle={serviceData.hero_style || "social_media_hero"}
                  defaultHeroImage={
                    serviceData.default_hero_image ||
                    "/template/template-one-hero.webp"
                  }
                  serviceId={serviceData.id}
                  language={language}
                  t={t}
                />
              ))
            : (() => {
                const serviceTemplate = getServiceTemplate(serviceData);
                switch (serviceTemplate) {
                  case "web-development":
                    return (
                      <>
                        {serviceData.show_hero !== false && (
                          <WebDevHero
                            data={serviceData}
                            serviceId={serviceData.id}
                          />
                        )}
                        {serviceData.show_features !== false && (
                          <WebDesignFeatures
                            title={
                              serviceData.features_title ||
                              t("webDevFeaturesTitle")
                            }
                            description={serviceData.features_subtitle}
                            featuresList={serviceData.features_list}
                          />
                        )}
                        {serviceData.show_how_we_work !== false && (
                          <WebDevHowWeWork
                            howWeWorkList={serviceData.how_we_work_list}
                          />
                        )}
                        {serviceData.show_blogs !== false && <LatestBlogs />}
                      </>
                    );
                  case "ui-ux-design":
                    return (
                      <>
                        {serviceData.show_hero !== false && (
                          <WebDesignHero
                            data={serviceData}
                            serviceId={serviceData.id}
                          />
                        )}
                        {serviceData.show_features !== false && (
                          <WebDesignFeatures
                            title={serviceData.features_title}
                            description={serviceData.features_subtitle}
                            featuresList={serviceData.features_list}
                          />
                        )}
                        {serviceData.show_portfolio !== false && (
                          <WebDesignPortfolio />
                        )}
                        {serviceData.show_request !== false && (
                          <WebDesignRequest />
                        )}
                        {serviceData.show_blogs !== false && <LatestBlogs />}
                      </>
                    );
                  case "social-media":
                    return (
                      <>
                        {serviceData.show_hero !== false && (
                          <SocialMediaHero
                            title={serviceData.hero_title || serviceData.title}
                            desc={
                              serviceData.hero_subtitle ||
                              serviceData.description
                            }
                            bgImage={
                              serviceData.id === "seo-optimization" ||
                              serviceData.id === "network-marketing"
                                ? "/template/template-one-hero.webp"
                                : undefined
                            }
                            planetImage={null}
                            serviceId={serviceData.id}
                          />
                        )}
                        {serviceData.show_digital_marketing !== false && (
                          <DigitalMarketingServices
                            marketingTitle={serviceData.digital_marketing_title}
                            marketingSubtitle={
                              serviceData.digital_marketing_subtitle
                            }
                            marketingList={serviceData.digital_marketing_list}
                          />
                        )}
                        {serviceData.show_why_choose_us !== false && (
                          <SocialMediaWhyChooseUs
                            whyTitle={serviceData.why_choose_us_title}
                            whySubtitle={serviceData.why_choose_us_subtitle}
                            whyList={serviceData.why_choose_us_list}
                          />
                        )}
                        {serviceData.show_how_we_work !== false && (
                          <SocialMediaHowWeWork
                            howWeWorkList={serviceData.how_we_work_list}
                          />
                        )}
                        {serviceData.show_request !== false && (
                          <SocialMediaContact />
                        )}
                        {serviceData.show_blogs !== false && <LatestBlogs />}
                      </>
                    );
                  case "cloud-hosting":
                    return (
                      <>
                        {serviceData.show_hero !== false && (
                          <ServicesHero
                            title={serviceData.hero_title || serviceData.title}
                            desc={
                              serviceData.hero_subtitle ||
                              serviceData.description
                            }
                            serviceId={serviceData.id}
                          />
                        )}
                        {serviceData.show_features !== false && (
                          <ServicesFeatures
                            featuresTitle={serviceData.features_title}
                            featuresSubtitle={serviceData.features_subtitle}
                            featuresList={serviceData.features_list}
                          />
                        )}
                        {serviceData.show_hosting_offers !== false && (
                          <HostingOffers
                            offersList={serviceData.hosting_offers_list}
                          />
                        )}
                        {serviceData.show_blogs !== false && <LatestBlogs />}
                      </>
                    );
                  case "ecommerce":
                    return (
                      <>
                        {serviceData.show_hero !== false && (
                          <EcommerceHero
                            data={serviceData}
                            serviceId={serviceData.id}
                          />
                        )}
                        {serviceData.show_features !== false && (
                          <EcommerceFeatures
                            title={serviceData.features_title}
                            subtitle={serviceData.features_subtitle}
                            featuresList={serviceData.features_list}
                          />
                        )}
                        {serviceData.show_blogs !== false && <LatestBlogs />}
                      </>
                    );
                  case "mobile-apps":
                    return (
                      <>
                        {serviceData.show_hero !== false && (
                          <SocialMediaHero
                            title={
                              serviceData.hero_title ||
                              serviceData.title ||
                              t("mobileAppDev")
                            }
                            desc={
                              serviceData.hero_subtitle ||
                              serviceData.description
                            }
                            bgImage="/template/template-two-hero.webp"
                            planetImage={null}
                            serviceId={serviceData.id}
                          />
                        )}
                        {serviceData.show_features !== false && (
                          <WebDesignFeatures
                            title={
                              serviceData.features_title ||
                              t("mobileAppFeaturesTitle")
                            }
                            description={serviceData.features_subtitle || ""}
                            featuresList={serviceData.features_list}
                          />
                        )}
                        {serviceData.show_portfolio !== false && (
                          <WebDesignPortfolio isMobileApp={true} />
                        )}
                        {serviceData.show_how_we_work !== false && (
                          <MobileAppHowWeWork
                            howWeWorkList={serviceData.how_we_work_list}
                          />
                        )}
                        {serviceData.show_request !== false && (
                          <WebDesignRequest />
                        )}
                        {serviceData.show_blogs !== false && <LatestBlogs />}
                      </>
                    );
                  case "digital-identity":
                    return (
                      <>
                        {serviceData.show_hero !== false && (
                          <WebDevHero
                            data={{
                              title:
                                serviceData.hero_title ||
                                serviceData.title ||
                                t("digitalIdentityDev"),
                              desc:
                                serviceData.hero_subtitle ||
                                serviceData.description,
                              bg_image: "/template/template-three-hero.webp",
                              hidePlanet: true,
                              bgPosition: "bg-bottom",
                              overlay: "bg-black/20",
                            }}
                            serviceId={serviceData.id}
                          />
                        )}
                        {serviceData.show_features !== false && (
                          <WebDesignFeatures
                            title={
                              serviceData.features_title ||
                              t("digitalIdentityFeaturesTitle")
                            }
                            description={serviceData.features_subtitle || ""}
                            featuresList={serviceData.features_list}
                          />
                        )}
                        {serviceData.show_portfolio !== false && (
                          <div className="py-10">
                            <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-10">
                              <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
                                {t("diSomeWorks")}
                              </h2>
                            </div>
                            <WebDesignPortfolio />
                          </div>
                        )}
                        {serviceData.show_hosting_offers !== false && (
                          <div className="pt-20">
                            <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-[-50px] relative z-30">
                              <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
                                {t("diSetUpSite")}
                              </h2>
                            </div>
                            <HostingOffers
                              offersList={serviceData.hosting_offers_list}
                            />
                          </div>
                        )}
                        {serviceData.show_how_we_work !== false && (
                          <>
                            <section className="py-20 px-6 lg:px-20 max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12">
                              <div className="w-full lg:w-1/2">
                                <h2 className="text-white text-3xl md:text-5xl font-bold mb-8 leading-tight">
                                  {t("diMakeIdeas")}
                                </h2>
                                <ul className="space-y-4">
                                  {(
                                    serviceData.how_we_work_list || [
                                      { title: t("diStep1") },
                                      { title: t("diStep2") },
                                      { title: t("diStep3") },
                                      { title: t("diStep4") },
                                      { title: t("diStep5") },
                                    ]
                                  ).map((item, idx) => (
                                    <li
                                      key={idx}
                                      className="flex items-center gap-4 text-gray-300"
                                    >
                                      <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                                        <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                                      </div>
                                      <span className="text-lg">
                                        {item.title}
                                      </span>
                                    </li>
                                  ))}
                                </ul>
                              </div>
                              <div className="w-full lg:w-1/2 relative">
                                <Image
                                  src="/template/Mask group.webp"
                                  alt="Analytics Dashboard"
                                  width={700}
                                  height={450}
                                  loading="lazy"
                                  sizes="(max-width: 1024px) 100vw, 50vw"
                                  className="w-full h-auto object-contain drop-shadow-2xl"
                                />
                              </div>
                            </section>
                            <section className="py-20 px-6 lg:px-20 max-w-7xl mx-auto flex flex-col lg:flex-row-reverse items-center gap-12">
                              <div className="w-full lg:w-1/2 flex flex-col items-start">
                                <h2 className="text-white text-3xl md:text-5xl font-bold mb-6 leading-tight">
                                  {t("diBuildOnline")}
                                </h2>
                                <p className="text-gray-400 mb-8 leading-relaxed text-lg">
                                  {t("diGetMassiveSpeed")}
                                </p>
                                <p className="text-sm text-gray-500 uppercase tracking-widest mb-4 font-semibold">
                                  {t("diTrustedBy")}
                                </p>
                                <div className="flex flex-col sm:flex-row items-center gap-8">
                                  <div className="flex -space-x-4">
                                    <div className="w-12 h-12 rounded-full bg-gray-600 border-2 border-[#03071E]"></div>
                                    <div className="w-12 h-12 rounded-full bg-gray-500 border-2 border-[#03071E]"></div>
                                    <div className="w-12 h-12 rounded-full bg-gray-400 border-2 border-[#03071E]"></div>
                                    <div className="w-12 h-12 rounded-full bg-[#D98B50] border-2 border-[#03071E] flex items-center justify-center text-sm font-bold text-white shadow-lg shadow-[#D98B50]/30">
                                      +
                                    </div>
                                  </div>
                                  <button className="px-10 py-4 rounded-full bg-[#D98B50] text-white font-bold hover:bg-orange-600 transition-colors shadow-lg shadow-[#D98B50]/20">
                                    {t("diGetStarted")}
                                  </button>
                                </div>
                              </div>
                              <div className="w-full lg:w-1/2 relative">
                                <Image
                                  src="/template/stephen-phillips-hostreviews-co-uk-shr_Xn8S8QU-unsplash 1.webp"
                                  alt="Laptop showing website"
                                  width={700}
                                  height={450}
                                  loading="lazy"
                                  sizes="(max-width: 1024px) 100vw, 50vw"
                                  className="w-full h-auto object-contain drop-shadow-[0_0_40px_rgba(255,255,255,0.05)]"
                                />
                              </div>
                            </section>
                          </>
                        )}
                        {serviceData.show_blogs !== false && <LatestBlogs />}
                      </>
                    );
                  default:
                    return (
                      <div className="text-center py-20">
                        <h2 className="text-2xl font-bold">
                          Unknown Service Template
                        </h2>
                      </div>
                    );
                }
              })()}
        </div>
      )}

      {template === "about" && (
        <>
          <AboutHero data={pageData} />
          <Experience />
          <Solutions />
        </>
      )}

      {template === "contact" && (
        <>
          <div className="w-full max-w-7xl mx-auto px-6 relative z-10 pt-32 pb-10">
            <div className="flex flex-col lg:flex-row items-start justify-between">
              <ContactInfo data={pageData} />
              <ContactForm />
            </div>
            <ContactMap />
          </div>
        </>
      )}

      {template === "portfolio" && (
        <div className="w-full relative z-10 pt-24">
          <PortfolioGallery data={pageData} perPage={4} />
          <CTA />
        </div>
      )}

      {template === "blog" && (
        <div className="w-full relative z-10 flex flex-col items-center pt-24">
          <BlogHero data={pageData} posts={posts} />
          <BlogGrid posts={posts} />
          <Newsletter />
        </div>
      )}

      {template === "faq" && (
        <div className="pt-24 pb-20 w-full relative z-10">
          <FAQContent data={pageData} />
        </div>
      )}

      {template === "custom" && (
        <div className="pt-24 pb-20">
          <div className="max-w-7xl mx-auto px-6 lg:px-20">
            {pageData.sections?.blocks?.map((block, index) => {
              switch (block.type) {
                case "hero":
                  return (
                    <div key={index} className="mb-16 text-center">
                      <h1 className="text-4xl md:text-6xl font-bold mb-4">
                        {block.data.title?.[language]}
                      </h1>
                      <p className="text-xl text-gray-400 max-w-3xl mx-auto">
                        {block.data.subtitle?.[language]}
                      </p>
                      {block.data.image && (
                        <div className="mt-8 rounded-2xl overflow-hidden border border-white/10 relative">
                          <Image
                            src={`${STORAGE_URL}/storage/${block.data.image}`}
                            alt="Hero"
                            width={1200}
                            height={600}
                            loading="lazy"
                            sizes="(max-width: 1024px) 100vw, 1200px"
                            className="w-full h-auto"
                          />
                        </div>
                      )}
                    </div>
                  );
                case "text_block":
                  return (
                    <div
                      key={index}
                      className="mb-16 bg-white/5 p-8 rounded-2xl border border-white/10"
                    >
                      <h2 className="text-3xl font-bold mb-6 text-[#FF8A00]">
                        {block.data.heading?.[language]}
                      </h2>
                      <div
                        className="prose prose-invert max-w-none"
                        dangerouslySetInnerHTML={{
                          __html: sanitizeHtml(block.data.content?.[language]),
                        }}
                      ></div>
                    </div>
                  );
                case "features_grid":
                  return (
                    <div key={index} className="mb-16">
                      <h2 className="text-3xl font-bold mb-10 text-center">
                        {block.data.section_title?.[language]}
                      </h2>
                      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {block.data.items?.map((item, i) => (
                          <div
                            key={i}
                            className="bg-black/40 backdrop-blur-md p-6 rounded-2xl border border-white/10 hover:border-[#FF8A00]/50 transition-colors"
                          >
                            <h3 className="text-xl font-bold mb-2 text-white">
                              {item.title?.[language]}
                            </h3>
                            <p className="text-gray-400">
                              {item.desc?.[language]}
                            </p>
                          </div>
                        ))}
                      </div>
                    </div>
                  );
                case "cta_block":
                  return (
                    <div
                      key={index}
                      className="mb-16 bg-gradient-to-r from-[#FF8A00]/20 to-[#FF8A00]/5 border border-[#FF8A00]/20 p-10 rounded-3xl text-center relative overflow-hidden"
                    >
                      <div className="absolute top-0 right-0 w-64 h-64 bg-[#FF8A00]/10 blur-[80px] rounded-full"></div>
                      <div className="relative z-10">
                        <h2 className="text-3xl md:text-5xl font-bold mb-4 text-white">
                          {block.data.title?.[language]}
                        </h2>
                        <p className="text-lg text-gray-300 mb-8 max-w-2xl mx-auto">
                          {block.data.subtitle?.[language]}
                        </p>
                        {block.data.button_text?.[language] && (
                          <a
                            href={safeUrl(block.data.button_link)}
                            className="inline-block bg-[#FF8A00] text-white px-8 py-4 rounded-full font-bold hover:scale-105 transition-transform shadow-[0_0_20px_rgba(255,138,0,0.3)]"
                          >
                            {block.data.button_text?.[language]}
                          </a>
                        )}
                      </div>
                    </div>
                  );
                case "faq_block":
                  return (
                    <div key={index} className="mb-16 max-w-4xl mx-auto">
                      <h2 className="text-3xl font-bold mb-8 text-center text-white">
                        {block.data.section_title?.[language]}
                      </h2>
                      <div className="space-y-4">
                        {block.data.faqs?.map((faq, i) => (
                          <div
                            key={i}
                            className="bg-white/5 border border-white/10 rounded-xl overflow-hidden"
                          >
                            <div className="p-5 font-bold text-lg text-white border-b border-white/10 bg-white/5">
                              {faq.question?.[language]}
                            </div>
                            <div
                              className="p-5 text-gray-300 prose prose-invert max-w-none"
                              dangerouslySetInnerHTML={{
                                __html: sanitizeHtml(faq.answer?.[language]),
                              }}
                            ></div>
                          </div>
                        ))}
                      </div>
                    </div>
                  );
                case "split_block":
                  const isRight = block.data.image_on_right;
                  return (
                    <div
                      key={index}
                      className={`mb-16 flex flex-col ${isRight ? "md:flex-row" : "md:flex-row-reverse"} items-center gap-10`}
                    >
                      <div className="flex-1">
                        <h2 className="text-3xl md:text-4xl font-bold mb-6 text-[#FF8A00]">
                          {block.data.title?.[language]}
                        </h2>
                        <div
                          className="prose prose-invert max-w-none text-gray-300 text-lg leading-relaxed"
                          dangerouslySetInnerHTML={{
                            __html: sanitizeHtml(block.data.content?.[language]),
                          }}
                        ></div>
                      </div>
                      <div className="flex-1 w-full">
                        {block.data.image && (
                          <div className="rounded-2xl overflow-hidden shadow-[0_0_30px_rgba(255,138,0,0.1)] border border-white/10 relative">
                            <Image
                              src={`${STORAGE_URL}/storage/${block.data.image}`}
                              alt="Split Content"
                              width={800}
                              height={500}
                              loading="lazy"
                              sizes="(max-width: 1024px) 100vw, 50vw"
                              className="w-full h-auto object-cover"
                            />
                          </div>
                        )}
                      </div>
                    </div>
                  );
                case "stats_block":
                  return (
                    <div
                      key={index}
                      className="mb-16 grid grid-cols-2 md:grid-cols-4 gap-6"
                    >
                      {block.data.stats?.map((stat, i) => (
                        <div
                          key={i}
                          className="text-center p-8 bg-black/40 backdrop-blur-md rounded-2xl border border-white/10"
                        >
                          <h3 className="text-4xl md:text-5xl font-bold text-[#FF8A00] mb-2">
                            {stat.number}
                          </h3>
                          <p className="text-gray-300 font-medium text-lg">
                            {stat.label?.[language]}
                          </p>
                        </div>
                      ))}
                    </div>
                  );
                default:
                  return null;
              }
            })}
          </div>
        </div>
      )}
    </main>
  );
}

// ─────────────────────────────────────────────────────
// Block Renderer — renders each block by type
// ─────────────────────────────────────────────────────
function RenderBlock({
  block,
  heroStyle,
  defaultHeroImage,
  serviceId,
  language,
  t,
}) {
  const data = block.data || {};
  const heroImage = data.hero_image
    ? `${STORAGE_URL}/storage/${data.hero_image}`
    : defaultHeroImage;
  const resolveText = (field) => {
    if (!field) return "";
    if (typeof field === "object") {
      return field[language] || field.en || "";
    }
    return field;
  };

  switch (block.type) {
    // ── HERO ──
    case "hero":
      if (heroStyle === "web_dev_hero") {
        return (
          <WebDevHero
            data={{
              title: resolveText(data.title),
              subtitle: resolveText(data.subtitle),
              desc: resolveText(data.subtitle),
              bg_image: heroImage,
              hidePlanet: true,
              bgPosition: "bg-bottom",
              overlay: "bg-black/20",
            }}
            serviceId={serviceId}
          />
        );
      }
      // Default: social_media_hero
      return (
        <SocialMediaHero
          title={resolveText(data.title)}
          desc={resolveText(data.subtitle)}
          bgImage={heroImage}
          planetImage={null}
          serviceId={serviceId}
        />
      );

    // ── FEATURES ──
    case "features":
      if (serviceId === "cloud-hosting") {
        return (
          <ServicesFeatures
            title={resolveText(data.title)}
            featuresList={data.items}
          />
        );
      }
      return (
        <WebDesignFeatures
          title={resolveText(data.title)}
          description={resolveText(data.subtitle)}
          featuresList={data.items}
        />
      );

    // ── HOW WE WORK (Timeline) ──
    case "how_we_work_timeline":
      return <SocialMediaHowWeWork howWeWorkList={data.items} />;

    // ── HOW WE WORK (Pipeline Steps) ──
    case "how_we_work_steps":
      return <WebDevHowWeWork howWeWorkList={data.items} />;

    // ── HOW WE WORK (Mobile) ──
    case "how_we_work_mobile":
      return <MobileAppHowWeWork howWeWorkList={data.items} />;

    // ── DIGITAL MARKETING ──
    case "digital_marketing":
      return (
        <DigitalMarketingServices
          marketingTitle={resolveText(data.title)}
          marketingSubtitle={resolveText(data.subtitle)}
          marketingList={data.items}
        />
      );

    // ── WHY CHOOSE US ──
    case "why_choose_us":
      return (
        <SocialMediaWhyChooseUs
          whyTitle={resolveText(data.title)}
          whySubtitle={resolveText(data.subtitle)}
          whyList={data.items}
        />
      );

    // ── PORTFOLIO ──
    case "portfolio":
      return (
        <>
          {(data.section_title) && (
            <div className="py-10">
              <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-10">
                <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
                  {resolveText(data.section_title)}
                </h2>
              </div>
            </div>
          )}
          <WebDesignPortfolio isMobileApp={data.is_mobile_app || false} />
        </>
      );

    // ── CONTACT / REQUEST ──
    case "contact_request":
      if (data.style === "web_design") {
        return <WebDesignRequest />;
      }
      return <SocialMediaContact />;

    // ── HOSTING OFFERS ──
    case "hosting_offers":
      return (
        <HostingOffers
          title={resolveText(data.title)}
          offersList={data.items}
        />
      );

    // ── BLOGS ──
    case "blogs":
      return <LatestBlogs />;

    // ── E-COMMERCE FEATURES ──
    case "ecommerce_features":
      return (
        <EcommerceFeatures
          title={resolveText(data.title)}
          subtitle={resolveText(data.subtitle)}
          featuresList={data.items}
        />
      );

    // ── SPLIT CONTENT ──
    case "split_content": {
      const imageUrl = data.image
        ? `${STORAGE_URL}/storage/${data.image}`
        : null;
      const isRight = data.image_on_right !== false;
      return (
        <section
          className={`py-20 px-6 lg:px-20 max-w-7xl mx-auto flex flex-col ${
            isRight ? "lg:flex-row" : "lg:flex-row-reverse"
          } items-center gap-12`}
        >
          <div className="w-full lg:w-1/2">
            <h2 className="text-white text-3xl md:text-5xl font-bold mb-8 leading-tight">
              {resolveText(data.title)}
            </h2>
            {data.content && (
              <p className="text-gray-400 mb-8 leading-relaxed text-lg">
                {resolveText(data.content)}
              </p>
            )}
            {data.items && data.items.length > 0 && (
              <ul className="space-y-4">
                {data.items.map((item, idx) => (
                  <li
                    key={idx}
                    className="flex items-center gap-4 text-gray-300"
                  >
                    <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                      <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                    </div>
                    <span className="text-lg">
                      {resolveText(item.title)}
                    </span>
                  </li>
                ))}
              </ul>
            )}
          </div>
          <div className="w-full lg:w-1/2 relative">
            {imageUrl && (
              <Image
                src={imageUrl}
                alt={resolveText(data.title) || "Content"}
                width={700}
                height={500}
                loading="lazy"
                sizes="(max-width: 1024px) 100vw, 50vw"
                className="w-full h-auto object-contain drop-shadow-2xl"
              />
            )}
          </div>
        </section>
      );
    }

    default:
      return null;
  }
}
