import { fetchAPI } from "./api";
import { unstable_cache } from "next/cache";

// Revalidate every 60 seconds (equivalent to cacheLife("minutes"))
const REVALIDATE = 60;

export const getCachedSettings = unstable_cache(
  async (locale = "en") =>
    fetchAPI(`/settings?lang=${locale}`, { skipHeaders: true }),
  ["settings"],
  { revalidate: REVALIDATE }
);

export const getCachedHeadSettings = unstable_cache(
  async (locale = "en") =>
    fetchAPI(`/settings?lang=${locale}`, { skipHeaders: true }),
  ["head-settings"],
  { revalidate: REVALIDATE }
);

export const getCachedHomeData = unstable_cache(
  async (locale = "en") =>
    fetchAPI(`/pages/home?lang=${locale}`, { skipHeaders: true }),
  ["home-data"],
  { revalidate: REVALIDATE }
);

export const getCachedPage = unstable_cache(
  async (slug, locale = "en") =>
    fetchAPI(`/pages/${slug}?lang=${locale}`, { skipHeaders: true }),
  ["page"],
  { revalidate: REVALIDATE }
);

export const getCachedService = unstable_cache(
  async (slug, locale = "en") =>
    fetchAPI(`/services/${slug}?lang=${locale}`, { skipHeaders: true }),
  ["service"],
  { revalidate: REVALIDATE }
);

export const getCachedBlogPost = unstable_cache(
  async (id, locale = "en") =>
    fetchAPI(`/blog/${id}?lang=${locale}`, { skipHeaders: true }),
  ["blog-post"],
  { revalidate: REVALIDATE }
);
