import { describe, expect, it } from "vitest";
import { getMediaUrl } from "../src/lib/api";

describe("getMediaUrl", () => {
  it("points uploaded settings files to API storage", () => {
    expect(getMediaUrl("settings/logo.webp")).toBe(
      "https://api.serv5.com.eg/storage/settings/logo.webp",
    );
    expect(getMediaUrl("/storage/settings/logo.webp")).toBe(
      "https://api.serv5.com.eg/storage/settings/logo.webp",
    );
  });

  it("keeps absolute media URLs unchanged", () => {
    expect(getMediaUrl("https://cdn.example.com/logo.webp")).toBe(
      "https://cdn.example.com/logo.webp",
    );
  });
});
