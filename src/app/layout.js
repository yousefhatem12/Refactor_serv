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

import { fetchAPI } from "@/lib/api";

export async function generateMetadata() {
  const settings = await fetchAPI("/settings");
  return {
    title: settings?.site_title || "Serv5 | Advanced Agentic Coding",
    description:
      settings?.site_description ||
      "Leading company in web development and AI solutions.",
  };
}

import { cookies } from "next/headers";
import { LanguageProvider } from "./context/LanguageContext";
import { AuthProvider } from "./context/AuthContext";
import { SettingsProvider } from "./context/SettingsContext";

export default async function RootLayout({ children }) {
  const cookieStore = await cookies();
  const defaultLanguage = cookieStore.get("NEXT_LOCALE")?.value || "en";
  const settings = await fetchAPI("/settings");
  return (
    <html
      lang={defaultLanguage}
      className={`${montserrat.variable} ${poppins.variable} ${alexandria.variable} min-h-screen antialiased`}
      suppressHydrationWarning
    >
      <body className="min-h-screen flex flex-col" suppressHydrationWarning>
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
