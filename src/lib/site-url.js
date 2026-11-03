const DEFAULT_SITE_URL = "https://serv5.com.eg";

export function getSiteUrl() {
  const configuredUrl =
    process.env.SITE_URL ||
    process.env.NEXT_PUBLIC_SITE_URL ||
    DEFAULT_SITE_URL;

  return configuredUrl.trim().replace(/\/+$/, "");
}
