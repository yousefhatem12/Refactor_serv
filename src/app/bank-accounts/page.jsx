import React from "react";
import { headers } from "next/headers";
import { getCachedSettings, getCachedBankAccounts } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import BankAccountsContent from "../components/BankAccounts/BankAccountsContent";

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
      ? "الحسابات البنكية وطرق الدفع | Serv5"
      : "Bank Accounts & Payment Methods | Serv5";

  const description =
    lang === "ar"
      ? "تعرف على الحسابات البنكية الرسمية وطرق السداد المعتمدة لدى شركة سيرف فايف."
      : "Official bank accounts and payment methods for Serv5 services and contracts.";

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
      url: `${siteUrl}/${lang}/bank-accounts`,
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

export default async function BankAccountsPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialAccounts = null;
  try {
    const res = await getCachedBankAccounts(lang, "egypt").catch(() => null);
    initialAccounts = res?.data || res || null;
  } catch (error) {
    console.error("Failed to fetch initial bank accounts on server:", error);
  }

  return <BankAccountsContent initialData={initialAccounts} initialLang={lang} />;
}
