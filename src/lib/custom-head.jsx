import React from "react";
import Script from "next/script";
import { parseFragment } from "parse5";

const ALLOWED_HEAD_TAGS = new Set([
  "base",
  "link",
  "meta",
  "noscript",
  "script",
  "style",
  "title",
]);

const BOOLEAN_ATTRIBUTES = new Set([
  "async",
  "defer",
  "disabled",
  "nomodule",
]);

const REACT_ATTRIBUTE_NAMES = {
  charset: "charSet",
  class: "className",
  crossorigin: "crossOrigin",
  "http-equiv": "httpEquiv",
  nomodule: "noModule",
  referrerpolicy: "referrerPolicy",
};

function attributesToProps(attrs = []) {
  return Object.fromEntries(
    attrs
      .filter(({ name, value }) => {
        const normalizedName = name.toLowerCase();
        if (normalizedName.startsWith("on") || normalizedName === "srcdoc") {
          return false;
        }

        if (!["src", "href"].includes(normalizedName)) return true;
        if (String(value || "").trim().startsWith("//")) return false;

        try {
          const url = new URL(value, "https://serv5.com.eg");
          return ["http:", "https:"].includes(url.protocol) || value.startsWith("/");
        } catch {
          return false;
        }
      })
      .map(({ name, value }) => {
        const normalizedName = REACT_ATTRIBUTE_NAMES[name] || name;
        return [
          normalizedName,
          BOOLEAN_ATTRIBUTES.has(name) ? true : value,
        ];
      }),
  );
}

function textContent(node) {
  if (node.nodeName === "#text") return node.value || "";
  return (node.childNodes || []).map(textContent).join("");
}

function parseCustomHead(code) {
  if (!code || typeof code !== "string") return [];

  try {
    return parseFragment(code).childNodes || [];
  } catch {
    return [];
  }
}

export function extractCustomMeta(code) {
  const entries = parseCustomHead(code).flatMap((node) => {
    if (node.tagName !== "meta") return [];

    const props = attributesToProps(node.attrs);
    return props.name && props.content
      ? [[props.name, props.content]]
      : [];
  });

  return Object.fromEntries(entries);
}

export function renderCustomHeadCode(
  code,
  { excludeMeta = false, useNextScript = false, nonce = null } = {},
) {
  const elements = parseCustomHead(code).flatMap((node, index) => {
    const tagName = node.tagName;
    if (!tagName || !ALLOWED_HEAD_TAGS.has(tagName)) return [];

    const props = attributesToProps(node.attrs);
    if (tagName === "meta" && excludeMeta && props.name) return [];

    const key = `custom-head-${index}`;

    if (tagName === "script") {
      const scriptBody = textContent(node).trim();
      const scriptProps = {
        ...props,
        key,
        strategy: useNextScript ? "beforeInteractive" : undefined,
        nonce: nonce || undefined,
      };

      if (scriptBody) {
        scriptProps.id = props.id || key;
        scriptProps.dangerouslySetInnerHTML = { __html: scriptBody };
      }

      return [
        React.createElement(
          useNextScript ? Script : "script",
          scriptProps,
        ),
      ];
    }

    if (tagName === "style") {
      return [
        React.createElement("style", {
          ...props,
          key,
          dangerouslySetInnerHTML: { __html: textContent(node) },
        }),
      ];
    }

    if (["base", "link", "meta"].includes(tagName)) {
      return [React.createElement(tagName, { ...props, key })];
    }

    return [React.createElement(tagName, { ...props, key }, textContent(node))];
  });

  return <>{elements}</>;
}
