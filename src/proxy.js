import { NextResponse } from "next/server";

export function proxy(request) {
  const host = request.headers.get("host") || "";
  const proto = request.headers.get("x-forwarded-proto") || "https";
  const cleanHost = host.replace(/^www\./, "");

  // Redirect www to non-www for SEO consistency (e.g. www.serv5.com.eg -> serv5.com.eg)
  if (host.startsWith("www.")) {
    return NextResponse.redirect(
      `${proto}://${cleanHost}${request.nextUrl.pathname}${request.nextUrl.search}`,
      301
    );
  }

  const { pathname } = request.nextUrl;

  // Exclude internal files, API routes, and static assets
  if (
    pathname.startsWith("/_next") ||
    pathname.startsWith("/api") ||
    pathname.includes(".")
  ) {
    return NextResponse.next();
  }

  // 1. Check if the pathname starts with a supported locale (/ar or /en)
  const pathParts = pathname.split("/");
  const maybeLocale = pathParts[1];
  const isLocaleInPath = maybeLocale === "ar" || maybeLocale === "en";

  if (isLocaleInPath) {
    const locale = maybeLocale;
    // Extract the remainder of the path (e.g. "/ar/about-us" -> "/about-us", "/ar" -> "/")
    const remainder = "/" + pathParts.slice(2).join("/");

    // Override the request headers to pass the locale to server-side components/layouts
    const requestHeaders = new Headers(request.headers);
    requestHeaders.set("x-locale", locale);
    requestHeaders.set("x-pathname", pathname);

    // Safely inject/sync cookie to request header for standard cookies() utility, preserving other cookies
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
      request: {
        headers: requestHeaders,
      },
    });

    // Set/update the cookie in the response to keep it in sync in the browser
    response.cookies.set("NEXT_LOCALE", locale, { path: "/", maxAge: 31536000 });
    return response;
  } else {
    // 2. No locale in the path. Determine target locale from cookie or browser language.
    let locale = request.cookies.get("NEXT_LOCALE")?.value;

    if (!locale || (locale !== "ar" && locale !== "en")) {
      const acceptLanguage = request.headers.get("accept-language");
      if (acceptLanguage && acceptLanguage.includes("ar")) {
        locale = "ar";
      } else {
        locale = "en";
      }
    }

    // Redirect to the localized path (e.g., "/about-us" -> "/ar/about-us", "/" -> "/ar")
    const redirectPath = `/${locale}${pathname === "/" ? "" : pathname}`;
    const response = NextResponse.redirect(
      `${proto}://${cleanHost}${redirectPath}${request.nextUrl.search}`,
      307
    );

    // Set the cookie on the redirect response
    response.cookies.set("NEXT_LOCALE", locale, { path: "/", maxAge: 31536000 });
    return response;
  }
}

export const config = {
  matcher: [
    // Skip all internal paths (_next, api) and files with a period in the path
    "/((?!api|_next|.*\\..*).*)",
  ],
};
