import { fetchAPI } from "./api";
import { cacheLife } from "next/cache";

export async function getCachedSettings(locale = "en") {
  "use cache";
  cacheLife("seconds");
  return fetchAPI(`/settings?lang=${locale}`, { skipHeaders: true });
}

export async function getCachedHeadSettings(locale = "en") {
  "use cache";
  // Short-lived caches are excluded from prerendering in Next 16. Keeping the
  // head cache for minutes lets custom tags be emitted in the initial <head>.
  cacheLife("minutes");
  return fetchAPI(`/settings?lang=${locale}`, { skipHeaders: true });
}

export async function getCachedHomeData(locale) {
  "use cache";
  cacheLife("seconds");
  return fetchAPI(`/pages/home?lang=${locale}`, { skipHeaders: true });
}


