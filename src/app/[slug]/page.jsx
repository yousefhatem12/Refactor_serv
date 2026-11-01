import React, { Suspense } from "react";
import { notFound } from "next/navigation";
import { headers } from "next/headers";
import { fetchAPI } from "../../lib/api";
import { getCachedPage, getCachedService } from "../../lib/cached-api";
import DynamicPageClient from "./DynamicPageClient";
import Loading from "../loading";

// Note: Metadata (including getSeoAlternates(), OpenGraph, and Twitter)
// is handled by src/app/[slug]/layout.jsx to avoid overriding SEO alternates.

// Separate async component for data fetching to utilize Suspense in PPR
async function DynamicPageContent({ slug }) {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  
  let pageData = null;
  let serviceData = null;
  let isServicePage = false;
  let posts = [];
  
  try {
    if (slug.startsWith("services-")) {
      isServicePage = true;
      const serviceSlug = slug.replace("services-", "");
      const res = await getCachedService(serviceSlug, lang);
      
      if (!res || res.message === "Service not found") {
        notFound();
      }
      serviceData = res.data || res;
    } else {
      const res = await getCachedPage(slug, lang);
      
      if (!res || res.message === "Page not found") {
        notFound();
      }
      pageData = res.data || res;
      
      if (pageData.sections?.template === "blog") {
        const blogData = await fetchAPI(`/blog?lang=${lang}&limit=4`);
        posts = blogData?.data || blogData || [];
      }
    }
  } catch (error) {
    if (error.message && error.message.includes("NEXT_NOT_FOUND")) {
      throw error; // Let next.js handle notFound()
    }
    console.error("Error loading dynamic page:", error);
    notFound();
  }
  
  return (
    <DynamicPageClient 
      slug={slug}
      initialPageData={pageData}
      initialServiceData={serviceData}
      isServicePage={isServicePage}
      initialPosts={posts}
    />
  );
}

export default async function DynamicPage({ params }) {
  const { slug } = await params;
  
  return (
    <Suspense fallback={<Loading />}>
      <DynamicPageContent slug={slug} />
    </Suspense>
  );
}
