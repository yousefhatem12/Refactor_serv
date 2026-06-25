import { getCachedHomeData } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { headers } from "next/headers";

export async function generateMetadata() {
  const headerList = await headers();
  const locale = headerList.get("x-locale") || "en";

  const data = await getCachedHomeData(locale);
  const alternates = await getSeoAlternates();

  return {
    title: data?.seo?.title || "Serv5 | Advanced Agentic Coding",
    description:
      data?.seo?.description ||
      "Leading company in web development and AI solutions.",
    alternates,
  };
}

export default function HomeLayout({ children }) {
  return children;
}
