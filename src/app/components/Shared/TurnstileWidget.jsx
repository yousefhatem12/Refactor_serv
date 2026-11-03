"use client";

import Script from "next/script";
import { useEffect, useRef, useState } from "react";
import { useNonce } from "@/app/context/NonceContext";

const SITE_KEY = process.env.NEXT_PUBLIC_TURNSTILE_SITE_KEY || "";
const SCRIPT_ID = "cloudflare-turnstile-api";

/**
 * A small explicit-rendering wrapper around Cloudflare Turnstile.
 * The token is deliberately kept in the parent form so it can be sent to the
 * API together with the rest of the form data.
 */
export default function TurnstileWidget({
  action = "form",
  language = "en",
  onTokenChange,
  className = "",
}) {
  const nonce = useNonce();
  const containerRef = useRef(null);
  const widgetIdRef = useRef(null);
  const callbackRef = useRef(onTokenChange);
  const [scriptReady, setScriptReady] = useState(
    () => typeof window !== "undefined" && Boolean(window.turnstile),
  );
  const [hasError, setHasError] = useState(false);
  const isArabic = language === "ar";

  useEffect(() => {
    callbackRef.current = onTokenChange;
  }, [onTokenChange]);

  // Script deduplication means a later widget may not receive its own onLoad
  // callback. Polling only until the global API is ready handles that case.
  useEffect(() => {
    if (!SITE_KEY || scriptReady || typeof window === "undefined") return undefined;

    const intervalId = window.setInterval(() => {
      if (window.turnstile) {
        setScriptReady(true);
      }
    }, 100);

    return () => window.clearInterval(intervalId);
  }, [scriptReady]);

  useEffect(() => {
    if (!SITE_KEY || !scriptReady || !containerRef.current || !window.turnstile) {
      return undefined;
    }

    callbackRef.current?.("");
    setHasError(false);

    try {
      widgetIdRef.current = window.turnstile.render(containerRef.current, {
        sitekey: SITE_KEY,
        action,
        theme: "dark",
        appearance: "always",
        callback: (token) => callbackRef.current?.(token),
        "expired-callback": () => callbackRef.current?.(""),
        "timeout-callback": () => callbackRef.current?.(""),
        "error-callback": () => {
          setHasError(true);
          callbackRef.current?.("");
        },
      });
    } catch (error) {
      console.error("Failed to render Turnstile:", error);
      window.setTimeout(() => setHasError(true), 0);
      callbackRef.current?.("");
    }

    return () => {
      if (widgetIdRef.current !== null && window.turnstile) {
        try {
          window.turnstile.remove(widgetIdRef.current);
        } catch {
          // The widget may already have been removed by Turnstile.
        }
      }
      widgetIdRef.current = null;
    };
  }, [action, scriptReady]);

  return (
    <div className={`flex flex-col items-start gap-2 ${className}`}>
      {SITE_KEY && (
        <Script
          id={SCRIPT_ID}
          src="https://challenges.cloudflare.com/turnstile/v0/api.js"
          strategy="afterInteractive"
          nonce={nonce || undefined}
          onLoad={() => setScriptReady(true)}
          onError={() => {
            setHasError(true);
            callbackRef.current?.("");
          }}
        />
      )}

      <div ref={containerRef} className="min-h-[65px]" />

      {!SITE_KEY && (
        <p className="text-xs text-amber-300" role="alert">
          {isArabic
            ? "الكابتشا غير مهيأة حالياً. من فضلك تواصل مع مسؤول الموقع."
            : "CAPTCHA is not configured yet. Please contact the site administrator."}
        </p>
      )}

      {hasError && SITE_KEY && (
        <p className="text-xs text-red-400" role="alert">
          {isArabic
            ? "تعذر تحميل الكابتشا. حدّث الصفحة وحاول مرة أخرى."
            : "CAPTCHA could not load. Refresh the page and try again."}
        </p>
      )}
    </div>
  );
}
