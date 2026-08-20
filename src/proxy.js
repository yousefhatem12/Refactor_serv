
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

  // In production: remove unsafe-eval and restrict to known script hosts only.
  // In development: keep permissive settings to avoid blocking local tooling.
  const scriptSrc = isDevelopment
    ? `script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http:`
    : `script-src 'self' 'unsafe-inline' https://challenges.cloudflare.com`;

  // In production: restrict frames to known embed hosts only.
  const frameSrc = isDevelopment
    ? "frame-src 'self' https://challenges.cloudflare.com https://maps.google.com https://www.google.com https://www.youtube.com https://youtube.com https: http:"
    : "frame-src 'self' https://challenges.cloudflare.com https://maps.google.com https://www.google.com https://www.youtube.com https://youtube.com";

  const cspDirectives = [
    "default-src 'self'",
    scriptSrc,
    "style-src 'self' 'unsafe-inline' https:",
    `img-src 'self' data: blob: ${apiOrigin} https:${localApiOrigins}`,
    "font-src 'self' data: https:",
    `connect-src 'self' ${apiOrigin} https:${localApiOrigins}`,
    frameSrc,
    `media-src 'self' ${apiOrigin} https:${localApiOrigins}`,
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'self'",
  ];

  if (!isDevelopment) {
    cspDirectives.push("upgrade-insecure-requests");
  }

  return cspDirectives.join("; ");
}

function withSecurityHeaders(response, nonce) {
  const isProd = process.env.NODE_ENV === "production";

  response.headers.set("Content-Security-Policy", createContentSecurityPolicy(nonce));

  // Prevent browsers from MIME-sniffing a response away from the declared Content-Type.
  response.headers.set("X-Content-Type-Options", "nosniff");

  // Disallow embedding the site in an <iframe> on foreign origins (clickjacking defence).
  // frame-ancestors in CSP above already covers modern browsers; this covers IE11/legacy.
  response.headers.set("X-Frame-Options", "SAMEORIGIN");

  // Control how much referrer information is included with requests.
  response.headers.set("Referrer-Policy", "strict-origin-when-cross-origin");

  // Restrict access to sensitive browser APIs that this site does not use.
  response.headers.set(
    "Permissions-Policy",
    "camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()"
  );

  // Enable HSTS in production — tell browsers to only connect over HTTPS for 1 year.
  if (isProd) {
    response.headers.set(
      "Strict-Transport-Security",
      "max-age=31536000; includeSubDomains; preload"
    );
  }

  return response;
}

export function proxy(request) {
  const host = request.headers.get("host") || "";
  const nonce = Buffer.from(crypto.randomUUID()).toString("base64");
  const requestHeaders = new Headers(request.headers);
  requestHeaders.set("x-nonce", nonce);

  // Redirect www to non-www for SEO consistency.
  if (host.startsWith("www.")) {
    const cleanHost = host.replace(/^www\./, "");
    const proto = request.headers.get("x-forwarded-proto") || request.nextUrl.protocol.replace(":", "");
    const redirectUrl = `${proto}://${cleanHost}${request.nextUrl.pathname}${request.nextUrl.search}`;
    return NextResponse.redirect(redirectUrl, 301);
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
  const redirectUrl = new URL(redirectPath, request.url);
  redirectUrl.search = request.nextUrl.search;
  const response = NextResponse.redirect(redirectUrl, 307);

  response.cookies.set("NEXT_LOCALE", locale, { path: "/", maxAge: 31536000 });
  return withSecurityHeaders(response, nonce);
}

export const config = {
  matcher: ["/((?!api|_next|.*\\..*).*)"],
};
