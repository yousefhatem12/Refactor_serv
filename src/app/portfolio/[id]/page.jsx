import React from "react";
import { headers } from "next/headers";
import { notFound } from "next/navigation";
import { getCachedPortfolioItem } from "@/lib/cached-api";
import PortfolioDetailClient from "./PortfolioDetailClient";

// Note: Metadata (canonical, hreflang, openGraph, twitter) is handled
// by src/app/portfolio/[id]/layout.jsx to avoid duplication.

export default async function PortfolioDetailPage({ params }) {
  const { id } = await params;
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let project = null;
  try {
    const res = await getCachedPortfolioItem(id, lang).catch(() => null);
    project = res?.data || res || null;
  } catch (err) {
    console.error("Failed to fetch portfolio item on server:", err);
  }

  if (!project || !project.name) {
    notFound();
  }

  return (
    <PortfolioDetailClient
      initialProject={project}
      id={id}
      initialLang={lang}
    />
  );
}
