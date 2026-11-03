import React from "react";
import { headers } from "next/headers";
import { getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import QuotationHero from "../components/Quotation/QuotationHero";
import QuotationForm from "../components/Quotation/QuotationForm";

export async function generateMetadata() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  let settings = null;
  let alternates = null;

  try {
    const [settingsRes, alts] = await Promise.all([
      getCachedSettings(lang).catch(() => null),
      getSeoAlternates().catch(() => null),
    ]);
    settings = settingsRes;
    alternates = alts;
  } catch (err) {
    // API fallback
  }

  const title =
    lang === "ar"
      ? "طلب عرض سعر مجاني | Serv5"
      : "Request a Free Quotation | Serv5";

  const description =
    lang === "ar"
      ? "اطلب عرض سعر مخصص لمشروعك البرمجي، موقعك الإلكتروني أو تطبيق الجوال من شركة سيرف فايف."
      : "Request a detailed quotation tailored for your software project, web application, or mobile app from Serv5.";

  const ogImage = settings?.social_share_image
    ? getMediaUrl(settings.social_share_image)
    : `${siteUrl}/logo.svg`;

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${lang}/request-quote`,
      siteName: "Serv5",
      type: "website",
      images: ogImage ? [{ url: ogImage }] : [],
    },
    twitter: {
      card: "summary_large_image",
      title,
      description,
      images: ogImage ? [ogImage] : [],
    },
  };
}

export default function RequestQuotePage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      {/* Background elements consistent with other pages */}
      <div className="fixed inset-0 z-0 pointer-events-none">
        <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/10 blur-[150px] rounded-full" />
        <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 blur-[150px] rounded-full" />
      </div>

      <div className="w-full relative z-10">
        <QuotationHero />
        <QuotationForm />
      </div>
    </main>
  );
}
