import { getCachedBlogPost } from "@/lib/cached-api";
import { getSeoAlternates } from "@/lib/seo";
import { headers, cookies } from "next/headers";

export async function generateMetadata({ params }) {
  const { id } = await params;
  const headersList = await headers();
  const xLocale = headersList.get("x-locale");
  const cookieStore = await cookies();
  const language = xLocale || cookieStore?.get("NEXT_LOCALE")?.value || "en";
  
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const alternates = await getSeoAlternates();
  
  let title = "Blog | Serv5";
  let description = "Read our latest articles.";
  let ogImage = "";

  try {
    const post = await getCachedBlogPost(id, language);
    if (post && post.title) {
      title = post.title;
      // Strip HTML tags and limit length to ~160 chars
      description = post.description || post.content?.substring(0, 160).replace(/<[^>]*>/g, "") || "";
      ogImage = post.image || "";
    }
  } catch (error) {
    console.error("Failed to fetch blog post metadata:", error);
  }

  return {
    title,
    description,
    alternates,
    openGraph: {
      title,
      description,
      url: `${siteUrl}/${language}/blog/${id}`,
      siteName: "Serv5",
      type: "article",
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

export default function BlogPostLayout({ children }) {
  return children;
}
