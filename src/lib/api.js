const API_BASE_URL =
  process.env.NEXT_PUBLIC_API_URL || "https://api.serv5.com.eg/api/v1";

export const getMediaUrl = (path) => {
  if (!path) return "";
  if (path.startsWith("http")) return path;

  const baseUrl = API_BASE_URL.replace(/\/api(\/v\d+)?$/, "");
  return `${baseUrl}/storage/${path.replace(/^\//, "")}`;
};

// In-memory cache to store GET promises and their timestamp
const apiCache = new Map();
const CACHE_TTL = 300000; // 5 minutes

export const fetchAPI = async (endpoint, options = {}) => {
  let lang = "en";
  
  // 1. Try to extract lang from options or endpoint URL query string
  let queryLang = null;
  try {
    const tempUrl = new URL(endpoint, "http://localhost");
    queryLang = tempUrl.searchParams.get("lang");
  } catch (e) {}

  if (options.lang) {
    lang = options.lang;
  } else if (queryLang) {
    lang = queryLang;
  } else if (typeof window === "undefined") {
    if (!options.skipHeaders) {
      try {
        const { cookies, headers } = await import("next/headers");
        const headersList = await headers();
        const xLocale = headersList.get("x-locale");
        if (xLocale) {
          lang = xLocale;
        } else {
          const cookieStore = await cookies();
          const nextLocale = cookieStore.get("NEXT_LOCALE");
          if (nextLocale) lang = nextLocale.value;
        }
      } catch (error) {
        if (error && (
          error.message?.includes("prerender") || 
          error.message?.includes("headers()") || 
          error.message?.includes("cookies()") || 
          error.digest?.startsWith("NEXT_") || 
          error.message?.includes("dynamicIO")
        )) {
          throw error;
        }
      }
    }
  } else {
    const match = document.cookie.match(new RegExp("(^| )NEXT_LOCALE=([^;]+)"));
    if (match) lang = match[2];
  }

  const url = new URL(`${API_BASE_URL}${endpoint}`);
  url.searchParams.set("lang", lang);

  const token =
    typeof window !== "undefined" ? localStorage.getItem("auth_token") : null;
  const method = (options.method || "GET").toUpperCase();
  const isGet = method === "GET";

  // Construct cache key based on URL and auth state
  let cacheKey = null;
  if (isGet && typeof window !== "undefined") {
    cacheKey = `${url.pathname}${url.search}:${token || ""}`;

    const cached = apiCache.get(cacheKey);
    if (cached && Date.now() - cached.timestamp < CACHE_TTL) {
      return cached.promise;
    }
  }

  const fetchPromise = (async () => {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10-second timeout

    try {
      const isFormData =
        typeof FormData !== "undefined" && options.body instanceof FormData;
      const headers = {
        Accept: "application/json",
        "Accept-Language": lang,
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...options.headers,
      };

      if (!isFormData) {
        headers["Content-Type"] = "application/json";
      }

      // console.log(`[fetchAPI] Fetching: ${url.toString()}`);
      const response = await fetch(url.toString(), {
        ...options,
        cache: options.cache || "no-store",
        headers,
        signal: controller.signal,
      });
      clearTimeout(timeoutId);
      // console.log(
      //   `[fetchAPI] Received response for: ${url.toString()} - Status: ${response.status}`,
      // );

      if (!response.ok) {
        console.warn(`API warning: ${response.statusText}`);
        if (isGet && cacheKey) apiCache.delete(cacheKey); // Don't cache bad responses
        return null;
      }

      return await response.json();
    } catch (error) {
      clearTimeout(timeoutId);
      if (error && (
        error.message?.includes("prerender") || 
        error.message?.includes("headers()") || 
        error.message?.includes("cookies()") || 
        error.digest?.startsWith("NEXT_") || 
        error.message?.includes("dynamicIO")
      )) {
        throw error;
      }
      console.warn("API connection failed:", error.name === "AbortError" ? "Timeout" : error.message);
      if (isGet && cacheKey) apiCache.delete(cacheKey); // Don't cache failures
      return null; // Return null so the UI doesn't crash
    }
  })();

  if (isGet && cacheKey && typeof window !== "undefined") {
    apiCache.set(cacheKey, {
      promise: fetchPromise,
      timestamp: Date.now(),
    });
  }

  return fetchPromise;
};