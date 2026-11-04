"use client";
import { useState, useEffect, useRef } from "react";
import { executeWorkerTask } from "./worker-pool";

/**
 * Hook to filter, search, and sort items in parallel Web Workers
 */
export function useWorkerFilter(items, filterOptions) {
  const [result, setResult] = useState(() => ({
    items: Array.isArray(items) ? items : [],
    total: Array.isArray(items) ? items.length : 0,
    hasMore: false,
    currentPage: 1,
    lastPage: 1,
    isProcessing: false,
  }));

  const lastTaskIdRef = useRef(0);

  useEffect(() => {
    let isMounted = true;
    const currentTaskId = ++lastTaskIdRef.current;

    setResult((prev) => ({ ...prev, isProcessing: true }));

    executeWorkerTask("FILTER_SORT_SEARCH", {
      items,
      ...filterOptions,
    })
      .then((data) => {
        if (isMounted && currentTaskId === lastTaskIdRef.current) {
          setResult({
            ...data,
            isProcessing: false,
          });
        }
      })
      .catch((err) => {
        console.warn("Worker filter error:", err);
        if (isMounted && currentTaskId === lastTaskIdRef.current) {
          setResult((prev) => ({ ...prev, isProcessing: false }));
        }
      });

    return () => {
      isMounted = false;
    };
  }, [
    items,
    filterOptions?.query,
    filterOptions?.categoryFilter,
    filterOptions?.page,
    filterOptions?.perPage,
    filterOptions?.sortBy,
    filterOptions?.sortOrder,
  ]);

  return result;
}

/**
 * Hook to extract clean text from HTML in Web Workers
 */
export function useWorkerHtmlToText(htmlString) {
  const [text, setText] = useState(() => {
    if (!htmlString || typeof htmlString !== "string") return "";
    if (!htmlString.includes("<") && !htmlString.includes("&")) return htmlString;
    return "";
  });

  useEffect(() => {
    if (!htmlString || typeof htmlString !== "string") {
      setText("");
      return;
    }

    if (!htmlString.includes("<") && !htmlString.includes("&")) {
      setText(htmlString.trim());
      return;
    }

    let isMounted = true;
    executeWorkerTask("HTML_TO_TEXT", htmlString).then((cleaned) => {
      if (isMounted) setText(cleaned);
    });

    return () => {
      isMounted = false;
    };
  }, [htmlString]);

  return text;
}

