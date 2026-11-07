import { describe, expect, it, vi } from "vitest";

vi.mock("next/cache", () => ({
  unstable_cache: (fn) => fn,
}));

vi.mock("../src/lib/api", () => ({
  fetchAPI: vi.fn(async (endpoint, options) => ({ endpoint, options })),
}));

import {
  getCachedSettings,
  getCachedHeadSettings,
  getCachedHomeData,
  getCachedPage,
  getCachedService,
  getCachedBlogPost,
  getCachedPortfolioItem,
  getCachedProductItem,
  getCachedServicesList,
  getCachedNavLinks,
} from "../src/lib/cached-api";
import { fetchAPI } from "../src/lib/api";

describe("cached-api helpers", () => {
  it("calls fetchAPI with language and skipHeaders: true for all cache helpers", async () => {
    await getCachedSettings("ar");
    expect(fetchAPI).toHaveBeenCalledWith("/settings?lang=ar", { skipHeaders: true, cache: "no-store" });

    await getCachedHeadSettings("en");
    expect(fetchAPI).toHaveBeenCalledWith("/settings?lang=en", { skipHeaders: true, cache: "no-store" });

    await getCachedHomeData("ar");
    expect(fetchAPI).toHaveBeenCalledWith("/pages/home?lang=ar", { skipHeaders: true, cache: "no-store" });

    await getCachedPage("about", "en");
    expect(fetchAPI).toHaveBeenCalledWith("/pages/about?lang=en", { skipHeaders: true, cache: "no-store" });

    await getCachedService("web-dev", "ar");
    expect(fetchAPI).toHaveBeenCalledWith("/services/web-dev?lang=ar", { skipHeaders: true, cache: "no-store" });

    await getCachedBlogPost(42, "en");
    expect(fetchAPI).toHaveBeenCalledWith("/blog/42?lang=en", { skipHeaders: true, cache: "no-store" });

    await getCachedPortfolioItem(99, "ar");
    expect(fetchAPI).toHaveBeenCalledWith("/portfolio/99?lang=ar", { skipHeaders: true, cache: "no-store" });

    await getCachedProductItem(10, "en");
    expect(fetchAPI).toHaveBeenCalledWith("/products/10?lang=en", { skipHeaders: true, cache: "no-store" });

    await getCachedServicesList("ar");
    expect(fetchAPI).toHaveBeenCalledWith("/services?lang=ar", { skipHeaders: true, cache: "no-store" });

    await getCachedNavLinks("en");
    expect(fetchAPI).toHaveBeenCalledWith("/nav-links?lang=en", { skipHeaders: true, cache: "no-store" });
  });
});

