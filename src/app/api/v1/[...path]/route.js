import { NextResponse } from "next/server";

const TARGET_API =
  process.env.BACKEND_API_URL ||
  process.env.INTERNAL_API_URL ||
  process.env.NEXT_PUBLIC_API_URL ||
  "https://api.serv5.com.eg/api/v1";

const FALLBACK_API = TARGET_API;

async function handleProxy(request, context) {
  const params = await context.params;
  const pathSegments = params?.path || [];
  const subPath = Array.isArray(pathSegments) ? pathSegments.join("/") : pathSegments;
  const { search } = new URL(request.url);

  const forwardHeaders = new Headers();
  for (const [key, value] of request.headers.entries()) {
    const lower = key.toLowerCase();
    // Do not forward hop-by-hop headers
    if (!["host", "connection", "content-length", "transfer-encoding"].includes(lower)) {
      forwardHeaders.set(key, value);
    }
  }

  // Ensure origin is acceptable
  const origin = request.headers.get("origin") || "*";

  let body = undefined;
  if (!["GET", "HEAD"].includes(request.method)) {
    try {
      body = await request.blob();
    } catch {
      // Body empty or unreadable
    }
  }

  const primaryUrl = `${TARGET_API}/${subPath}${search}`;

  try {
    let res = await fetch(primaryUrl, {
      method: request.method,
      headers: forwardHeaders,
      body,
      cache: "no-store",
      signal: AbortSignal.timeout(20000),
    });

    // If local target returned a server error (5xx) or 404 and fallback is different, try fallback
    if (!res.ok && res.status >= 500 && TARGET_API !== FALLBACK_API) {
      const fallbackUrl = `${FALLBACK_API}/${subPath}${search}`;
      res = await fetch(fallbackUrl, {
        method: request.method,
        headers: forwardHeaders,
        body,
        cache: "no-store",
        signal: AbortSignal.timeout(6000),
      });
    }

    const resData = await res.arrayBuffer();
    const resHeaders = new Headers();

    for (const [key, value] of res.headers.entries()) {
      const lower = key.toLowerCase();
      if (!["content-encoding", "transfer-encoding"].includes(lower)) {
        resHeaders.set(key, value);
      }
    }

    // Attach permissive CORS headers so browser NEVER blocks this same-origin response
    resHeaders.set("Access-Control-Allow-Origin", origin);
    resHeaders.set("Access-Control-Allow-Credentials", "true");
    resHeaders.set("Access-Control-Allow-Methods", "GET, POST, PUT, PATCH, DELETE, OPTIONS");
    resHeaders.set("Access-Control-Allow-Headers", "*");

    return new NextResponse(resData, {
      status: res.status,
      statusText: res.statusText,
      headers: resHeaders,
    });
  } catch (error) {
    // If primary backend is offline or failed, try fallback
    if (TARGET_API !== FALLBACK_API) {
      try {
        const fallbackUrl = `${FALLBACK_API}/${subPath}${search}`;
        const res = await fetch(fallbackUrl, {
          method: request.method,
          headers: forwardHeaders,
          body,
          cache: "no-store",
          signal: AbortSignal.timeout(6000),
        });

        const resData = await res.arrayBuffer();
        const resHeaders = new Headers();
        for (const [key, value] of res.headers.entries()) {
          const lower = key.toLowerCase();
          if (!["content-encoding", "transfer-encoding"].includes(lower)) {
            resHeaders.set(key, value);
          }
        }
        resHeaders.set("Access-Control-Allow-Origin", origin);
        resHeaders.set("Access-Control-Allow-Credentials", "true");
        resHeaders.set("Access-Control-Allow-Methods", "GET, POST, PUT, PATCH, DELETE, OPTIONS");
        resHeaders.set("Access-Control-Allow-Headers", "*");

        return new NextResponse(resData, {
          status: res.status,
          statusText: res.statusText,
          headers: resHeaders,
        });
      } catch (fallbackError) {
        return NextResponse.json(
          { error: "Backend API unavailable", details: fallbackError.message },
          { status: 502 }
        );
      }
    }

    return NextResponse.json(
      { error: "Backend connection failed", details: error.message },
      { status: 502 }
    );
  }
}

export async function GET(req, ctx) {
  return handleProxy(req, ctx);
}

export async function POST(req, ctx) {
  return handleProxy(req, ctx);
}

export async function PUT(req, ctx) {
  return handleProxy(req, ctx);
}

export async function PATCH(req, ctx) {
  return handleProxy(req, ctx);
}

export async function DELETE(req, ctx) {
  return handleProxy(req, ctx);
}

export async function OPTIONS(req) {
  const origin = req.headers.get("origin") || "*";
  return new NextResponse(null, {
    status: 204,
    headers: {
      "Access-Control-Allow-Origin": origin,
      "Access-Control-Allow-Credentials": "true",
      "Access-Control-Allow-Methods": "GET, POST, PUT, PATCH, DELETE, OPTIONS",
      "Access-Control-Allow-Headers": "*",
      "Access-Control-Max-Age": "86400",
    },
  });
}

