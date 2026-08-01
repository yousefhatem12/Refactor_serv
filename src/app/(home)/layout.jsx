import { getCachedHomeData, getCachedSettings } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { headers } from "next/headers";

export async function generateMetadata() {
  const headerList = await headers();
  const locale = headerList.get("x-locale") || "en";

  const data = await getCachedHomeData(locale);
  const settings = await getCachedSettings(locale);
  const alternates = await getSeoAlternates();

  return {
    title: {
      absolute: settings?.site_title || data?.seo?.title || "Serv5",
    },
    description:
      data?.seo?.description ||
      "Leading company in web development and AI solutions.",
    alternates,
  };
}

export default function HomeLayout({ children }) {
  return children;
}
