# Blog Content API – Frontend Integration

This document describes how the frontend should consume and render formatted blog content from the Serv5 API.

## Endpoint

```http
GET https://api.serv5.com.eg/api/v1/blog/{slug}?lang={language}
```

Parameters:

| Parameter | Location | Values | Description |
| --- | --- | --- | --- |
| `slug` | URL | Post slug or ID | Identifies the requested post. |
| `lang` | Query string | `ar` or `en` | Selects the Arabic or English content. Defaults to English. |

The language may also be sent using the `Accept-Language` header. The `lang` query parameter takes precedence.

Example:

```http
GET https://api.serv5.com.eg/api/v1/blog/practical-roadmap-ecommerce-store-design?lang=ar
Accept: application/json
```

## Response shape

The single-post endpoint wraps the post in a `data` property:

```json
{
  "data": {
    "id": 1015,
    "title": "خريطة طريق لتصميم متجر إلكتروني",
    "slug": "practical-roadmap-ecommerce-store-design",
    "content": "<h2>اختيار المنصة</h2><p>اكتشف كيف يساهم <strong>تحسين محركات البحث</strong> في زيادة الزيارات.</p>",
    "table": null,
    "list": [],
    "author": "Admin",
    "category": "التجارة الإلكترونية",
    "category_raw": "ECOMMERCE",
    "image": "https://api.serv5.com.eg/storage/blog/example.webp",
    "published_at": "01 أغسطس 2026",
    "created_at_iso": "2026-08-01T09:00:00.000000Z",
    "updated_at_iso": "2026-08-01T10:00:00.000000Z",
    "seo": {
      "title": "خريطة طريق لتصميم متجر إلكتروني",
      "description": "دليل عملي لاختيار منصة وتصميم متجر إلكتروني ناجح."
    }
  }
}
```

Frontend normalization:

```js
const payload = await response.json();
const post = payload.data ?? payload;
```

## Formatted `content`

`content` is an HTML string. It must be rendered as HTML, not printed as ordinary text.

Supported formatting includes:

| Formatting | Returned HTML |
| --- | --- |
| Bold | `<strong>content</strong>` |
| Italic | `<em>content</em>` |
| Underline | `<u>content</u>` |
| Strikethrough | `<del>content</del>` |
| Superscript | `content <sup>content</sup>` |
| Subscript | `content <sub>content</sub>` |
| Same-tab link | `<a href="/seo-guide">content</a>` |
| New-tab link | `<a href="https://example.com" target="_blank">content</a>` |
| Paragraph | `<p>content</p>` |
| Alignment | `<p style="text-align: center;">content</p>` |
| Headings | `<h2>` through `<h6>` |
| Ordered list | `<ol><li>...</li></ol>` |
| Unordered list | `<ul><li>...</li></ul>` |
| Table | `<table>...</table>` |
| Quote | `<blockquote>...</blockquote>` |
| Code block | `<pre><code>...</code></pre>` |

Example content value:

```json
{
  "content": "<h2>SEO Basics</h2><p style=\"text-align: center;\">Discover how <strong>SEO</strong> drives <a href=\"/seo-guide\">targeted traffic</a>.</p><p>Open the <a href=\"https://example.com/report\" target=\"_blank\">full report</a>.</p>"
}
```

React rendering example:

```jsx
export function ArticleContent({ content }) {
  if (!content) return null;

  return (
    <article
      className="article-content"
      dangerouslySetInnerHTML={{ __html: content }}
    />
  );
}
```

Only render HTML directly when the response comes from the trusted Serv5 API. If content can come from another or user-controlled source, sanitize it before using `dangerouslySetInnerHTML`.

## Table contract

`table` is `null` when the article does not contain a table.

```json
{
  "table": null
}
```

When a table exists, `head` contains the column labels and `rows` contains objects keyed by those labels:

```json
{
  "table": {
    "head": ["Plan", "Storage", "Price"],
    "rows": [
      {
        "Plan": "Starter",
        "Storage": "10 GB",
        "Price": "$10"
      },
      {
        "Plan": "Business",
        "Storage": "50 GB",
        "Price": "$25"
      }
    ]
  }
}
```

TypeScript type:

```ts
type ArticleTable = {
  head: string[];
  rows: Record<string, string>[];
} | null;
```

Custom table rendering example:

```jsx
function ArticleTable({ table }) {
  if (!table) return null;

  return (
    <table>
      <thead>
        <tr>
          {table.head.map((heading, index) => (
            <th key={`${heading}-${index}`}>{heading}</th>
          ))}
        </tr>
      </thead>
      <tbody>
        {table.rows.map((row, rowIndex) => (
          <tr key={rowIndex}>
            {table.head.map((heading, columnIndex) => (
              <td key={`${heading}-${columnIndex}`}>
                {row[heading] ?? ""}
              </td>
            ))}
          </tr>
        ))}
      </tbody>
    </table>
  );
}
```

## List contract

The property name is `list` (singular). Its value is always an array. It is empty when the article does not contain a list:

```json
{
  "list": []
}
```

Each object describes one ordered or unordered list:

```json
{
  "list": [
    {
      "type": "ordered",
      "list": ["Choose a platform", "Configure payments", "Launch the store"]
    },
    {
      "type": "unordered",
      "list": ["Fast setup", "Secure checkout", "Responsive design"]
    }
  ]
}
```

TypeScript types:

```ts
type ArticleList = {
  type: "ordered" | "unordered";
  list: string[];
};

type ArticleLists = ArticleList[];
```

Custom list rendering example:

```jsx
function ArticleLists({ lists }) {
  return lists.map((entry, index) => {
    const Tag = entry.type === "ordered" ? "ol" : "ul";

    return (
      <Tag key={index}>
        {entry.list.map((item, itemIndex) => (
          <li key={itemIndex}>{item}</li>
        ))}
      </Tag>
    );
  });
}

// Usage
<ArticleLists lists={post.list ?? []} />;
```

## Avoid duplicate rendering

Tables and lists remain included inside the `content` HTML. The structured `table` and `list` properties are also provided for frontends that need custom components.

Choose one strategy:

1. Render the complete `content` HTML, including its tables and lists.
2. Process/remove tables and lists from `content`, then render them using the structured properties.

Do not render the complete HTML and the structured versions together, or the same table/list will appear twice.

## Required article styles

Rendering HTML is not enough when the project uses a CSS reset such as Tailwind Preflight. Headings, tables, and lists need explicit styles. Example:

```css
.article-content h2 {
  margin: 2rem 0 1rem;
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.3;
}

.article-content h3 {
  margin: 1.5rem 0 0.75rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.article-content p {
  margin: 0 0 1rem;
  line-height: 1.8;
}

.article-content ul,
.article-content ol {
  margin: 1rem 0;
  padding-inline-start: 1.5rem;
}

.article-content ul {
  list-style: disc;
}

.article-content ol {
  list-style: decimal;
}

.article-content a {
  color: #ff8a00;
  text-decoration: underline;
}

.article-content table {
  width: 100%;
  margin: 1.5rem 0;
  border-collapse: collapse;
}

.article-content th,
.article-content td {
  padding: 0.75rem;
  border: 1px solid rgb(255 255 255 / 20%);
  text-align: start;
}
```

If Tailwind Typography is installed, the frontend may use its `prose` classes instead of writing all article styles manually.

## Error handling

Expected status codes:

| Status | Meaning |
| --- | --- |
| `200` | Published post returned successfully. |
| `404` | The slug/ID does not exist or the post is not published. |

Example:

```js
const response = await fetch(
  `https://api.serv5.com.eg/api/v1/blog/${slug}?lang=${language}`,
  { headers: { Accept: "application/json" } }
);

if (response.status === 404) {
  // Show the not-found page.
  return null;
}

if (!response.ok) {
  throw new Error(`Blog API failed with status ${response.status}`);
}

const { data: post } = await response.json();
```

