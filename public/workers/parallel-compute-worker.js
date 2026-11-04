/**
 * Serv5 Multi-Threaded Parallel Compute Worker
 * Runs CPU-heavy computations off the main thread:
 * 1. Array search, filtering, deduplication, and sorting
 * 2. HTML to clean text extraction & sanitization
 * 3. Encrypted / nested payload sanitization
 * 4. Data aggregation & statistics
 */

self.onmessage = function (e) {
  const { id, action, payload } = e.data || {};
  if (!id) return;

  try {
    let result;
    switch (action) {
      case "FILTER_SORT_SEARCH":
        result = handleFilterSortSearch(payload);
        break;

      case "HTML_TO_TEXT":
        result = handleHtmlToText(payload);
        break;

      case "BATCH_HTML_TO_TEXT":
        result = handleBatchHtmlToText(payload);
        break;

      case "SANITIZE_HTML":
        result = handleSanitizeHtml(payload);
        break;

      case "SANITIZE_PAYLOAD":
        result = handleSanitizePayload(payload);
        break;

      case "DEDUPLICATE_ITEMS":
        result = handleDeduplicateItems(payload);
        break;

      default:
        throw new Error(`Unknown worker action: ${action}`);
    }

    self.postMessage({ id, success: true, result });
  } catch (error) {
    self.postMessage({
      id,
      success: false,
      error: error ? error.message : "Unknown error in worker",
    });
  }
};

/**
 * High-performance array search, category filtering, and sorting
 */
function handleFilterSortSearch(params) {
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

  // Category filter
  if (categoryFilter && categoryFilter !== "all" && categoryFilter !== "ALL") {
    filtered = filtered.filter((item) => {
      if (!item) return false;
      const cat = item[categoryField] || item.category_raw || item.category_id;
      return String(cat).toLowerCase() === String(categoryFilter).toLowerCase();
    });
  }

  // Multi-field text query search
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

  // Sort
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

  // Pagination
  if (perPage && perPage > 0) {
    const start = (page - 1) * perPage;
    const paginatedItems = filtered.slice(start, start + perPage);
    return {
      items: paginatedItems,
      total,
      hasMore: start + perPage < total,
      currentPage: page,
      lastPage: Math.ceil(total / perPage) || 1,
    };
  }

  return {
    items: filtered,
    total,
    hasMore: false,
    currentPage: 1,
    lastPage: 1,
  };
}

/**
 * Fast HTML text stripper and entity decoder
 */
function handleHtmlToText(value) {
  if (typeof value !== "string" || value.length === 0) return "";
  return stripHtmlFast(value);
}

function handleBatchHtmlToText(items) {
  if (!Array.isArray(items)) return [];
  return items.map((item) => (typeof item === "string" ? stripHtmlFast(item) : ""));
}

function stripHtmlFast(html) {
  if (!html.includes("<") && !html.includes("&")) return html.trim();

  // Strip scripts, styles, and templates completely
  let text = html
    .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, " ")
    .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, " ")
    .replace(/<(br|p|div|h[1-6]|li|tr)[^>]*>/gi, " ")
    .replace(/<[^>]+>/g, " ");

  // Decode common HTML entities
  text = text
    .replace(/&amp;/g, "&")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&nbsp;/g, " ")
    .replace(/\s+([!?,.:;])/g, "$1")
    .replace(/\s+/g, " ")
    .trim();

  return text;
}

/**
 * Safe client HTML sanitization
 */
function handleSanitizeHtml(html) {
  if (typeof html !== "string" || html.length === 0) return "";
  if (!html.includes("<") && !html.includes("&")) return html;

  // Remove dangerous tags and inline event handlers
  return html
    .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")
    .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, "")
    .replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, "")
    .replace(/\son\w+="[^"]*"/gi, "")
    .replace(/\son\w+='[^']*'/gi, "")
    .replace(/javascript:[^"']*/gi, "#");
}

/**
 * Fast in-place sanitization of encrypted payloads
 */
function handleSanitizePayload(data) {
  if (!data) return data;
  if (typeof data === "string") {
    if (data.charCodeAt(0) === 101 && data.startsWith("eyJpdiI6")) return "";
    if (data.charCodeAt(0) === 123 && data.startsWith('{"iv":') && data.includes('"value":')) return "";
    return data;
  }
  if (Array.isArray(data)) {
    for (let i = 0; i < data.length; i++) {
      data[i] = handleSanitizePayload(data[i]);
    }
    return data;
  }
  if (typeof data === "object") {
    for (const key in data) {
      if (Object.prototype.hasOwnProperty.call(data, key)) {
        data[key] = handleSanitizePayload(data[key]);
      }
    }
    return data;
  }
  return data;
}

/**
 * Array deduplication by ID or key
 */
function handleDeduplicateItems({ existing = [], incoming = [], key = "id" }) {
  const seen = new Set(existing.map((item) => item && item[key]));
  const uniqueIncoming = [];
  for (let i = 0; i < incoming.length; i++) {
    const item = incoming[i];
    if (item && !seen.has(item[key])) {
      seen.add(item[key]);
      uniqueIncoming.push(item);
    }
  }
  return uniqueIncoming;
}
