import React from "react";
import { renderToStaticMarkup } from "react-dom/server";
import { describe, expect, it } from "vitest";
import {
  extractCustomMeta,
  renderCustomHeadCode,
} from "../src/lib/custom-head";

describe("custom header code", () => {
  it("renders verification and analytics tags as direct head children", () => {
    const code = `
      <meta name="google-site-verification" content="verification-token">
      <script async src="https://example.com/analytics.js"></script>
      <script>window.analyticsReady = true;</script>
    `;
    const resources = renderCustomHeadCode(code, { useNextScript: true });
    const headElements = React.Children.toArray(resources.props.children);

    expect(extractCustomMeta(code)).toEqual({
      "google-site-verification": "verification-token",
    });
    expect(headElements).toHaveLength(3);
    expect(headElements[0].type).toBe("meta");
    expect(headElements[0].props).toMatchObject({
      name: "google-site-verification",
      content: "verification-token",
    });
    expect(headElements[1].props).toMatchObject({
      async: true,
      src: "https://example.com/analytics.js",
      strategy: "beforeInteractive",
    });
    expect(headElements[2].props).toMatchObject({
      id: "custom-head-5",
      strategy: "beforeInteractive",
      dangerouslySetInnerHTML: {
        __html: "window.analyticsReady = true;",
      },
    });
  });

  it("ignores elements that are invalid inside head", () => {
    const markup = renderToStaticMarkup(
      React.createElement(
        "head",
        null,
        renderCustomHeadCode(
          '<div>Not valid in head</div><meta name="robots" content="index" onclick="alert(1)">',
        ),
      ),
    );

    expect(markup.startsWith("<head>")).toBe(true);
    expect(markup).toContain('<meta name="robots" content="index"/>');
    expect(markup).not.toContain("Not valid in head");
    expect(markup).not.toContain("onclick");
    expect(markup).not.toContain("<body");
  });
});
