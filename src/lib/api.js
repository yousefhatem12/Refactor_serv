const API_BASE_URL =
  process.env.INTERNAL_API_URL ||
  process.env.NEXT_PUBLIC_API_URL ||
  "http://127.0.0.1:8000/api/v1";

const LEGACY_IMAGE_MAP = {
  "/Services/Purple Planet.webp": "/Services/planet4.webp",
  "/Services/Blue Planet.webp": "/Services/blue.webp",
  "/Services/new/yellow.webp": "/Services/yellow.webp",
  "/Services/new/green.webp": "/Services/green.webp",
  "Services/Purple Planet.webp": "/Services/planet4.webp",
  "Services/Blue Planet.webp": "/Services/blue.webp",
  "Services/new/yellow.webp": "/Services/yellow.webp",
  "Services/new/green.webp": "/Services/green.webp",
};

export const getMediaUrl = (path) => {
  if (!path) return "";
  if (path.startsWith("http://") || path.startsWith("https://")) return path;

  if (LEGACY_IMAGE_MAP[path]) {
    return LEGACY_IMAGE_MAP[path];
  }

  // Static assets located in the Next.js public/ directory (starts with / and not /storage/)
  if (path.startsWith("/") && !path.startsWith("/storage/")) {
    return path;
  }

  // Mappings from database seed records to public static assets
  if (path.startsWith("projects/")) {
    return "/" + path.replace(/^projects\//, "portfolio/");
  }
  if (path.startsWith("posts/")) {
    return "/" + path.replace(/^posts\//, "our-blog/");
  }

  // Placeholder service heroes from old seeders fallback to empty so defaultHeroImage is used
  if (path.startsWith("services/heroes/")) {
    return "";
  }

  const baseUrl = API_BASE_URL.replace(/\/api(\/v\d+)?$/, "");
  const normalizedPath = path
    .replace(/^\//, "")
    .replace(/^storage\//, "");
  return `${baseUrl}/storage/${normalizedPath}`;
};

// In-memory cache to store GET promises and their timestamp.
// WARNING: This cache is shared across ALL requests in Next.js Server-Side Rendering (SSR).
// It is perfectly safe for public data (services, portfolio, etc.), but MUST NOT be used 
// for personal/private endpoints. Private endpoints must bypass this cache.
const apiCache = new Map();
const CACHE_TTL = 2000; // 2-second in-flight deduplication window; backend ApiCacheService handles versioned caching

// Browser request concurrency queue to prevent overwhelming development/production servers
// and to prioritize visible content requests so they finish significantly faster.
let activeBrowserRequests = 0;
const MAX_CONCURRENT_BROWSER_REQUESTS = 3;
const browserRequestQueue = [];

function processNextInQueue() {
  if (activeBrowserRequests >= MAX_CONCURRENT_BROWSER_REQUESTS) return;
  if (browserRequestQueue.length === 0) return;

  const next = browserRequestQueue.shift();
  activeBrowserRequests++;
  next.execute().finally(() => {
    activeBrowserRequests--;
    processNextInQueue();
  });
}

function runWithBrowserQueue(fn, priority = "normal") {
  if (typeof window === "undefined") {
    return fn();
  }
  return new Promise((resolve, reject) => {
    const item = {
      priority,
      execute: () => fn().then(resolve, reject),
    };
    if (priority === "high") {
      browserRequestQueue.unshift(item);
    } else {
      browserRequestQueue.push(item);
    }
    processNextInQueue();
  });
}


/**
 * Helper to determine the language from options, URL, headers, or cookies.
 */
const getCurrentLang = async (endpoint, options) => {
  let lang = "en";
  
  if (options.lang) {
    return options.lang;
  }

  try {
    const tempUrl = new URL(endpoint, "http://localhost");
    const queryLang = tempUrl.searchParams.get("lang");
    if (queryLang) return queryLang;
  } catch (e) {}

  if (typeof window === "undefined") {
    if (!options.skipHeaders) {
      try {
        const { cookies, headers } = await import("next/headers");
        const headersList = await headers();
        const xLocale = headersList.get("x-locale");
        if (xLocale) {
          return xLocale;
        }
        const cookieStore = await cookies();
        const nextLocale = cookieStore.get("NEXT_LOCALE");
        if (nextLocale) return nextLocale.value;
      } catch (error) {
        // If Next.js PPR or static generation throws a digest error due to accessing dynamic APIs, re-throw it.
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
    if (match) return match[2];
  }

  return lang;
};

const sanitizeEncryptedPayloads = (data) => {
  if (!data) return data;
  if (typeof data === "string") {
    const trimmed = data.trim();
    if (trimmed.startsWith("eyJpdiI6") || (trimmed.startsWith('{"iv":') && trimmed.includes('"value":'))) {
      return "";
    }
    return data;
  }
  if (Array.isArray(data)) {
    return data.map(sanitizeEncryptedPayloads);
  }
  if (typeof data === "object") {
    const cleaned = {};
    for (const [key, val] of Object.entries(data)) {
      cleaned[key] = sanitizeEncryptedPayloads(val);
    }
    return cleaned;
  }
  return data;
};

/**
 * Laravel resources are returned in more than one valid shape in this API:
 * a plain array, { data: [] }, or a paginated object. Keep that compatibility
 * in one place so UI components do not each implement a different parser.
 */
export const getApiItems = (payload) => {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
};

export const getApiResource = (payload) => {
  if (payload && payload.data !== undefined && !Array.isArray(payload.data)) {
    return payload.data;
  }

  return payload;
};

export const getApiPage = (payload) => {
  const envelope =
    payload?.data && !Array.isArray(payload.data) && Array.isArray(payload.data.data)
      ? payload.data
      : payload;
  const currentPage = Number(envelope?.current_page ?? envelope?.currentPage ?? 1);
  const lastPage = Number(envelope?.last_page ?? envelope?.lastPage ?? currentPage);
  const explicitHasMore = envelope?.has_more ?? envelope?.hasMore;

  return {
    items: getApiItems(payload),
    currentPage: Number.isFinite(currentPage) && currentPage > 0 ? currentPage : 1,
    lastPage: Number.isFinite(lastPage) && lastPage > 0 ? lastPage : 1,
    hasMore:
      explicitHasMore !== undefined
        ? Boolean(explicitHasMore)
        : currentPage < lastPage,
  };
};

export const fetchAPI = async (endpoint, options = {}) => {
  const lang = await getCurrentLang(endpoint, options);

  // In browser: ALWAYS use same-origin /api/v1 to eliminate all CORS issues completely
  const effectiveBaseUrl =
    typeof window !== "undefined"
      ? `${window.location.origin}/api/v1`
      : API_BASE_URL;

  const cleanEndpoint = endpoint.startsWith("/") ? endpoint : `/${endpoint}`;
  const url = new URL(`${effectiveBaseUrl}${cleanEndpoint}`);
  url.searchParams.set("lang", lang);

  const method = (options.method || "GET").toUpperCase();
  const isGet = method === "GET";
  const isCurrentUserRequest = url.pathname.endsWith("/me");
  const canUseBrowserCache =
    isGet &&
    !isCurrentUserRequest &&
    typeof window !== "undefined" &&
    options.cache !== "no-store" &&
    options.cache !== "reload";

  // Auth is stored in an HttpOnly cookie, so never cache the current user response.
  let cacheKey = null;
  if (canUseBrowserCache) {
    cacheKey = `${url.pathname}${url.search}`;

    const cached = apiCache.get(cacheKey);
    if (cached && Date.now() - cached.timestamp < CACHE_TTL) {
      return cached.promise;
    }
  }

  const doFetch = async () => {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 20000); // 20-second timeout

    try {
      const isFormData =
        typeof FormData !== "undefined" && options.body instanceof FormData;
      const headers = {
        Accept: "application/json",
        "Accept-Language": lang,
        ...options.headers,
      };

      if (!isFormData) {
        headers["Content-Type"] = "application/json";
      }

      const response = await fetch(url.toString(), {
        ...options,
        cache: options.cache || "no-store",
        credentials: options.credentials || "include",
        headers,
        signal: controller.signal,
      });
      clearTimeout(timeoutId);

      if (!response.ok) {
        const expectedAnonymousAuthFailure =
          isCurrentUserRequest && (response.status === 401 || response.status === 419);
        if (!expectedAnonymousAuthFailure) {
          console.warn(`API warning: ${response.statusText}`);
        }
        if (isGet && cacheKey) apiCache.delete(cacheKey); // Don't cache bad responses
        return null;
      }

      const json = await response.json();
      return sanitizeEncryptedPayloads(json);
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
  };

  const fetchPromise = runWithBrowserQueue(doFetch, options.priority || "normal");

  if (canUseBrowserCache && cacheKey) {
    apiCache.set(cacheKey, {
      promise: fetchPromise,
      timestamp: Date.now(),
    });
  }

  return fetchPromise;
};
