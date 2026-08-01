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
  weight: ["300", "400", "500", "600", "700", "800", "900"],
  display: "swap",
});

const poppins = Poppins({
  variable: "--font-poppins",
  subsets: ["latin"],
  weight: ["300", "400", "500", "600", "700"],
  display: "swap",
});

const alexandria = Alexandria({
  variable: "--font-alexandria",
  subsets: ["latin", "arabic"],
  weight: ["300", "400", "500", "600", "700", "800", "900"],
  display: "swap",
});

import { headers, cookies } from "next/headers";
import { fetchAPI, getMediaUrl } from "@/lib/api";
import { getSeoAlternates } from "@/lib/seo";
import { getCachedSettings } from "@/lib/cached-api";

export async function generateMetadata() {
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const cookieStore = await cookies();
  const language = xLocale || cookieStore.get("NEXT_LOCALE")?.value || "en";

  const settings = await getCachedSettings(language);
  const faviconUrl = settings?.site_favicon
    ? getMediaUrl(settings.site_favicon)
    : "/logo.ico";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const alternates = await getSeoAlternates();

  const siteTitle = settings?.site_title || "Serv5";
  const siteDesc =
    settings?.site_description ||
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
    alternates,
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

import { LanguageProvider } from "./context/LanguageContext";
import { SettingsProvider } from "./context/SettingsContext";
import Loading from "./loading";
import { Suspense } from "react";
import { AuthProvider } from "./context/AuthContext";

async function ProvidersAndContent({ children }) {
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const cookieStore = await cookies();
  const defaultLanguage = xLocale || cookieStore.get("NEXT_LOCALE")?.value || "en";

  const settings = await getCachedSettings(defaultLanguage);

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
    <LanguageProvider defaultLanguage={defaultLanguage}>
      <AuthProvider>
        <SettingsProvider initialSettings={settings || {}} ssrLocale={defaultLanguage}>
          {settings?.custom_header_code && (
            <div
              dangerouslySetInnerHTML={{ __html: settings.custom_header_code }}
              style={{ display: "none" }}
            />
          )}
          <HomeBackground />
          <Navbar />
          {children}
          <Footer />

          {/* Organization & WebSite Schemas */}
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{ __html: JSON.stringify(orgSchema) }}
          />
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{ __html: JSON.stringify(websiteSchema) }}
          />
          {settings?.custom_footer_code && (
            <div
              dangerouslySetInnerHTML={{ __html: settings.custom_footer_code }}
              style={{ display: "none" }}
            />
          )}
        </SettingsProvider>
      </AuthProvider>
    </LanguageProvider>
  );
}

export default async function RootLayout({ children }) {
  return (
    <html
      lang="en"
      className={`${montserrat.variable} ${poppins.variable} ${alexandria.variable} ${tsDamasSans.variable} min-h-screen antialiased relative`}
      suppressHydrationWarning
    >
      <head>
        <link rel="preload" href="/Hero/earth.webp" as="image" type="image/webp" fetchPriority="high" />
        <link rel="preload" href="/Hero/orange-planet.webp" as="image" type="image/webp" fetchPriority="high" />
        <link rel="preload" href="/logo.svg" as="image" type="image/svg+xml" fetchPriority="high" />
      </head>
      <body className="min-h-screen flex flex-col relative" suppressHydrationWarning>
        <script
          dangerouslySetInnerHTML={{
            __html: `
              (function() {
                try {
                  const orgSet = Element.prototype.setAttribute;
                  Element.prototype.setAttribute = function(name, value) {
                    if (name === 'bis_skin_checked') return;
                    orgSet.apply(this, arguments);
                  };
                  const orgSetNS = Element.prototype.setAttributeNS;
                  Element.prototype.setAttributeNS = function(ns, name, value) {
                    if (name === 'bis_skin_checked') return;
                    orgSetNS.apply(this, arguments);
                  };
                } catch (e) {}
              })();
            `,
          }}
        />
        <Suspense fallback={<Loading />}>
          <ProvidersAndContent>
            {children}
          </ProvidersAndContent>
        </Suspense>
      </body>
    </html>
  );
}

