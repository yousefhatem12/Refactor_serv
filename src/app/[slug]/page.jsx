"use client";
import React, { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { fetchAPI } from "../../lib/api";
import { useLanguage } from "../context/LanguageContext";
import Navbar from "../components/Navbar/Navbar";
import Footer from "../components/Footer/Footer";
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

export default function DynamicPage() {
  const { slug } = useParams();
  const { language } = useLanguage();
  const router = useRouter();
  const [pageData, setPageData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [posts, setPosts] = useState([]);

  useEffect(() => {
    const loadPage = async () => {
      try {
        const data = await fetchAPI(`/pages/${slug}?lang=${language}`);
        if (!data || data.message === "Page not found") {
          router.push("/404");
          return;
        }
        setPageData(data);
        
        if (data.sections?.template === 'blog') {
            const blogData = await fetchAPI(`/blog?lang=${language}`);
            if (blogData) setPosts(blogData);
        }
      } catch (error) {
        console.error("Error loading page:", error);
        router.push("/404");
      } finally {
        setLoading(false);
      }
    };

    if (slug && !['home', 'about', 'faq', 'blog', 'portfolio', 'contact-us'].includes(slug)) {
      loadPage();
    } else if (slug) {
        setLoading(false); // Should be handled by specific routes
    }
  }, [slug, language, router]);

  if (loading) {
    return (
      <div className="min-h-screen bg-[#020617] flex items-center justify-center">
        <div className="w-16 h-16 border-4 border-[#FF8A00] border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  if (!pageData) return null;

  const template = pageData.sections?.template || "custom";

  return (
    <main className="min-h-screen bg-[#020617] text-white">
      <Navbar />

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
          <PortfolioGallery data={pageData} />
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
                      <h1 className="text-4xl md:text-6xl font-bold mb-4">{block.data.title?.[language]}</h1>
                      <p className="text-xl text-gray-400 max-w-3xl mx-auto">{block.data.subtitle?.[language]}</p>
                      {block.data.image && (
                        <div className="mt-8 rounded-2xl overflow-hidden border border-white/10">
                           <img src={`http://127.0.0.1:8000/storage/${block.data.image}`} alt="Hero" className="w-full h-auto" />
                        </div>
                      )}
                    </div>
                  );
                case "text_block":
                  return (
                    <div key={index} className="mb-16 bg-white/5 p-8 rounded-2xl border border-white/10">
                      <h2 className="text-3xl font-bold mb-6 text-[#FF8A00]">{block.data.heading?.[language]}</h2>
                      <div className="prose prose-invert max-w-none" dangerouslySetInnerHTML={{ __html: block.data.content?.[language] }}></div>
                    </div>
                  );
                case "features_grid":
                  return (
                    <div key={index} className="mb-16">
                       <h2 className="text-3xl font-bold mb-10 text-center">{block.data.section_title?.[language]}</h2>
                       <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                          {block.data.items?.map((item, i) => (
                             <div key={i} className="bg-black/40 backdrop-blur-md p-6 rounded-2xl border border-white/10 hover:border-[#FF8A00]/50 transition-colors">
                                <h3 className="text-xl font-bold mb-2 text-white">{item.title?.[language]}</h3>
                                <p className="text-gray-400">{item.desc?.[language]}</p>
                             </div>
                          ))}
                       </div>
                    </div>
                  );
                case "cta_block":
                  return (
                    <div key={index} className="mb-16 bg-gradient-to-r from-[#FF8A00]/20 to-[#FF8A00]/5 border border-[#FF8A00]/20 p-10 rounded-3xl text-center relative overflow-hidden">
                      <div className="absolute top-0 right-0 w-64 h-64 bg-[#FF8A00]/10 blur-[80px] rounded-full"></div>
                      <div className="relative z-10">
                         <h2 className="text-3xl md:text-5xl font-bold mb-4 text-white">{block.data.title?.[language]}</h2>
                         <p className="text-lg text-gray-300 mb-8 max-w-2xl mx-auto">{block.data.subtitle?.[language]}</p>
                         {block.data.button_text?.[language] && (
                            <a href={block.data.button_link || "#"} className="inline-block bg-[#FF8A00] text-white px-8 py-4 rounded-full font-bold hover:scale-105 transition-transform shadow-[0_0_20px_rgba(255,138,0,0.3)]">
                               {block.data.button_text?.[language]}
                            </a>
                         )}
                      </div>
                    </div>
                  );
                case "faq_block":
                  return (
                    <div key={index} className="mb-16 max-w-4xl mx-auto">
                       <h2 className="text-3xl font-bold mb-8 text-center text-white">{block.data.section_title?.[language]}</h2>
                       <div className="space-y-4">
                          {block.data.faqs?.map((faq, i) => (
                             <div key={i} className="bg-white/5 border border-white/10 rounded-xl overflow-hidden">
                                <div className="p-5 font-bold text-lg text-white border-b border-white/10 bg-white/5">
                                   {faq.question?.[language]}
                                </div>
                                <div className="p-5 text-gray-300 prose prose-invert max-w-none" dangerouslySetInnerHTML={{ __html: faq.answer?.[language] }}></div>
                             </div>
                          ))}
                       </div>
                    </div>
                  );
                case "split_block":
                  const isRight = block.data.image_on_right;
                  return (
                    <div key={index} className={`mb-16 flex flex-col ${isRight ? 'md:flex-row' : 'md:flex-row-reverse'} items-center gap-10`}>
                       <div className="flex-1">
                          <h2 className="text-3xl md:text-4xl font-bold mb-6 text-[#FF8A00]">{block.data.title?.[language]}</h2>
                          <div className="prose prose-invert max-w-none text-gray-300 text-lg leading-relaxed" dangerouslySetInnerHTML={{ __html: block.data.content?.[language] }}></div>
                       </div>
                       <div className="flex-1 w-full">
                          {block.data.image && (
                             <div className="rounded-2xl overflow-hidden shadow-[0_0_30px_rgba(255,138,0,0.1)] border border-white/10">
                                <img src={`http://127.0.0.1:8000/storage/${block.data.image}`} alt="Split Content" className="w-full h-auto object-cover" />
                             </div>
                          )}
                       </div>
                    </div>
                  );
                case "stats_block":
                  return (
                    <div key={index} className="mb-16 grid grid-cols-2 md:grid-cols-4 gap-6">
                       {block.data.stats?.map((stat, i) => (
                          <div key={i} className="text-center p-8 bg-black/40 backdrop-blur-md rounded-2xl border border-white/10">
                             <h3 className="text-4xl md:text-5xl font-bold text-[#FF8A00] mb-2">{stat.number}</h3>
                             <p className="text-gray-300 font-medium text-lg">{stat.label?.[language]}</p>
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

      <Footer />
    </main>
  );
}
