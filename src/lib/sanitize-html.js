import { parseFragment } from "parse5";

const ALLOWED_TAGS = new Set([
  "a",
  "b",
  "blockquote",
  "br",
  "code",
  "del",
  "div",
  "em",
  "h1",
  "h2",
  "h3",
  "h4",
  "h5",
  "h6",
  "hr",
  "i",
  "img",
  "li",
  "ol",
  "p",
  "pre",
  "s",
  "span",
  "strong",
  "table",
  "tbody",
  "td",
  "tfoot",
  "th",
  "thead",
  "tr",
  "u",
  "ul",
]);

const DROP_CONTENT_TAGS = new Set([
  "applet",
  "embed",
  "iframe",
  "object",
  "script",
  "style",
  "template",
]);

const VOID_TAGS = new Set(["br", "hr", "img"]);
const GLOBAL_ATTRIBUTES = new Set(["title"]);
const TAG_ATTRIBUTES = {
  a: new Set(["href", "target", "rel"]),
  img: new Set(["src", "alt", "width", "height", "loading", "decoding"]),
  td: new Set(["colspan", "rowspan", "scope"]),
  th: new Set(["colspan", "rowspan", "scope"]),
};

function escapeText(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;");
}

function escapeAttribute(value) {
  return escapeText(value).replaceAll('"', "&quot;");
}

function isSafeUrl(value, attribute) {
  let candidate = String(value || "").trim();
  if (!candidate || candidate.startsWith("//")) return null;

  if (attribute === "href") {
    // Rewrite legacy/broken serv5.eg typo to official domain
    candidate = candidate.replace(/https?:\/\/serv5\.eg(?!\.com)/gi, "https://serv5.com.eg");
  }

  if (candidate.startsWith("/") || candidate.startsWith("#")) {
    return candidate;
  }

  try {
    const url = new URL(candidate, "https://serv5.com.eg");
    const allowedProtocols = attribute === "href"
      ? new Set(["http:", "https:", "mailto:", "tel:"])
      : new Set(["http:", "https:"]);

    return allowedProtocols.has(url.protocol) ? candidate : null;
  } catch {
    return null;
  }
}

function getSafeAttributes(node, tagName) {
  const allowed = TAG_ATTRIBUTES[tagName] || new Set();
  const attributes = [];
  let targetBlank = false;
  let hasAlt = false;
  let hasLoading = false;

  for (const attribute of node.attrs || []) {
    const name = String(attribute.name || "").toLowerCase();
    if (name.startsWith("on") || name === "srcdoc") continue;
    if (!GLOBAL_ATTRIBUTES.has(name) && !allowed.has(name)) continue;

    let value = String(attribute.value || "").trim();
    if (name === "href" || name === "src") {
      value = isSafeUrl(value, name);
      if (!value) continue;
    }

    if (name === "alt") hasAlt = true;
    if (name === "loading") hasLoading = true;
    if (name === "target" && value === "_blank") targetBlank = true;
    attributes.push(` ${name}="${escapeAttribute(value)}"`);
  }

  if (tagName === "a" && targetBlank && !attributes.some((attr) => attr.startsWith(" rel="))) {
    attributes.push(' rel="noopener noreferrer"');
  }

  if (tagName === "img") {
    if (!hasAlt) attributes.push(' alt=""');
    if (!hasLoading) attributes.push(' loading="lazy"');
  }

  return attributes.join("");
}

function serializeNode(node) {
  if (!node) return "";
  if (node.nodeName === "#text") return escapeText(node.value || "");
  if (node.nodeName === "#comment") return "";

  const rawTagName = String(node.tagName || "").toLowerCase();
  if (!rawTagName) return (node.childNodes || []).map(serializeNode).join("");
  if (DROP_CONTENT_TAGS.has(rawTagName)) return "";

  // Downgrade any <h1> inside article bodies to <h2> to maintain a single H1 per page
  const tagName = rawTagName === "h1" ? "h2" : rawTagName;

  const children = (node.childNodes || []).map(serializeNode).join("");
  if (!ALLOWED_TAGS.has(tagName)) return children;
  if (VOID_TAGS.has(tagName)) {
    return `<${tagName}${getSafeAttributes(node, tagName)} />`;
  }

  return `<${tagName}${getSafeAttributes(node, tagName)}>${children}</${tagName}>`;
}

export function sanitizeHtml(value) {
  if (typeof value !== "string" || value.length === 0) return "";

  try {
    return (parseFragment(value).childNodes || []).map(serializeNode).join("");
  } catch {
    return escapeText(value);
  }
}

function collectText(node) {
  if (!node) return "";
  if (node.nodeName === "#text") return node.value || "";
  if (node.nodeName === "#comment") return "";

  const tagName = String(node.tagName || "").toLowerCase();
  const separator = tagName === "br" ? "\n" : " ";
  return `${(node.childNodes || []).map(collectText).join("")}${separator}`;
}

export function htmlToText(value) {
  if (typeof value !== "string" || value.length === 0) return "";

  try {
    return (parseFragment(value).childNodes || [])
      .map(collectText)
      .join("")
      .replace(/\s+/g, " ")
      .trim();
  } catch {
    return value.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
  }
}
