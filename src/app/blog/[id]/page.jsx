import React from "react";
import { headers } from "next/headers";
import { notFound } from "next/navigation";
import { getCachedBlogPost } from "@/lib/cached-api";
import BlogPostClient from "./BlogPostClient";

// Note: Metadata (canonical, hreflang, openGraph, twitter) is handled
// by src/app/blog/[id]/layout.jsx to avoid duplication.

export default async function BlogPostPage({ params }) {
  const { id } = await params;
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let post = null;
  try {
    const res = await getCachedBlogPost(id, lang).catch(() => null);
    post = res?.data || res || null;
  } catch (err) {
    console.error("Failed to fetch blog post on server:", err);
  }

  if (!post || !post.title) {
    notFound();
  }

  return (
    <BlogPostClient
      initialPost={post}
      slug={id}
      initialLang={lang}
    />
  );
}
