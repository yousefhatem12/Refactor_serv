"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import { useParams } from "next/navigation";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import Loading from "../../loading";
import { useLanguage } from "../../context/LanguageContext";
import { useSettings } from "../../context/SettingsContext";

export default function BlogPostDetails() {
  const { language } = useLanguage();
  const settings = useSettings();
  const params = useParams();
  const slug = params?.id; // The folder is still named [id] but it receives the slug from URL
  const [post, setPost] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [imgError, setImgError] = useState(false);

  // Adjust state during render to avoid synchronous setState inside useEffect
  const [prevSlug, setPrevSlug] = useState(slug);
  const [prevLang, setPrevLang] = useState(language);

  if (slug !== prevSlug || language !== prevLang) {
    setPrevSlug(slug);
    setPrevLang(language);
    setIsLoading(true);
  }

  useEffect(() => {
    if (slug) {
      setImgError(false);
      fetchAPI(`/blog/${slug}?lang=${language}`)
        .then((res) => {
          if (res) setPost(res.data || res);
          setIsLoading(false);
        })
        .catch((err) => {
          console.error(err);
          setIsLoading(false);
        });
    }
  }, [slug, language]);

  useEffect(() => {
    if (post) {
      document.title = post.seo?.title || post.title;
      const desc = post.seo?.description || post.title;
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
  }, [post]);

  if (isLoading) {
    return <Loading />;
  }



  if (!post) {
    return (
      <div className="min-h-screen flex items-center justify-center text-white bg-[#040A37]">
        <h1 className="text-2xl font-bold">
          {language === "ar" ? "المقال غير موجود" : "Post not found"}
        </h1>
      </div>
    );
  }

  const isRtl = language === "ar";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const postUrl = `${siteUrl}/${language}/blog/${slug}`;
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : `${siteUrl}/logo.svg`;

  const articleSchema = post
    ? {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        headline: post.title,
        image: post.image ? [post.image] : [],
        datePublished: post.created_at_iso || post.published_at,
        dateModified: post.updated_at_iso || post.created_at_iso || post.published_at,
        author: {
          "@type": "Person",
          name: post.author || "Serv5 Team",
        },
        publisher: {
          "@type": "Organization",
          name: settings?.site_title || "Serv5",
          logo: {
            "@type": "ImageObject",
            url: siteLogo,
          },
        },
        description: post.seo?.description || post.title,
        mainEntityOfPage: {
          "@type": "WebPage",
          "@id": postUrl,
        },
      }
    : null;

  const fallbackLogo = settings?.site_logo ? getMediaUrl(settings.site_logo) : "/logo.png";
  const displayImage = (!post?.image || imgError) ? fallbackLogo : post.image;
  const isFallback = !post?.image || imgError;

  return (
    <main
      className="flex min-h-screen flex-col relative overflow-x-hidden pt-10 pb-10"
      dir={isRtl ? "rtl" : "ltr"}
    >
      {articleSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(articleSchema) }}
        />
      )}
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      <div className="w-full relative z-10 flex flex-col items-center pt-24 text-white">
        <section className="w-full max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10">
          <div className="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
            <div className="w-full lg:w-1/2">
              <span className="text-[#FF8A00] font-bold mb-4 block uppercase tracking-widest">
                {post.category ||
                  (language === "ar" ? "تكنولوجيا" : "Technology")}
              </span>
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight uppercase">
                {post.title}
              </h1>
              <div
                className="text-gray-300 text-lg leading-relaxed mb-10 article-content"
                dangerouslySetInnerHTML={{ __html: post.content }}
              />
              <div className="flex items-center gap-4 text-sm text-gray-400">
                <span>
                  {language === "ar" ? "بواسطة" : "By"} {post.author}
                </span>
                <span>•</span>
                <span>{post.published_at}</span>
              </div>
            </div>

            <div className="w-full lg:w-1/2 relative h-[300px] sm:h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-2xl border border-white/10 group bg-gray-800 flex items-center justify-center">
              {displayImage && (
                <Image
                  src={displayImage}
                  alt={post.title}
                  fill
                  className={
                    isFallback
                      ? "object-contain p-16 opacity-40 group-hover:scale-105 transition-transform duration-700"
                      : "object-cover object-center group-hover:scale-105 transition-transform duration-700"
                  }
                  onError={() => setImgError(true)}
                  priority
                />
              )}
            </div>
          </div>
        </section>
      </div>
    </main>
  );
}
