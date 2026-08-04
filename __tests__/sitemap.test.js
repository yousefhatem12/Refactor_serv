import { afterEach, describe, expect, it, vi } from "vitest";

vi.mock("next/server", () => ({
  connection: vi.fn(async () => {}),
}));

import { GET as getSitemapIndex } from "../src/app/sitemap.xml/route";
import { GET as getPageSitemap } from "../src/app/page-sitemap.xml/route";

const originalSiteUrl = process.env.SITE_URL;
const originalPublicSiteUrl = process.env.NEXT_PUBLIC_SITE_URL;

afterEach(() => {
  if (originalSiteUrl === undefined) delete process.env.SITE_URL;
  else process.env.SITE_URL = originalSiteUrl;

  if (originalPublicSiteUrl === undefined) delete process.env.NEXT_PUBLIC_SITE_URL;
  else process.env.NEXT_PUBLIC_SITE_URL = originalPublicSiteUrl;
});

describe("sitemap URLs", () => {
  it("uses the configured public origin instead of an internal proxy origin", async () => {
    process.env.SITE_URL = "https://serv5.com.eg/";

    const indexResponse = await getSitemapIndex();
    const pageResponse = await getPageSitemap();
    const indexXml = await indexResponse.text();
    const pageXml = await pageResponse.text();

    expect(indexXml).toContain(
      "<loc>https://serv5.com.eg/page-sitemap.xml</loc>",
    );
    expect(pageXml).toContain("<loc>https://serv5.com.eg/en</loc>");
    expect(`${indexXml}${pageXml}`).not.toContain("localhost");
    expect(indexResponse.headers.get("Cache-Control")).toBe(
      "public, max-age=0, s-maxage=300, must-revalidate",
    );
  });

  it("falls back to the production domain when no URL is configured", async () => {
    delete process.env.SITE_URL;
    delete process.env.NEXT_PUBLIC_SITE_URL;

    const xml = await (await getSitemapIndex()).text();

    expect(xml).toContain("<loc>https://serv5.com.eg/page-sitemap.xml</loc>");
  });
});
