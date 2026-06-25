import { fetchAPI } from "./api";
import { cacheLife } from "next/cache";

export async function getCachedSettings() {
  "use cache";
  cacheLife("days");
  return fetchAPI("/settings", { lang: "en", skipHeaders: true });
}

export async function getCachedHomeData(locale) {
  "use cache";
  cacheLife("minutes");
  return fetchAPI(`/pages/home?lang=${locale}`, { skipHeaders: true });
}
