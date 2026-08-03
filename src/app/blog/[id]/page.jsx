"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import { useParams } from "next/navigation";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import Loading from "../../loading";
import { useLanguage } from "../../context/LanguageContext";
import { useSettings } from "../../context/SettingsContext";

function ArticleTable({ table }) {
  if (!table) return null;

  return (
    <div className="overflow-x-auto my-8 w-full">
      <table className="w-full border-collapse border border-white/20 text-white text-sm sm:text-base">
        <thead>
          <tr className="bg-white/10">
            {table.head.map((heading, index) => (
              <th className="border border-white/20 px-4 py-3 text-start font-bold" key={`${heading}-${index}`}>{heading}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {table.rows.map((row, rowIndex) => (
            <tr className="hover:bg-white/5 transition-colors" key={rowIndex}>
              {table.head.map((heading, columnIndex) => (
                <td className="border border-white/20 px-4 py-3" key={`${heading}-${columnIndex}`}>
                  {row[heading] ?? ""}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

function ArticleLists({ lists }) {
  if (!lists || lists.length === 0) return null;
  return lists.map((entry, index) => {
    const Tag = entry.type === "ordered" ? "ol" : "ul";

    return (
      <Tag key={index} className={`my-6 pl-6 pr-6 ${entry.type === "ordered" ? "list-decimal" : "list-disc"} text-gray-300 space-y-2`}>
        {entry.list.map((item, itemIndex) => (
          <li key={itemIndex} className="leading-relaxed">{item}</li>
        ))}
      </Tag>
    );
  });
}

export default function BlogPostDetails() {
  const { language } = useLanguage();
  const settings = useSettings();
  const params = useParams();
  const slug = params?.id; // The folder is still named [id] but it receives the slug from URL
  const [post, setPost] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : `${siteUrl}/logo.svg`;

  const [imgSrc, setImgSrc] = useState("");

  useEffect(() => {
    if (post) {
      setImgSrc(post.image ? getMediaUrl(post.image) : siteLogo);
    }
  }, [post, siteLogo]);

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
  const postUrl = `${siteUrl}/${language}/blog/${slug}`;

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

  let cleanContent = post.content || "";
  if (post.table) {
    cleanContent = cleanContent.replace(/<table[^>]*>[\s\S]*?<\/table>/gi, "");
  }
  if (post.list && post.list.length > 0) {
    cleanContent = cleanContent.replace(/<ul[^>]*>[\s\S]*?<\/ul>/gi, "");
    cleanContent = cleanContent.replace(/<ol[^>]*>[\s\S]*?<\/ol>/gi, "");
  }

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
        <section className="w-full max-w-5xl mx-auto px-6 lg:px-8 mb-32 relative z-10">

          {/* Header: category + title + meta */}
          <div className="mb-8 text-center">
            <span className="text-[#FF8A00] font-bold mb-3 block uppercase tracking-widest text-sm">
              {post.category || (language === "ar" ? "تكنولوجيا" : "Technology")}
            </span>
            <h1 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">
              {post.title}
            </h1>
            <div className="flex items-center justify-center gap-4 text-sm text-gray-400">
              <span>{language === "ar" ? "بواسطة" : "By"} {post.author}</span>
              <span>•</span>
              <span>{post.published_at}</span>
            </div>
          </div>

          {/* Image — full width */}
          <div className="w-full relative h-[280px] sm:h-[380px] md:h-[480px] lg:h-[540px] rounded-2xl overflow-hidden shadow-2xl border border-white/10 group bg-gray-800 flex items-center justify-center mb-12">
            <Image
              src={imgSrc || siteLogo}
              alt={post.title}
              fill
              className={imgSrc === siteLogo
                ? "w-1/2 h-1/2 object-contain group-hover:scale-105 transition-transform duration-700"
                : "object-cover object-center group-hover:scale-105 transition-transform duration-700"
              }
              priority
              onError={() => setImgSrc(siteLogo)}
            />
          </div>

          {/* Content — below the image, full width */}
          <div className="article-content max-w-none">
            <div
              className="text-gray-300 text-lg leading-relaxed mb-6 font-normal prose prose-invert max-w-none"
              dangerouslySetInnerHTML={{ __html: cleanContent }}
            />
            <ArticleTable table={post.table} />
            <ArticleLists lists={post.list} />
          </div>

        </section>
      </div>
    </main>
  );
}
