<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class PostContentFormatter
{
    /**
     * @return array{
     *     content: string,
     *     table: array{head: array<int, string>, rows: array<int, array<string, string>>}|null,
     *     list: array<int, array{type: string, list: array<int, string>}>
     * }
     */
    public static function format(mixed $content): array
    {
        $html = self::toHtml($content);
        $html = self::normalizeHtml($html);

        [$document, $xpath] = self::parseHtml($html);

        return [
            'content' => $html,
            'table' => $document && $xpath ? self::extractTable($xpath) : null,
            'list' => $document && $xpath ? self::extractLists($xpath) : [],
        ];
    }

    private static function toHtml(mixed $content): string
    {
        if (is_array($content)) {
            return self::renderer($content)->toHtml();
        }

        if (! is_string($content)) {
            return '';
        }

        $decoded = json_decode($content, true);

        if (is_array($decoded) && ($decoded['type'] ?? null) === 'doc') {
            return self::renderer($decoded)->toHtml();
        }

        return $content;
    }

    private static function renderer(string|array $content): RichContentRenderer
    {
        return RichContentRenderer::make($content)
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public');
    }

    private static function normalizeHtml(string $html): string
    {
        // Some AI providers returned escaped newlines inside otherwise valid HTML.
        // Keep them intact in code samples, but do not expose them as article text.
        $parts = preg_split(
            '/(<(?:pre|code)\b[^>]*>.*?<\/(?:pre|code)>)/is',
            $html,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );

        if ($parts !== false) {
            foreach ($parts as $index => $part) {
                if (preg_match('/^<(?:pre|code)\b/i', $part)) {
                    continue;
                }

                $parts[$index] = str_replace(['\\r\\n', '\\n', '\\r'], "\n", $part);
            }

            $html = implode('', $parts);
        }

        // Keep legacy HTML consistent with the public API contract.
        $html = preg_replace('/<\s*b(\s[^>]*)?>/i', '<strong$1>', $html) ?? $html;
        $html = preg_replace('/<\s*\/\s*b\s*>/i', '</strong>', $html) ?? $html;
        $html = preg_replace('/<\s*(?:s|strike)(\s[^>]*)?>/i', '<del$1>', $html) ?? $html;
        $html = preg_replace('/<\s*\/\s*(?:s|strike)\s*>/i', '</del>', $html) ?? $html;

        return $html;
    }

    /**
     * @return array{DOMDocument|null, DOMXPath|null}
     */
    private static function parseHtml(string $html): array
    {
        if (trim($html) === '' || ! class_exists(DOMDocument::class)) {
            return [null, null];
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="post-content-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return [null, null];
        }

        return [$document, new DOMXPath($document)];
    }

    /**
     * @return array{head: array<int, string>, rows: array<int, array<string, string>>}|null
     */
    private static function extractTable(DOMXPath $xpath): ?array
    {
        $table = $xpath->query('//*[@id="post-content-root"]//table')->item(0);

        if (! $table instanceof DOMElement) {
            return null;
        }

        $rowNodes = $xpath->query('.//tr', $table);

        if (! $rowNodes || $rowNodes->length === 0) {
            return ['head' => [], 'rows' => []];
        }

        $headerRowIndex = null;
        $head = [];

        foreach ($rowNodes as $rowIndex => $row) {
            $headerCells = $xpath->query('./th', $row);

            if ($headerCells && $headerCells->length > 0) {
                $headerRowIndex = $rowIndex;
                $head = self::nodeValues($headerCells);
                break;
            }
        }

        if ($head === []) {
            $firstRowCells = $xpath->query('./th|./td', $rowNodes->item(0));
            $head = self::nodeValues($firstRowCells);
            $headerRowIndex = 0;
        }

        $keys = self::uniqueHeaderKeys($head);
        $rows = [];

        foreach ($rowNodes as $rowIndex => $row) {
            if ($rowIndex === $headerRowIndex) {
                continue;
            }

            $values = self::nodeValues($xpath->query('./th|./td', $row));

            if ($values === []) {
                continue;
            }

            $record = [];
            $columnCount = max(count($keys), count($values));

            for ($column = 0; $column < $columnCount; $column++) {
                $key = $keys[$column] ?? 'column_' . ($column + 1);
                $record[$key] = $values[$column] ?? '';
            }

            $rows[] = $record;
        }

        return ['head' => $head, 'rows' => $rows];
    }

    /**
     * @return array<int, array{type: string, list: array<int, string>}>
     */
    private static function extractLists(DOMXPath $xpath): array
    {
        $lists = [];
        $listNodes = $xpath->query(
            '//*[@id="post-content-root"]//*[self::ol or self::ul][not(ancestor::ol or ancestor::ul)]'
        );

        if (! $listNodes) {
            return [];
        }

        foreach ($listNodes as $listNode) {
            if (! $listNode instanceof DOMElement) {
                continue;
            }

            $items = [];

            foreach ($xpath->query('./li', $listNode) ?: [] as $itemNode) {
                $clone = $itemNode->cloneNode(true);

                if (! $clone instanceof DOMElement) {
                    continue;
                }

                self::removeNestedLists($clone);
                $items[] = self::cleanText($clone->textContent);
            }

            $lists[] = [
                'type' => strtolower($listNode->tagName) === 'ol' ? 'ordered' : 'unordered',
                'list' => $items,
            ];
        }

        return $lists;
    }

    private static function removeNestedLists(DOMNode $node): void
    {
        for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
            $child = $node->childNodes->item($index);

            if (! $child) {
                continue;
            }

            if ($child instanceof DOMElement && in_array(strtolower($child->tagName), ['ol', 'ul'], true)) {
                $node->removeChild($child);
                continue;
            }

            self::removeNestedLists($child);
        }
    }

    /**
     * @return array<int, string>
     */
    private static function nodeValues(iterable|false $nodes): array
    {
        if ($nodes === false) {
            return [];
        }

        $values = [];

        foreach ($nodes as $node) {
            $values[] = self::cleanText($node->textContent);
        }

        return $values;
    }

    /**
     * @param array<int, string> $head
     *
     * @return array<int, string>
     */
    private static function uniqueHeaderKeys(array $head): array
    {
        $used = [];
        $keys = [];

        foreach ($head as $index => $heading) {
            $base = $heading !== '' ? $heading : 'column_' . ($index + 1);
            $key = $base;
            $suffix = 2;

            while (isset($used[$key])) {
                $key = $base . '_' . $suffix++;
            }

            $used[$key] = true;
            $keys[] = $key;
        }

        return $keys;
    }

    private static function cleanText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }
}
