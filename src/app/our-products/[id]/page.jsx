import React from "react";
import { headers } from "next/headers";
import { notFound } from "next/navigation";
import { getCachedProductItem, getCachedProductsList } from "@/lib/cached-api";
import ProductDetailClient from "./ProductDetailClient";

// Note: Metadata (canonical, hreflang, openGraph, twitter) is handled
// by src/app/our-products/[id]/layout.jsx to avoid duplication.

export default async function ProductDetailsPage({ params }) {
  const { id } = await params;
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let productData = null;
  let otherProducts = [];

  try {
    const [productRes, othersRes] = await Promise.all([
      getCachedProductItem(id, lang).catch(() => null),
      getCachedProductsList(lang, 1, 6).catch(() => null),
    ]);

    productData = productRes?.data ? productRes.data : productRes;
    const othersList = Array.isArray(othersRes) ? othersRes : othersRes?.data || [];
    otherProducts = othersList.filter((p) => p.id !== id).slice(0, 6);
  } catch (err) {
    console.error("Failed to fetch product details on server:", err);
  }

  if (!productData) {
    notFound();
  }

  return (
    <ProductDetailClient
      initialProductData={productData}
      initialOtherProducts={otherProducts}
      productId={id}
      initialLang={lang}
    />
  );
}
