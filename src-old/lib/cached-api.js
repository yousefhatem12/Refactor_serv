import { fetchAPI } from "./api";
import { cacheLife } from "next/cache";

export async function getCachedSettings(locale = "en") {
  "use cache";
  cacheLife("seconds");
  return fetchAPI(`/settings?lang=${locale}`, { skipHeaders: true });
}

export async function getCachedHomeData(locale) {
  "use cache";
  cacheLife("seconds");
  return fetchAPI(`/pages/home?lang=${locale}`, { skipHeaders: true });
}


