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

export const getCachedPortfolioItem = unstable_cache(
  async (id, locale = "en") =>
    fetchAPI(`/portfolio/${id}?lang=${locale}`, { skipHeaders: true }),
  ["portfolio-item"],
  { revalidate: REVALIDATE }
);

export const getCachedProductItem = unstable_cache(
  async (id, locale = "en") =>
    fetchAPI(`/products/${id}?lang=${locale}`, { skipHeaders: true }),
  ["product-item"],
  { revalidate: REVALIDATE }
);

export const getCachedServicesList = unstable_cache(
  async (locale = "en") =>
    fetchAPI(`/services?lang=${locale}`, { skipHeaders: true }),
  ["services-list"],
  { revalidate: REVALIDATE }
);

export const getCachedNavLinks = unstable_cache(
  async (locale = "en") =>
    fetchAPI(`/nav-links?lang=${locale}`, { skipHeaders: true }),
  ["nav-links"],
  { revalidate: REVALIDATE }
);

export const getCachedBlogPosts = unstable_cache(
  async (locale = "en", limit = 4) =>
    fetchAPI(`/blog?lang=${locale}&limit=${limit}`, { skipHeaders: true }),
  ["blog-posts-list"],
  { revalidate: REVALIDATE }
);

export const getCachedProductsList = unstable_cache(
  async (locale = "en", page = 1, perPage = 9) =>
    fetchAPI(`/products?lang=${locale}&page=${page}&per_page=${perPage}`, { skipHeaders: true }),
  ["products-list"],
  { revalidate: REVALIDATE }
);

export const getCachedCareersList = unstable_cache(
  async (locale = "en") =>
    fetchAPI(`/careers?lang=${locale}&page=1&per_page=9`, { skipHeaders: true }),
  ["careers-list"],
  { revalidate: REVALIDATE }
);

export const getCachedTeamList = unstable_cache(
  async (locale = "en") =>
    fetchAPI(`/team?lang=${locale}`, { skipHeaders: true }),
  ["team-list"],
  { revalidate: REVALIDATE }
);

export const getCachedBankAccounts = unstable_cache(
  async (locale = "en", country = "egypt") =>
    fetchAPI(`/bank-accounts?lang=${locale}&country=${country}&page=1&per_page=6`, { skipHeaders: true }),
  ["bank-accounts-list"],
  { revalidate: REVALIDATE }
);




