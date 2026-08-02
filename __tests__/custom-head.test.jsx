import React from "react";
import { renderToStaticMarkup } from "react-dom/server";
import { describe, expect, it } from "vitest";
import {
  extractCustomMeta,
  renderCustomHeadCode,
} from "../src000/lib/custom-head";

describe("custom header code", () => {
  it("renders verification and analytics tags without a body wrapper", () => {
    const code = `
      <meta name="google-site-verification" content="verification-token">
      <script async src="https://example.com/analytics.js"></script>
      <script>window.analyticsReady = true;</script>
    `;
    const resources = renderCustomHeadCode(code, {
      excludeMeta: true,
      useNextScript: true,
    });
    const scripts = React.Children.toArray(resources.props.children);

    expect(extractCustomMeta(code)).toEqual({
      "google-site-verification": "verification-token",
    });
    expect(scripts).toHaveLength(2);
    expect(scripts[0].props).toMatchObject({
      async: true,
      src: "https://example.com/analytics.js",
      strategy: "beforeInteractive",
    });
    expect(scripts[1].props).toMatchObject({
      id: "custom-head-5",
      strategy: "beforeInteractive",
      dangerouslySetInnerHTML: {
        __html: "window.analyticsReady = true;",
      },
    });
  });

  it("ignores elements that are invalid inside head", () => {
    const markup = renderToStaticMarkup(
      <>{renderCustomHeadCode('<div>Not valid in head</div><meta name="robots" content="index">')}</>,
    );

    expect(markup).toContain('<meta name="robots" content="index"/>');
    expect(markup).not.toContain("Not valid in head");
  });
});
