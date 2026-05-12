import { fetchAPI } from "@/lib/api";

export async function generateMetadata() {
  // We don't have easy access to the locale here without cookies or middleware
  // but we can try to fetch the default or use the headers
  const data = await fetchAPI("/pages/home");
  
  return {
    title: data?.seo?.title || "Serv5 | Advanced Agentic Coding",
    description: data?.seo?.description || "Leading company in web development and AI solutions.",
  };
}

export default function HomeLayout({ children }) {
  return children;
}
