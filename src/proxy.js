import { NextResponse } from "next/server";

const DEFAULT_API_URL = "https://api.serv5.com.eg/api/v1";

function getApiOrigin() {
  try {
    return new URL(process.env.NEXT_PUBLIC_API_URL || DEFAULT_API_URL).origin;
  } catch {
    return new URL(DEFAULT_API_URL).origin;
  }
}

function createContentSecurityPolicy(nonce) {
  const isDevelopment = process.env.NODE_ENV !== "production";
  const apiOrigin = getApiOrigin();
  const localApiOrigins = isDevelopment
    ? " http://localhost:8000 http://127.0.0.1:8000"
    : "";

  return [
    "default-src 'self'",
    `script-src 'self' 'nonce-${nonce}'${isDevelopment ? " 'unsafe-eval'" : ""}`,
    "style-src 'self' 'unsafe-inline'",
    `img-src 'self' data: blob: ${apiOrigin} https://flagcdn.com${localApiOrigins}`,
    "font-src 'self' data:",
    `connect-src 'self' ${apiOrigin}${localApiOrigins}`,
    "frame-src 'self' https://maps.google.com https://www.google.com https://www.youtube.com https://youtube.com",
    `media-src 'self' ${apiOrigin}${localApiOrigins}`,
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'self'",
    "upgrade-insecure-requests",
  ].join("; ");
}

function withSecurityHeaders(response, nonce) {
  response.headers.set("Content-Security-Policy", createContentSecurityPolicy(nonce));
  return response;
}

export function proxy(request) {
  const host = request.headers.get("host") || "";
  const nonce = Buffer.from(crypto.randomUUID()).toString("base64");
  const requestHeaders = new Headers(request.headers);
  requestHeaders.set("x-nonce", nonce);

  // Redirect www to non-www for SEO consistency.
  if (host.startsWith("www.")) {
    const canonicalOrigin = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
    const canonicalUrl = new URL(request.nextUrl.pathname + request.nextUrl.search, canonicalOrigin);

    return NextResponse.redirect(canonicalUrl, 301);
  }

  const { pathname } = request.nextUrl;

  // Exclude internal files, API routes, and static assets.
  if (
    pathname.startsWith("/_next") ||
    pathname.startsWith("/api") ||
    pathname.includes(".")
  ) {
    return withSecurityHeaders(NextResponse.next({ request: { headers: requestHeaders } }), nonce);
  }

  // Check if the pathname starts with a supported locale (/ar or /en).
  const pathParts = pathname.split("/");
  const maybeLocale = pathParts[1];
  const isLocaleInPath = maybeLocale === "ar" || maybeLocale === "en";

  if (isLocaleInPath) {
    const locale = maybeLocale;
    const remainder = "/" + pathParts.slice(2).join("/");

    requestHeaders.set("x-locale", locale);
    requestHeaders.set("x-pathname", pathname);

    let cookieHeader = request.headers.get("cookie") || "";
    cookieHeader = cookieHeader
      .split(";")
      .map((c) => c.trim())
      .filter((c) => c && !c.startsWith("NEXT_LOCALE="))
      .join("; ");

    const newCookie = `NEXT_LOCALE=${locale}`;
    cookieHeader = cookieHeader ? `${cookieHeader}; ${newCookie}` : newCookie;
    requestHeaders.set("cookie", cookieHeader);

    const response = NextResponse.rewrite(new URL(remainder, request.url), {
      request: { headers: requestHeaders },
    });

    response.cookies.set("NEXT_LOCALE", locale, { path: "/", maxAge: 31536000 });
    return withSecurityHeaders(response, nonce);
  }

  // No locale in path — detect from cookie or browser Accept-Language.
  let locale = request.cookies.get("NEXT_LOCALE")?.value;

  if (!locale || (locale !== "ar" && locale !== "en")) {
    const acceptLanguage = request.headers.get("accept-language");
    locale = acceptLanguage && acceptLanguage.includes("ar") ? "ar" : "en";
  }

  const redirectPath = `/${locale}${pathname === "/" ? "" : pathname}`;
  const canonicalOrigin = process.env.NEXT_PUBLIC_SITE_URL || "https://serv5.com.eg";
  const redirectUrl = new URL(redirectPath, canonicalOrigin);
  redirectUrl.search = request.nextUrl.search;
  const response = NextResponse.redirect(redirectUrl, 307);

  response.cookies.set("NEXT_LOCALE", locale, { path: "/", maxAge: 31536000 });
  return withSecurityHeaders(response, nonce);
}

export const config = {
  matcher: ["/((?!api|_next|.*\\..*).*)"],
};
