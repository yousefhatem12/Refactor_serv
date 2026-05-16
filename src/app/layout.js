import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import Navbar from "./components/Navbar/Navbar";
import Footer from "./components/Footer/Footer";
import HomeBackground from "./components/Home/HomeBackground";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

import { fetchAPI } from "@/lib/api";

export async function generateMetadata() {
  const settings = await fetchAPI('/settings');
  return {
    title: settings?.site_title || "Serv5 | Advanced Agentic Coding",
    description: settings?.site_description || "Leading company in web development and AI solutions.",
  };
}

import { LanguageProvider } from "./context/LanguageContext";
import { AuthProvider } from "./context/AuthContext";
import { SettingsProvider } from "./context/SettingsContext";

export default async function RootLayout({ children }) {
  const settings = await fetchAPI('/settings');
  return (
    <html
      lang="en"
      className={`${geistSans.variable} ${geistMono.variable} h-full antialiased`}
      suppressHydrationWarning
    >
      <body className="min-h-full flex flex-col" suppressHydrationWarning>
        {settings?.custom_header_code && (
          <div dangerouslySetInnerHTML={{ __html: settings.custom_header_code }} style={{ display: 'none' }} />
        )}
        <LanguageProvider>
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
          <div dangerouslySetInnerHTML={{ __html: settings.custom_footer_code }} style={{ display: 'none' }} />
        )}
      </body>
    </html>
  );
}
