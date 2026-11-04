import { Montserrat, Poppins, Alexandria } from "next/font/google";
import localFont from "next/font/local";
import "./globals.css";
import Navbar from "./components/Navbar/Navbar";
import Footer from "./components/Footer/Footer";
import HomeBackground from "./components/Home/HomeBackground";

const tsDamasSans = localFont({
  src: "../../public/fonts/TsDamasSansFreeBold.woff2",
  variable: "--font-ts-damas",
  display: "swap",
});

const montserrat = Montserrat({
  variable: "--font-montserrat",
  subsets: ["latin"],
  weight: ["400", "500", "700"],
  display: "swap",
});

const poppins = Poppins({
  variable: "--font-poppins",
  subsets: ["latin"],
  weight: ["400", "500", "700"],
  display: "swap",
});

const alexandria = Alexandria({
  variable: "--font-alexandria",
  subsets: ["latin", "arabic"],
  weight: ["400", "500", "700"],
  display: "swap",
});

import { headers, cookies } from "next/headers";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import {
  getCachedHomeData,
  getCachedHeadSettings,
  getCachedSettings,
  getCachedServicesList,
  getCachedNavLinks,
} from "@/lib/cached-api";
import { renderCustomHeadCode } from "@/lib/custom-head";
import { sanitizeHtml } from "@/lib/sanitize-html";
import { safeJsonForScript } from "@/lib/safe-json";
import { LanguageProvider } from "./context/LanguageContext";
import { SettingsProvider } from "./context/SettingsContext";
import Loading from "./loading";
import { Suspense } from "react";
import { AuthProvider } from "./context/AuthContext";
import { NonceProvider } from "./context/NonceContext";

export async function generateMetadata() {
  // IMPORTANT: Do NOT call headers(), cookies(), or getSeoAlternates() here.
  // This is the ROOT layout generateMetadata — it runs OUTSIDE any Suspense boundary.
  // Any dynamic API call here (headers, cookies, getSeoAlternates) will pollute the
  // PPR static shell and cause React error #419 (hydration mismatch) in production.
  //
  // Rule: only "use cache" functions with static inputs are allowed here.
  // Per-route alternates (hreflang) are set in each page's own generateMetadata.
  const language = "en";

  let settings = null;
  let homeData = null;
  try {
    [settings, homeData] = await Promise.all([
      getCachedSettings(language),
      getCachedHomeData(language),
    ]);
  } catch (e) {
    // API unavailable — use static fallbacks below
  }

  const faviconUrl = settings?.site_favicon
    ? getMediaUrl(settings.site_favicon)
    : "/logo.svg";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";

  const siteTitle = homeData?.seo?.title || settings?.site_title || "Serv5";
  const siteDesc =
    homeData?.seo?.description || settings?.site_description ||
    "Leading company in web development and AI solutions.";
  const siteLogo = settings?.site_logo ? getMediaUrl(settings.site_logo) : "";
  const ogImageUrl = settings?.social_share_image
    ? getMediaUrl(settings.social_share_image)
    : siteLogo;
  return {
    metadataBase: new URL(siteUrl),
    title: {
      template: `%s | ${siteTitle}`,
      default: siteTitle,
    },
    description: siteDesc,
    icons: {
      icon: faviconUrl,
      shortcut: faviconUrl,
      apple: faviconUrl,
    },
    openGraph: {
      title: siteTitle,
      description: siteDesc,
      url: siteUrl,
      siteName: siteTitle,
      images: ogImageUrl ? [{ url: ogImageUrl }] : [],
      type: "website",
    },
    twitter: {
      card: "summary_large_image",
      title: siteTitle,
      description: siteDesc,
      images: ogImageUrl ? [ogImageUrl] : [],
    },
  };
}

async function ProvidersAndContent({ children }) {
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const nonce = headersList.get("x-nonce") || "";
  const cookieStore = await cookies();
  const defaultLanguage = xLocale || cookieStore.get("NEXT_LOCALE")?.value || "en";

  const [settings, servicesRes, navLinksRes] = await Promise.all([
    getCachedSettings(defaultLanguage),
    getCachedServicesList(defaultLanguage).catch(() => null),
    getCachedNavLinks(defaultLanguage).catch(() => null),
  ]);

  const initialServices = Array.isArray(servicesRes) ? servicesRes : (servicesRes?.data || []);
  const initialNavLinks = Array.isArray(navLinksRes) ? navLinksRes : (navLinksRes?.data || []);

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const siteTitle = settings?.site_title || "Serv5";
  const siteDesc =
    settings?.site_description ||
    "Leading company in web development and AI solutions.";
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : `${siteUrl}/logo.svg`;

  const orgSchema = {
    "@context": "https://schema.org",
    "@type": "Organization",
    name: siteTitle,
    url: siteUrl,
    logo: siteLogo,
    description: siteDesc,
  };

  const websiteSchema = {
    "@context": "https://schema.org",
    "@type": "WebSite",
    name: siteTitle,
    url: siteUrl,
    potentialAction: {
      "@type": "SearchAction",
      target: `${siteUrl}/search?q={search_term_string}`,
      "query-input": "required name=search_term_string",
    },
  };

  return (
    <NonceProvider nonce={nonce}>
      <LanguageProvider defaultLanguage={defaultLanguage} initialSettings={settings || {}}>
        <AuthProvider>
          <SettingsProvider initialSettings={settings || {}} ssrLocale={defaultLanguage}>
            <HomeBackground />
            <Navbar initialServices={initialServices} initialNavLinks={initialNavLinks} />
            {children}
            <Footer />

            {/* Organization & WebSite Schemas */}
            <script
              type="application/ld+json"
              dangerouslySetInnerHTML={{ __html: safeJsonForScript(orgSchema) }}
            />
            <script
              type="application/ld+json"
              dangerouslySetInnerHTML={{ __html: safeJsonForScript(websiteSchema) }}
            />
            {settings?.custom_footer_code && (
              <div
                dangerouslySetInnerHTML={{ __html: sanitizeHtml(settings.custom_footer_code) }}
                style={{ display: "none" }}
              />
            )}
          </SettingsProvider>
        </AuthProvider>
      </LanguageProvider>
    </NonceProvider>
  );
}

export default async function RootLayout({ children }) {
  let locale = "en";
  try {
    const reqHeaders = await headers();
    const xLocale = reqHeaders.get("x-locale");
    if (xLocale === "ar" || xLocale === "en") {
      locale = xLocale;
    }
  } catch {
    // In static prerender, fallback to default en
  }
  const dir = locale === "ar" ? "rtl" : "ltr";

  // getCachedHeadSettings uses "use cache" — safe outside Suspense.
  // Custom head code is site-wide, so "en" locale is fine here.
  const settings = await getCachedHeadSettings("en");
  const customHead = renderCustomHeadCode(settings?.custom_header_code, {
    useNextScript: true,
  });

  return (
    <html
      lang={locale}
      dir={dir}
      className={`${montserrat.variable} ${poppins.variable} ${alexandria.variable} ${tsDamasSans.variable} min-h-screen antialiased relative`}
      suppressHydrationWarning
    >
      <head>
        {/* Resource Hints: Preconnect to critical API and Font origins */}
        <link rel="preconnect" href="https://api.serv5.com.eg" crossOrigin="anonymous" />
        <link rel="dns-prefetch" href="https://api.serv5.com.eg" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />

        {/* Fix lang/dir immediately from URL before React hydrates (avoids RTL flash) */}
        <script
          suppressHydrationWarning
          dangerouslySetInnerHTML={{
            __html: `(function(){try{var p=location.pathname,ar=p.startsWith('/ar')||p==='/ar';if(ar){var h=document.documentElement;h.setAttribute('lang','ar');h.setAttribute('dir','rtl');}}catch(e){}})();`,
          }}
        />

        {/* Preload Critical Brand Font to prevent layout shift & FOUT */}
        <link rel="preload" href="/fonts/TsDamasSansFreeBold.woff2" as="font" type="font/woff2" crossOrigin="anonymous" />

        {/* Preload Largest Contentful Paint (LCP) Candidates & Critical Hero Assets */}
        <link rel="preload" href="/logo.svg" as="image" type="image/svg+xml" fetchPriority="high" />
        <link rel="preload" href="/Hero/bg.webp" as="image" type="image/webp" fetchPriority="high" />
        <link rel="preload" href="/Hero/earth.webp" as="image" type="image/webp" fetchPriority="high" />
        <link rel="preload" href="/Hero/orange-planet.webp" as="image" type="image/webp" fetchPriority="high" />
        <link rel="preload" href="/Hero/left-starts-Animate.svg" as="image" type="image/svg+xml" />
        <link rel="preload" href="/Hero/front-stars.svg" as="image" type="image/svg+xml" />
        {customHead}
      </head>
      <body className="min-h-screen flex flex-col relative" suppressHydrationWarning>
        <div id="app-shell">
          <Suspense fallback={<Loading />}>
            <ProvidersAndContent>
              {children}
            </ProvidersAndContent>
          </Suspense>
        </div>
      </body>
    </html>
  );
}
