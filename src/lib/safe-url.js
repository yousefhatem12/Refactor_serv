const ALLOWED_PROTOCOLS = new Set(["http:", "https:", "mailto:", "tel:"]);

export function safeUrl(value, fallback = "#") {
  if (typeof value !== "string") return fallback;

  const candidate = value.trim();
  if (!candidate || candidate.startsWith("//")) return fallback;
  if (candidate.startsWith("/") || candidate.startsWith("#")) return candidate;

  try {
    const url = new URL(candidate, "https://serv5.com.eg");
    return ALLOWED_PROTOCOLS.has(url.protocol) ? candidate : fallback;
  } catch {
    return fallback;
  }
}
