/**
 * Serv5 Multi-Threaded Parallel Worker Pool
 * Manages concurrent Web Workers across CPU cores to offload heavy calculations.
 * Features:
 * - Dynamic pool sizing based on hardware concurrency (2 to 6 workers)
 * - Round-robin dispatching and message correlation
 * - Automatic retry and error resilience
 * - Seamless fallback to synchronous computation in SSR and Node environments
 */

let workerPool = [];
let nextWorkerIndex = 0;
let messageIdCounter = 0;
const pendingCallbacks = new Map();
let isInitialized = false;

// Fallback synchronous handlers for SSR / unsupported browsers
const fallbackHandlers = {
  FILTER_SORT_SEARCH: (params) => {
    const {
      items = [],
      query = "",
      searchFields = ["title", "name", "description", "content"],
      categoryFilter = null,
      categoryField = "category",
      sortBy = null,
      sortOrder = "asc",
      page = 1,
      perPage = null,
    } = params || {};

    if (!Array.isArray(items)) return { items: [], total: 0, hasMore: false };

    let filtered = items;

    if (categoryFilter && categoryFilter !== "all" && categoryFilter !== "ALL") {
      filtered = filtered.filter((item) => {
        if (!item) return false;
        const cat = item[categoryField] || item.category_raw || item.category_id;
        return String(cat).toLowerCase() === String(categoryFilter).toLowerCase();
      });
    }

    if (query && typeof query === "string" && query.trim().length > 0) {
      const q = query.trim().toLowerCase();
      filtered = filtered.filter((item) => {
        if (!item) return false;
        for (let i = 0; i < searchFields.length; i++) {
          const val = item[searchFields[i]];
          if (typeof val === "string" && val.toLowerCase().includes(q)) {
            return true;
          }
        }
        return false;
      });
    }

    if (sortBy) {
      filtered.sort((a, b) => {
        const valA = a ? a[sortBy] : "";
        const valB = b ? b[sortBy] : "";
        if (valA === valB) return 0;
        const cmp = valA > valB ? 1 : -1;
        return sortOrder === "desc" ? -cmp : cmp;
      });
    }

    const total = filtered.length;
    if (perPage && perPage > 0) {
      const start = (page - 1) * perPage;
      return {
        items: filtered.slice(start, start + perPage),
        total,
        hasMore: start + perPage < total,
        currentPage: page,
        lastPage: Math.ceil(total / perPage) || 1,
      };
    }

    return { items: filtered, total, hasMore: false, currentPage: 1, lastPage: 1 };
  },

  HTML_TO_TEXT: (html) => {
    if (typeof html !== "string" || html.length === 0) return "";
    return html
      .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, " ")
      .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, " ")
      .replace(/<(br|p|div|h[1-6]|li|tr)[^>]*>/gi, " ")
      .replace(/<[^>]+>/g, " ")
      .replace(/&amp;/g, "&")
      .replace(/&lt;/g, "<")
      .replace(/&gt;/g, ">")
      .replace(/&quot;/g, '"')
      .replace(/&#39;/g, "'")
      .replace(/&nbsp;/g, " ")
      .replace(/\s+([!?,.:;])/g, "$1")
      .replace(/\s+/g, " ")
      .trim();
  },

  BATCH_HTML_TO_TEXT: (items) => {
    if (!Array.isArray(items)) return [];
    return items.map((item) => fallbackHandlers.HTML_TO_TEXT(item));
  },

  SANITIZE_HTML: (html) => {
    if (typeof html !== "string" || html.length === 0) return "";
    return html
      .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")
      .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, "")
      .replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, "")
      .replace(/\son\w+="[^"]*"/gi, "");
  },

  SANITIZE_PAYLOAD: (data) => {
    if (!data) return data;
    if (typeof data === "string") {
      if (data.charCodeAt(0) === 101 && data.startsWith("eyJpdiI6")) return "";
      if (data.charCodeAt(0) === 123 && data.startsWith('{"iv":') && data.includes('"value":')) return "";
      return data;
    }
    if (Array.isArray(data)) {
      for (let i = 0; i < data.length; i++) {
        data[i] = fallbackHandlers.SANITIZE_PAYLOAD(data[i]);
      }
      return data;
    }
    if (typeof data === "object") {
      for (const key in data) {
        if (Object.prototype.hasOwnProperty.call(data, key)) {
          data[key] = fallbackHandlers.SANITIZE_PAYLOAD(data[key]);
        }
      }
      return data;
    }
    return data;
  },

  DEDUPLICATE_ITEMS: ({ existing = [], incoming = [], key = "id" }) => {
    const seen = new Set(existing.map((item) => item && item[key]));
    const unique = [];
    for (let i = 0; i < incoming.length; i++) {
      const item = incoming[i];
      if (item && !seen.has(item[key])) {
        seen.add(item[key]);
        unique.push(item);
      }
    }
    return unique;
  },
};

function initWorkerPool() {
  if (typeof window === "undefined" || isInitialized || typeof Worker === "undefined") {
    return;
  }

  try {
    const hardwareThreads = navigator.hardwareConcurrency || 4;
    // Scale pool between 2 and 6 workers for high concurrency without overhead
    const poolSize = Math.min(Math.max(hardwareThreads, 2), 6);

    for (let i = 0; i < poolSize; i++) {
      const worker = new Worker("/workers/parallel-compute-worker.js");
      worker.onmessage = (event) => {
        const { id, success, result, error } = event.data || {};
        if (!id || !pendingCallbacks.has(id)) return;

        const { resolve, reject } = pendingCallbacks.get(id);
        pendingCallbacks.delete(id);

        if (success) {
          resolve(result);
        } else {
          reject(new Error(error || "Worker task execution failed"));
        }
      };

      worker.onerror = (err) => {
        console.warn("Worker pool worker error, falling back:", err);
      };

      workerPool.push(worker);
    }
    isInitialized = true;
  } catch (err) {
    console.warn("Failed to initialize Worker pool, falling back to sync execution:", err);
    workerPool = [];
  }
}

/**
 * Executes a heavy computation task on an available worker thread.
 * Automatically balances across workers or falls back to synchronous execution.
 *
 * @param {string} action Action type (e.g. 'FILTER_SORT_SEARCH', 'HTML_TO_TEXT')
 * @param {any} payload Parameters to pass to the action
 * @param {number} timeoutMs Optional timeout in milliseconds (default 5000)
 * @returns {Promise<any>}
 */
export function executeWorkerTask(action, payload, timeoutMs = 5000) {
  // If in SSR or no worker support, run immediate synchronous fallback
  if (typeof window === "undefined" || typeof Worker === "undefined") {
    const handler = fallbackHandlers[action];
    if (handler) {
      return Promise.resolve(handler(payload));
    }
    return Promise.reject(new Error(`Action ${action} not supported in fallback`));
  }

  // Initialize pool on first browser call
  if (!isInitialized) {
    initWorkerPool();
  }

  if (workerPool.length === 0) {
    const handler = fallbackHandlers[action];
    if (handler) {
      return Promise.resolve(handler(payload));
    }
    return Promise.reject(new Error(`Action ${action} not supported`));
  }

  return new Promise((resolve, reject) => {
    const id = ++messageIdCounter;
    const worker = workerPool[nextWorkerIndex];
    nextWorkerIndex = (nextWorkerIndex + 1) % workerPool.length;

    const timeout = setTimeout(() => {
      if (pendingCallbacks.has(id)) {
        pendingCallbacks.delete(id);
        // On timeout, run synchronous fallback instead of failing
        const handler = fallbackHandlers[action];
        if (handler) {
          resolve(handler(payload));
        } else {
          reject(new Error(`Worker task ${action} timed out after ${timeoutMs}ms`));
        }
      }
    }, timeoutMs);

    pendingCallbacks.set(id, {
      resolve: (val) => {
        clearTimeout(timeout);
        resolve(val);
      },
      reject: (err) => {
        clearTimeout(timeout);
        // On error, fallback to synchronous handler
        const handler = fallbackHandlers[action];
        if (handler) {
          resolve(handler(payload));
        } else {
          reject(err);
        }
      },
    });

    worker.postMessage({ id, action, payload });
  });
}

/**
 * Get active worker pool count
 */
export function getWorkerPoolSize() {
  return workerPool.length;
}
