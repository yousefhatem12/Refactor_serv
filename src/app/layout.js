import { Montserrat, Poppins, Alexandria } from "next/font/google";
import "./globals.css";
import Navbar from "./components/Navbar/Navbar";
import Footer from "./components/Footer/Footer";
import HomeBackground from "./components/Home/HomeBackground";

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

import { fetchAPI, getMediaUrl } from "@/lib/api";
import { getSeoAlternates } from "@/lib/seo";

export const dynamic = "force-dynamic";

export async function generateMetadata() {
  const settings = await fetchAPI("/settings");
  const faviconUrl = settings?.site_favicon
    ? getMediaUrl(settings.site_favicon)
    : "/logo.ico";
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const alternates = await getSeoAlternates();

  const siteTitle = settings?.site_title || "Serv5 | Advanced Agentic Coding";
  const siteDesc =
    settings?.site_description ||
    "Leading company in web development and AI solutions.";
  const siteLogo = settings?.site_logo ? getMediaUrl(settings.site_logo) : "";
  const ogImageUrl = settings?.social_share_image
    ? getMediaUrl(settings.social_share_image)
    : siteLogo;
  
  return {
    metadataBase: new URL(siteUrl),
    title: siteTitle,
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
      siteName: "Serv5",
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

import { cookies, headers } from "next/headers";
import { LanguageProvider } from "./context/LanguageContext";
import { AuthProvider } from "./context/AuthContext";
import { SettingsProvider } from "./context/SettingsContext";

export default async function RootLayout({ children }) {
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const cookieStore = await cookies();
  const defaultLanguage = xLocale || cookieStore.get("NEXT_LOCALE")?.value || "en";
  const settings = await fetchAPI("/settings");

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const siteTitle = settings?.site_title || "Serv5";
  const siteDesc =
    settings?.site_description ||
    "Leading company in web development and AI solutions.";
  const siteLogo = settings?.site_logo
    ? getMediaUrl(settings.site_logo)
    : `${siteUrl}/logo.png`;

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
    <html
      lang={defaultLanguage}
      dir={defaultLanguage === "ar" ? "rtl" : "ltr"}
      className={`${montserrat.variable} ${poppins.variable} ${alexandria.variable} min-h-screen antialiased relative`}
      suppressHydrationWarning
    >
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
        {settings?.custom_header_code && (
          <div
            dangerouslySetInnerHTML={{ __html: settings.custom_header_code }}
            style={{ display: "none" }}
          />
        )}
        <LanguageProvider defaultLanguage={defaultLanguage}>
          <AuthProvider>
            <SettingsProvider initialSettings={settings || {}}>
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
            </SettingsProvider>
          </AuthProvider>
        </LanguageProvider>
        {settings?.custom_footer_code && (
          <div
            dangerouslySetInnerHTML={{ __html: settings.custom_footer_code }}
            style={{ display: "none" }}
          />
        )}
      </body>
    </html>
  );
}
