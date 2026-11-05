import { fetchAPI } from "./api";

/**
 * Server-side data access layer (Clean Architecture / Single Source of Truth).
 *
 * Caching and instant invalidation on Admin Dashboard writes are handled by
 * the backend's versioned ApiCacheServiceInterface + ClearsApiCache trait,
 * ensuring 0-second staleness whenever an admin creates, edits, or deletes
 * content in Filament while keeping responses cached in Laravel between writes.
 */
export const getCachedSettings = async (locale = "en") =>
  fetchAPI(`/settings?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedHeadSettings = async (locale = "en") =>
  fetchAPI(`/settings?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedHomeData = async (locale = "en") =>
  fetchAPI(`/pages/home?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedPage = async (slug, locale = "en") =>
  fetchAPI(`/pages/${slug}?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedService = async (slug, locale = "en") =>
  fetchAPI(`/services/${slug}?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedBlogPost = async (id, locale = "en") =>
  fetchAPI(`/blog/${id}?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedPortfolioItem = async (id, locale = "en") =>
  fetchAPI(`/portfolio/${id}?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedProductItem = async (id, locale = "en") =>
  fetchAPI(`/products/${id}?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedServicesList = async (locale = "en") =>
  fetchAPI(`/services?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedNavLinks = async (locale = "en") =>
  fetchAPI(`/nav-links?lang=${locale}`, { skipHeaders: true, cache: "no-store" });

export const getCachedBlogPosts = async (locale = "en", limit = 4) =>
  fetchAPI(`/blog?lang=${locale}&limit=${limit}`, { skipHeaders: true, cache: "no-store" });

export const getCachedProductsList = async (locale = "en", page = 1, perPage = 9) =>
  fetchAPI(`/products?lang=${locale}&page=${page}&per_page=${perPage}`, { skipHeaders: true, cache: "no-store" });

export const getCachedCareersList = async (locale = "en") =>
  fetchAPI(`/careers?lang=${locale}&page=1&per_page=9`, { skipHeaders: true, cache: "no-store" });

export const getCachedTeamList = async (locale = "en") =>
  fetchAPI(`/team?lang=${locale}`, { skipHeaders: true, cache: "no-store" });
