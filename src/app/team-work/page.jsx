import React from "react";
import { headers } from "next/headers";
import { getCachedTeamList, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { getMediaUrl } from "@/lib/api";
import TeamWorkClient from "./TeamWorkClient";

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
      ? "فريق عمل سيرف فايف | قادة الإبداع والابتكار"
      : "Our Team | Serv5 Creative & Tech Leaders";

  const description =
    lang === "ar"
      ? "تعرف على مهندسي ومصممي وخبراء شركة سيرف فايف الذين يقودون التحول الرقمي ويصنعون النجاح لعملائنا."
      : "Meet the talented developers, designers, and strategists behind Serv5 powering digital excellence.";

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
      url: `${siteUrl}/${lang}/team-work`,
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

export default async function TeamWorkPage() {
  const headersList = await headers();
  const lang = headersList.get("x-locale") || "en";

  let initialDbTeam = [];
  try {
    const res = await getCachedTeamList(lang).catch(() => null);
    const data = res && res.data ? res.data : res;
    if (data && Array.isArray(data)) {
      initialDbTeam = data;
    }
  } catch (error) {
    console.error("Failed to fetch initial team on server:", error);
  }

  return (
    <TeamWorkClient
      initialDbTeam={initialDbTeam}
      initialLang={lang}
    />
  );
}
