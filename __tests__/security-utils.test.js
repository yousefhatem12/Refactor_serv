import { describe, expect, it } from "vitest";
import { sanitizeHtml, htmlToText } from "../src/lib/sanitize-html";
import { safeJsonForScript } from "../src/lib/safe-json";
import { safeUrl } from "../src/lib/safe-url";

describe("security utilities", () => {
  it("removes executable HTML and unsafe URLs while preserving safe formatting", () => {
    const result = sanitizeHtml(
      '<p>Hello <strong>world</strong></p><script>alert(1)</script><a href="javascript:alert(1)" onclick="alert(2)">link</a><img src="https://api.serv5.com.eg/image.webp" onerror="alert(3)">',
    );

    expect(result).toContain("<p>Hello <strong>world</strong></p>");
    expect(result).toContain('<a>link</a>');
    expect(result).toContain('src="https://api.serv5.com.eg/image.webp"');
    expect(result).not.toContain("script");
    expect(result).not.toContain("onclick");
    expect(result).not.toContain("onerror");
    expect(result).not.toContain("javascript:");
  });

  it("converts rich text previews to plain text", () => {
    expect(htmlToText("<h2>Title</h2><p>Hello <em>there</em></p>")).toBe(
      "Title Hello there",
    );
  });

  it("rejects unsafe navigation URLs", () => {
    expect(safeUrl("javascript:alert(1)")).toBe("#");
    expect(safeUrl("https://example.com")).toBe("https://example.com");
    expect(safeUrl("/contact-us")).toBe("/contact-us");
  });

  it("escapes JSON-LD script breakers", () => {
    const json = safeJsonForScript({ title: "</script><script>alert(1)</script>" });

    expect(json).not.toContain("</script>");
    expect(json).toContain("\\u003c/script\\u003e");
  });

  it("downgrades h1 to h2 in article bodies and enforces image accessibility", () => {
    const result = sanitizeHtml('<h1>Article Subtitle</h1><img src="https://serv5.com.eg/img.webp"><a href="http://serv5.eg/contact">Contact</a>');
    expect(result).toContain("<h2>Article Subtitle</h2>");
    expect(result).not.toContain("<h1>");
    expect(result).toContain('alt=""');
    expect(result).toContain('loading="lazy"');
    expect(result).toContain('href="https://serv5.com.eg/contact"');
  });
});
