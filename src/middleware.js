import { NextResponse } from "next/server";

export function middleware(request) {
  const host = request.headers.get("host") || "";
  const proto = request.headers.get("x-forwarded-proto") || "https";
  const cleanHost = host.replace(/^www\./, "");

  // Redirect www to non-www for SEO consistency
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

  // Check if the pathname starts with a supported locale (/ar or /en)
  const pathParts = pathname.split("/");
  const maybeLocale = pathParts[1];
  const isLocaleInPath = maybeLocale === "ar" || maybeLocale === "en";

  if (isLocaleInPath) {
    const locale = maybeLocale;
    const remainder = "/" + pathParts.slice(2).join("/");

    const requestHeaders = new Headers(request.headers);
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
    return response;
  } else {
    // No locale in path — detect from cookie or browser Accept-Language
    let locale = request.cookies.get("NEXT_LOCALE")?.value;

    if (!locale || (locale !== "ar" && locale !== "en")) {
      const acceptLanguage = request.headers.get("accept-language");
      locale = acceptLanguage && acceptLanguage.includes("ar") ? "ar" : "en";
    }

    const redirectPath = `/${locale}${pathname === "/" ? "" : pathname}`;
    const response = NextResponse.redirect(
      `${proto}://${cleanHost}${redirectPath}${request.nextUrl.search}`,
      307
    );

    response.cookies.set("NEXT_LOCALE", locale, { path: "/", maxAge: 31536000 });
    return response;
  }
}

export const config = {
  matcher: ["/((?!api|_next|.*\\..*).*)" ],
};
