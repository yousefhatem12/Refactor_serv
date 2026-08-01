import { createElement, Fragment } from "react";
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

const VOID_TAGS = new Set(["base", "link", "meta"]);
const BOOLEAN_ATTRIBUTES = new Set(["async", "defer", "disabled", "nomodule"]);

const REACT_ATTRIBUTE_NAMES = {
  charset: "charSet",
  class: "className",
  crossorigin: "crossOrigin",
  fetchpriority: "fetchPriority",
  for: "htmlFor",
  hreflang: "hrefLang",
  "http-equiv": "httpEquiv",
  imagesizes: "imageSizes",
  imagesrcset: "imageSrcSet",
  itemprop: "itemProp",
  nomodule: "noModule",
  referrerpolicy: "referrerPolicy",
};

function parseStyle(style) {
  return style.split(";").reduce((result, declaration) => {
    const separatorIndex = declaration.indexOf(":");
    if (separatorIndex === -1) return result;

    const property = declaration.slice(0, separatorIndex).trim();
    const value = declaration.slice(separatorIndex + 1).trim();
    if (!property || !value) return result;

    const reactProperty = property.startsWith("--")
      ? property
      : property.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());

    result[reactProperty] = value;
    return result;
  }, {});
}

function attributesToProps(attributes = []) {
  return attributes.reduce((props, attribute) => {
    const originalName = attribute.name.toLowerCase();
    const name = REACT_ATTRIBUTE_NAMES[originalName] || originalName;

    if (originalName.startsWith("on")) {
      return props;
    }

    if (originalName === "style") {
      props.style = parseStyle(attribute.value);
    } else if (BOOLEAN_ATTRIBUTES.has(originalName)) {
      props[name] = true;
    } else {
      props[name] = attribute.value;
    }

    return props;
  }, {});
}

function getTextContent(node) {
  return (node.childNodes || [])
    .filter((child) => child.nodeName === "#text")
    .map((child) => child.value)
    .join("");
}

function renderNode(node, key, options) {
  if (node.nodeName === "#document-fragment" || node.tagName === "head") {
    return createElement(
      Fragment,
      { key },
      ...(node.childNodes || []).map((child, index) =>
        renderNode(child, `${key}-${index}`, options),
      ),
    );
  }

  const tagName = node.tagName?.toLowerCase();
  if (!tagName || !ALLOWED_HEAD_TAGS.has(tagName)) return null;
  if (options.excludeMeta && tagName === "meta") return null;

  const props = {
    ...attributesToProps(node.attrs),
    key,
  };

  if (VOID_TAGS.has(tagName)) {
    return createElement(tagName, props);
  }

  if (tagName === "script") {
    const html = getTextContent(node);
    if (html) props.dangerouslySetInnerHTML = { __html: html };

    if (options.useNextScript) {
      props.strategy = "beforeInteractive";
      if (html && !props.id) props.id = key;

      return createElement(Script, props);
    }

    return createElement(tagName, props);
  }

  if (tagName === "style") {
    const html = getTextContent(node);
    if (html) props.dangerouslySetInnerHTML = { __html: html };

    return createElement(tagName, props);
  }

  const children = (node.childNodes || []).map((child, index) => {
    if (child.nodeName === "#text") return child.value;
    return renderNode(child, `${key}-${index}`, options);
  });

  return createElement(tagName, props, ...children);
}

/**
 * Converts trusted, administrator-managed header HTML into real React head
 * elements. No wrapper element is emitted, so verification meta tags remain
 * direct descendants of <head> in the server-rendered document.
 */
export function renderCustomHeadCode(code, options = {}) {
  if (typeof code !== "string" || !code.trim()) return null;

  const fragment = parseFragment(code);
  return renderNode(fragment, "custom-head", {
    excludeMeta: false,
    useNextScript: false,
    ...options,
  });
}

export function extractCustomMeta(code) {
  if (typeof code !== "string" || !code.trim()) return {};

  const metadata = {};
  const fragment = parseFragment(code);

  function visit(node) {
    if (node.tagName?.toLowerCase() === "meta") {
      const attributes = Object.fromEntries(
        (node.attrs || []).map((attribute) => [
          attribute.name.toLowerCase(),
          attribute.value,
        ]),
      );
      const name = attributes.name;
      const content = attributes.content;

      if (name && content) {
        const existing = metadata[name];
        metadata[name] = existing
          ? Array.isArray(existing)
            ? [...existing, content]
            : [existing, content]
          : content;
      }
    }

    for (const child of node.childNodes || []) visit(child);
  }

  visit(fragment);
  return metadata;
}
