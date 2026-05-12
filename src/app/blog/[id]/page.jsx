"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import { useParams } from "next/navigation";
import { fetchAPI } from "@/lib/api";
import Loader from "../../components/Loader/Loader";

export default function BlogPostDetails() {
  const params = useParams();
  const slug = params?.id; // The folder is still named [id] but it receives the slug from URL
  const [post, setPost] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    if (slug) {
      fetchAPI(`/blog/${slug}`).then(data => {
        setPost(data);
        setIsLoading(false);
      }).catch(err => {
        console.error(err);
        setIsLoading(false);
      });
    }
  }, [slug]);

  if (isLoading) {
    return (
      <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black">
        <Loader />
      </div>
    );
  }

  if (!post) {
    return (
      <div className="min-h-screen flex items-center justify-center text-white bg-[#040A37]">
        <h1>Post not found</h1>
      </div>
    );
  }

  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden pt-10 pb-10">
      <div className="fixed inset-0 bg-[#040A37] -z-20 pointer-events-none" />

      <div className="w-full relative z-10 flex flex-col items-center pt-24 text-white">
        <section className="max-w-7xl mx-auto px-6 lg:px-8 mb-32 relative z-10">
          <div className="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
            <div className="w-full lg:w-1/2">
              <span className="text-[#FF8A00] font-bold mb-4 block uppercase tracking-widest">
                {post.category}
              </span>
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight uppercase">
                {post.title}
              </h1>
              <div 
                className="text-gray-300 text-lg leading-relaxed mb-10 prose prose-invert max-w-none"
                dangerouslySetInnerHTML={{ __html: post.content }}
              />
              <div className="flex items-center gap-4 text-sm text-gray-400">
                <span>By {post.author}</span>
                <span>•</span>
                <span>{post.published_at}</span>
              </div>
            </div>

            <div className="w-full lg:w-1/2 relative h-[300px] sm:h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-2xl border border-white/10 group bg-gray-800">
              {post.image && (
                <Image
                  src={post.image}
                  alt={post.title}
                  fill
                  className="object-cover object-center group-hover:scale-105 transition-transform duration-700"
                />
              )}
            </div>
          </div>
        </section>
      </div>
    </main>
  );
}
