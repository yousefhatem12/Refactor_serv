<?php

namespace App\Services\Ai;

use DOMDocument;
use DOMElement;
use DOMNode;

class GeneratedHtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i',
        'blockquote', 'a', 'br', 'section', 'div',
    ];

    private const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form',
        'input', 'button', 'textarea', 'select', 'option', 'link', 'meta', 'base',
    ];

    private const ALLOWED_CLASSES = [
        'article-faq', 'article-faq-item', 'internal-link', 'internal-links',
        'internal-links-post', 'internal-links-service', 'internal-links-page',
        'topic-cluster-links',
    ];

    public static function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        if (!class_exists(DOMDocument::class)) {
            return self::fallback($html);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="serv5-generated-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return self::fallback($html);
        }

        $root = $document->getElementById('serv5-generated-root');
        if (!$root) {
            return self::fallback($html);
        }

        self::cleanChildren($root);

        $clean = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $clean .= $document->saveHTML($child);
        }

        return trim($clean);
    }

    private static function cleanChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if (!$node instanceof DOMElement) {
                if (!in_array($node->nodeType, [XML_TEXT_NODE, XML_CDATA_SECTION_NODE], true)) {
                    $parent->removeChild($node);
                }
                continue;
            }

            $tag = strtolower($node->tagName);
            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $parent->removeChild($node);
                continue;
            }

            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                self::cleanChildren($node);
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }

            self::cleanAttributes($node, $tag);
            self::cleanChildren($node);
        }
    }

    private static function cleanAttributes(DOMElement $element, string $tag): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            if ($name === 'class') {
                $classes = preg_split('/\s+/', trim($attribute->value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $classes = array_values(array_intersect($classes, self::ALLOWED_CLASSES));
                if ($classes === []) {
                    $element->removeAttribute($attribute->name);
                } else {
                    $element->setAttribute('class', implode(' ', $classes));
                }
                continue;
            }

            if ($tag === 'a' && $name === 'href') {
                if (!self::isAllowedHref($attribute->value)) {
                    $element->removeAttribute($attribute->name);
                }
                continue;
            }

            if ($tag === 'a' && $name === 'title') {
                continue;
            }

            $element->removeAttribute($attribute->name);
        }

        if ($tag === 'a' && !$element->hasAttribute('href')) {
            $element->removeAttribute('class');
        }
    }

    private static function isAllowedHref(string $href): bool
    {
        $href = trim(html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($href === '' || str_starts_with($href, '#')) {
            return false;
        }
        if (str_starts_with($href, '/')) {
            return !str_starts_with($href, '//');
        }

        $scheme = strtolower((string) parse_url($href, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($href, PHP_URL_HOST));

        return in_array($scheme, ['http', 'https'], true)
            && ($host === 'serv5.com.eg' || str_ends_with($host, '.serv5.com.eg'));
    }

    private static function fallback(string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|object|embed|svg|math|form)\b[^>]*>.*?</\1>#isu', '', $html) ?? '';
        $html = strip_tags($html, '<p><h2><h3><h4><ul><ol><li><strong><b><em><i><blockquote><a><br><section><div>');
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $html) ?? $html;
        $html = preg_replace('/\s+(style|srcdoc)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $html) ?? $html;
        $html = preg_replace('/href\s*=\s*(["\'])\s*(?:javascript|data|vbscript):.*?\1/iu', '', $html) ?? $html;

        return trim($html);
    }
}
