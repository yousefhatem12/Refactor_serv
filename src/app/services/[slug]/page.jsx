"use client";
import React, { useState, useEffect } from "react";
import { useParams, useRouter, notFound } from "next/navigation";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import { useLanguage } from "../../context/LanguageContext";
import { useSettings } from "../../context/SettingsContext";

import Loading from "../../loading";
import WebDevHero from "../../components/Services-web-dev/WebDevHero";
import WebDesignFeatures from "../../components/Services-web-design/WebDesignFeatures";
import WebDevHowWeWork from "../../components/Services-web-dev/WebDevHowWeWork";

// UI/UX Design
import WebDesignHero from "../../components/Services-web-design/WebDesignHero";
import WebDesignPortfolio from "../../components/Services-web-design/WebDesignPortfolio";
import WebDesignRequest from "../../components/Services-web-design/WebDesignRequest";

// Social Media
import SocialMediaHero from "../../components/Services-social-media/SocialMediaHero";
import DigitalMarketingServices from "../../components/Services-social-media/DigitalMarketingServices";
import SocialMediaWhyChooseUs from "../../components/Services-social-media/SocialMediaWhyChooseUs";
import SocialMediaHowWeWork from "../../components/Services-social-media/SocialMediaHowWeWork";
import SocialMediaContact from "../../components/Services-social-media/SocialMediaContact";

// Cloud Hosting
import ServicesHero from "../../components/Services-web-hosting/ServicesHero";
import ServicesFeatures from "../../components/Services-web-hosting/ServicesFeatures";
import HostingOffers from "../../components/Services-web-hosting/HostingOffers";

// E-commerce
import EcommerceHero from "../../components/Services-ecommerce/EcommerceHero";
import EcommerceFeatures from "../../components/Services-ecommerce/EcommerceFeatures";

// Mobile Apps
import MobileAppHowWeWork from "../../components/Services-web-dev/MobileAppHowWeWork";

// Shared
import LatestBlogs from "../../components/Services-web-hosting/LatestBlogs";
import QuotationForm from "../../components/Quotation/QuotationForm";

// ── Default hero images per template ──
// Hero images and styles are now fetched dynamically from the API (ServiceTemplate model)



export default function DynamicServicePage() {
  const { slug } = useParams();
  const { language, t } = useLanguage();
  const settings = useSettings();
  const router = useRouter();
  const [serviceData, setServiceData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [notFoundError, setNotFoundError] = useState(false);

  const apiSlug = slug?.startsWith("services-") ? slug.replace("services-", "") : slug;

  useEffect(() => {
    if (!apiSlug) return;

    fetchAPI(`/services/${apiSlug}?lang=${language}`)
      .then((data) => {
        if (!data || data.message === "Service not found") {
          setNotFoundError(true);
          return;
        }
        // Unwrap standard Laravel resource wrapper if present
        setServiceData(data.data ? data.data : data);
      })
      .catch((error) => {
        console.error("Error fetching service:", error);
        setNotFoundError(true);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [apiSlug, language, router]);

  useEffect(() => {
    if (serviceData) {
      document.title = serviceData.title;
      const desc = serviceData.description;
      if (desc) {
        let metaDesc = document.querySelector('meta[name="description"]');
        if (metaDesc) {
          metaDesc.setAttribute("content", desc);
        } else {
          metaDesc = document.createElement("meta");
          metaDesc.setAttribute("name", "description");
          metaDesc.setAttribute("content", desc);
          document.head.appendChild(metaDesc);
        }
      }
    }
  }, [serviceData]);

  if (notFoundError) {
    notFound();
  }

  if (loading) {
    return <Loading />;
  }

  if (!serviceData) return null;

  const heroStyle = serviceData.hero_style || "social_media_hero";
  const defaultHeroImage = serviceData.default_hero_image || "/template/template-one-hero.webp";
  const blocks = serviceData.blocks;

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  const getServiceImage = () => {
    if (blocks && Array.isArray(blocks)) {
      const heroBlock = blocks.find(b => b.type === "hero");
      if (heroBlock?.data?.hero_image) {
        return getMediaUrl(heroBlock.data.hero_image);
      }
    }
    if (serviceData?.template === "mobile-apps" || serviceData?.id === "mobile-apps") {
      return `${siteUrl}/template/template-two-hero.webp`;
    }
    return `${siteUrl}/template/template-one-hero.webp`;
  };

  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : `${siteUrl}/logo.png`;

  const serviceSchema = {
    "@context": "https://schema.org",
    "@type": "Service",
    name: serviceData?.title,
    description: serviceData?.description,
    url: `${siteUrl}/services/${serviceData?.slug || serviceData?.id}`,
    provider: {
      "@type": "Organization",
      name: settings?.site_title || "Serv5",
      url: siteUrl,
      logo: siteLogo,
    },
    image: getServiceImage(),
  };

  // ═══════════════════════════════════════════════════
  // NEW FORMAT: blocks array exists → render in order
  // ═══════════════════════════════════════════════════
  if (blocks && Array.isArray(blocks) && blocks.length > 0) {
    return (
      <main className="flex min-h-screen flex-col relative overflow-x-hidden">
        {serviceSchema && (
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{ __html: JSON.stringify(serviceSchema) }}
          />
        )}
        <div className="w-full relative z-10 pb-10 pt-20">
          {blocks.map((block, index) => (
            <RenderBlock
              key={index}
              block={block}
              heroStyle={heroStyle}
              defaultHeroImage={defaultHeroImage}
              serviceId={serviceData.id}
              language={language}
              t={t}
            />
          ))}
        </div>
      </main>
    );
  }

  // ═══════════════════════════════════════════════════
  // OLD FORMAT: backward compatibility (flat sections)
  // ═══════════════════════════════════════════════════
  return <OldFormatRenderer serviceData={serviceData} language={language} t={t} serviceSchema={serviceSchema} />;
}

// ─────────────────────────────────────────────────────
// Block Renderer — renders each block by type
// ─────────────────────────────────────────────────────
function RenderBlock({ block, heroStyle, defaultHeroImage, serviceId, language, t }) {
  const data = block.data || {};
  const heroImage = data.hero_image
    ? getMediaUrl(data.hero_image)
    : defaultHeroImage;

  switch (block.type) {
    // ── HERO ──
    case "hero":
      if (heroStyle === "web_dev_hero") {
        return (
          <WebDevHero
            data={{
              title: data.title?.[language] || data.title?.en || "",
              desc: data.subtitle?.[language] || data.subtitle?.en || "",
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
          title={data.title?.[language] || data.title?.en || ""}
          desc={data.subtitle?.[language] || data.subtitle?.en || ""}
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
            title={data.title?.[language] || data.title?.en || ""}
            featuresList={data.items}
          />
        );
      }
      return (
        <WebDesignFeatures
          title={data.title?.[language] || data.title?.en || ""}
          description={data.subtitle?.[language] || data.subtitle?.en || ""}
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
          marketingTitle={data.title?.[language] || data.title?.en || ""}
          marketingSubtitle={data.subtitle?.[language] || data.subtitle?.en || ""}
          marketingList={data.items}
        />
      );

    // ── WHY CHOOSE US ──
    case "why_choose_us":
      return (
        <SocialMediaWhyChooseUs
          whyTitle={data.title?.[language] || data.title?.en || ""}
          whySubtitle={data.subtitle?.[language] || data.subtitle?.en || ""}
          whyList={data.items}
        />
      );

    // ── PORTFOLIO ──
    case "portfolio":
      return (
        <>
          {(data.section_title?.en || data.section_title?.ar) && (
            <div className="py-10">
              <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-10">
                <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
                  {data.section_title?.[language] || data.section_title?.en || ""}
                </h2>
              </div>
            </div>
          )}
          <WebDesignPortfolio isMobileApp={data.is_mobile_app || false} />
        </>
      );

    // ── CONTACT / REQUEST ──
    case "contact_request":
      return <QuotationForm />;

    // ── HOSTING OFFERS ──
    case "hosting_offers":
      return (
        <HostingOffers
          title={data.title?.[language] || data.title?.en || ""}
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
          title={data.title?.[language] || data.title?.en || ""}
          subtitle={data.subtitle?.[language] || data.subtitle?.en || ""}
          featuresList={data.items}
        />
      );

    // ── SPLIT CONTENT ──
    case "split_content": {
      const imageUrl = data.image ? getMediaUrl(data.image) : null;
      const isRight = data.image_on_right !== false;
      return (
        <section
          className={`py-20 px-6 lg:px-20 max-w-7xl mx-auto flex flex-col ${
            isRight ? "lg:flex-row" : "lg:flex-row-reverse"
          } items-center gap-12`}
        >
          <div className="w-full lg:w-1/2">
            <h2 className="text-white text-3xl md:text-5xl font-bold mb-8 leading-tight">
              {data.title?.[language] || data.title?.en || ""}
            </h2>
            {data.content && (
              <p className="text-gray-400 mb-8 leading-relaxed text-lg">
                {data.content?.[language] || data.content?.en || ""}
              </p>
            )}
            {data.items && data.items.length > 0 && (
              <ul className="space-y-4">
                {data.items.map((item, idx) => (
                  <li key={idx} className="flex items-center gap-4 text-gray-300">
                    <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                      <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                    </div>
                    <span className="text-lg">
                      {item.title?.[language] || item.title?.en || ""}
                    </span>
                  </li>
                ))}
              </ul>
            )}
          </div>
          <div className="w-full lg:w-1/2 relative">
            {imageUrl && (
              <img
                src={imageUrl}
                alt={data.title?.en || "Content"}
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

// ─────────────────────────────────────────────────────
// Old Format Renderer — backward compatibility
// ─────────────────────────────────────────────────────
function OldFormatRenderer({ serviceData, language, t, serviceSchema }) {
  const getServiceTemplate = (data) => {
    if (data.template) return data.template;
    const id = data.id || "";
    if (id.includes("web-development")) return "web-development";
    if (id.includes("ui-ux-design") || id.includes("web-design")) return "ui-ux-design";
    if (id.includes("social-media") || id.includes("seo") || id.includes("marketing")) return "social-media";
    if (id.includes("cloud-hosting") || id.includes("web-hosting") || id.includes("hosting")) return "cloud-hosting";
    if (id.includes("ecommerce") || id.includes("online-stores") || id.includes("shop")) return "ecommerce";
    if (id.includes("mobile-apps") || id.includes("app-development")) return "mobile-apps";
    if (id.includes("digital-identity")) return "digital-identity";
    return "web-development";
  };

  const serviceTemplate = getServiceTemplate(serviceData);

  const renderOldTemplate = () => {
    switch (serviceTemplate) {
      case "web-development":
        return (
          <>
            {serviceData.show_hero !== false && <WebDevHero data={serviceData} serviceId={serviceData.id} />}
            {serviceData.show_features !== false && (
              <WebDesignFeatures title={serviceData.features_title || t("webDevFeaturesTitle")} description={serviceData.features_subtitle} featuresList={serviceData.features_list} />
            )}
            {serviceData.show_how_we_work !== false && <WebDevHowWeWork howWeWorkList={serviceData.how_we_work_list} />}
            {serviceData.show_blogs !== false && <LatestBlogs />}
          </>
        );
      case "ui-ux-design":
        return (
          <>
            {serviceData.show_hero !== false && <WebDesignHero data={serviceData} serviceId={serviceData.id} />}
            {serviceData.show_features !== false && (
              <WebDesignFeatures title={serviceData.features_title} description={serviceData.features_subtitle} featuresList={serviceData.features_list} />
            )}
            {serviceData.show_portfolio !== false && <WebDesignPortfolio />}
            {serviceData.show_request !== false && <QuotationForm />}
            {serviceData.show_blogs !== false && <LatestBlogs />}
          </>
        );
      case "social-media":
        return (
          <>
            {serviceData.show_hero !== false && (
              <SocialMediaHero
                title={serviceData.hero_title || serviceData.title}
                desc={serviceData.hero_subtitle || serviceData.description}
                bgImage={serviceData.id === "seo-optimization" || serviceData.id === "network-marketing" ? "/template/template-one-hero.webp" : undefined}
                planetImage={null}
                serviceId={serviceData.id}
              />
            )}
            {serviceData.show_digital_marketing !== false && (
              <DigitalMarketingServices marketingTitle={serviceData.digital_marketing_title} marketingSubtitle={serviceData.digital_marketing_subtitle} marketingList={serviceData.digital_marketing_list} />
            )}
            {serviceData.show_why_choose_us !== false && (
              <SocialMediaWhyChooseUs whyTitle={serviceData.why_choose_us_title} whySubtitle={serviceData.why_choose_us_subtitle} whyList={serviceData.why_choose_us_list} />
            )}
            {serviceData.show_how_we_work !== false && <SocialMediaHowWeWork howWeWorkList={serviceData.how_we_work_list} />}
            {serviceData.show_request !== false && <QuotationForm />}
            {serviceData.show_blogs !== false && <LatestBlogs />}
          </>
        );
      case "cloud-hosting":
        return (
          <>
            {serviceData.show_hero !== false && (
              <ServicesHero title={serviceData.hero_title || serviceData.title} desc={serviceData.hero_subtitle || serviceData.description} serviceId={serviceData.id} />
            )}
            {serviceData.show_features !== false && (
              <ServicesFeatures featuresTitle={serviceData.features_title} featuresSubtitle={serviceData.features_subtitle} featuresList={serviceData.features_list} />
            )}
            {serviceData.show_hosting_offers !== false && <HostingOffers offersList={serviceData.hosting_offers_list} />}
            {serviceData.show_blogs !== false && <LatestBlogs />}
          </>
        );
      case "ecommerce":
        return (
          <>
            {serviceData.show_hero !== false && <EcommerceHero data={serviceData} serviceId={serviceData.id} />}
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
              <SocialMediaHero title={serviceData.hero_title || serviceData.title || t("mobileAppDev")} desc={serviceData.hero_subtitle || serviceData.description} bgImage="/template/template-two-hero.webp" planetImage={null} serviceId={serviceData.id} />
            )}
            {serviceData.show_features !== false && (
              <WebDesignFeatures title={serviceData.features_title || t("mobileAppFeaturesTitle")} description={serviceData.features_subtitle || ""} featuresList={serviceData.features_list} />
            )}
            {serviceData.show_portfolio !== false && <WebDesignPortfolio isMobileApp={true} />}
            {serviceData.show_how_we_work !== false && <MobileAppHowWeWork howWeWorkList={serviceData.how_we_work_list} />}
            {serviceData.show_request !== false && <QuotationForm />}
            {serviceData.show_blogs !== false && <LatestBlogs />}
          </>
        );
      default:
        return (
          <>
            {serviceData.show_hero !== false && (
              <ServicesHero title={serviceData.hero_title || serviceData.title} desc={serviceData.hero_subtitle || serviceData.description} serviceId={serviceData.id} />
            )}
            {serviceData.show_features !== false && (
              <ServicesFeatures featuresTitle={serviceData.features_title} featuresSubtitle={serviceData.features_subtitle} featuresList={serviceData.features_list} />
            )}
            {serviceData.show_blogs !== false && <LatestBlogs />}
          </>
        );
    }
  };

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      {serviceSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(serviceSchema) }}
        />
      )}
      <div className="w-full relative z-10 pb-10 pt-20">
        {renderOldTemplate()}
      </div>
    </main>
  );
}
