import { describe, it, expect } from "vitest";
import { executeWorkerTask } from "../src/lib/worker-pool";

describe("Worker Pool & Parallel Compute Tasks", () => {
  it("filters, searches, and paginates items correctly", async () => {
    const items = [
      { id: 1, title: "Next.js Web Development", category: "dev" },
      { id: 2, title: "Mobile App Flutter", category: "mobile" },
      { id: 3, title: "Cloud Hosting Architecture", category: "hosting" },
      { id: 4, title: "React Native App", category: "mobile" },
      { id: 5, title: "UI/UX Graphic Design", category: "design" },
    ];

    // Filter by category 'mobile'
    const categoryResult = await executeWorkerTask("FILTER_SORT_SEARCH", {
      items,
      categoryFilter: "mobile",
    });
    expect(categoryResult.items.length).toBe(2);
    expect(categoryResult.items.map((i) => i.id)).toEqual([2, 4]);

    // Search query 'app'
    const searchResult = await executeWorkerTask("FILTER_SORT_SEARCH", {
      items,
      query: "app",
      searchFields: ["title"],
    });
    expect(searchResult.items.length).toBe(2);

    // Pagination
    const pageResult = await executeWorkerTask("FILTER_SORT_SEARCH", {
      items,
      page: 1,
      perPage: 2,
    });
    expect(pageResult.items.length).toBe(2);
    expect(pageResult.total).toBe(5);
    expect(pageResult.hasMore).toBe(true);
  });

  it("extracts clean text from HTML strings", async () => {
    const html = "<p>Welcome to <strong>Serv5</strong>! &amp; Enjoy our solutions.<br/>Contact us today.</p>";
    const text = await executeWorkerTask("HTML_TO_TEXT", html);
    expect(text).toContain("Welcome to Serv5! & Enjoy our solutions.");
    expect(text).not.toContain("<p>");
    expect(text).not.toContain("&amp;");
  });

  it("deduplicates items by key", async () => {
    const existing = [{ id: 1 }, { id: 2 }];
    const incoming = [{ id: 2 }, { id: 3 }, { id: 4 }];
    const unique = await executeWorkerTask("DEDUPLICATE_ITEMS", { existing, incoming });
    expect(unique).toEqual([{ id: 3 }, { id: 4 }]);
  });

  it("sanitizes encrypted and malicious payloads", async () => {
    const payload = {
      title: "Real title",
      secret: "eyJpdiI6InRlc3QiLCJ2YWx1ZSI6ImVuY3J5cHRlZCJ9",
      nested: {
        raw: '{"iv":"xyz","value":"secret"}',
        safe: "normal text",
      },
    };
    const cleaned = await executeWorkerTask("SANITIZE_PAYLOAD", payload);
    expect(cleaned.title).toBe("Real title");
    expect(cleaned.secret).toBe("");
    expect(cleaned.nested.raw).toBe("");
    expect(cleaned.nested.safe).toBe("normal text");
  });
});

