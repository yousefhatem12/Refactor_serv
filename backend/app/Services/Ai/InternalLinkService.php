<?php

namespace App\Services\Ai;

use App\Models\Post;
use App\Models\Service;

class InternalLinkService
{
    /**
     * Adds links only for real, existing internal content.
     * The visible phrase is preserved; only its HTML link wrapper is added.
     */
    public static function apply(array $article, string $languageMode, string $currentKeyword = ''): array
    {
        $targets = self::targets($currentKeyword);
        $suggestions = [];

        foreach (['ar', 'en'] as $lang) {
            if (!self::languageIsEnabled($languageMode, $lang)) {
                continue;
            }

            foreach (["introduction_{$lang}", "body_{$lang}"] as $field) {
                if (empty($article[$field])) {
                    continue;
                }
                [$content, $used] = self::linkText($article[$field], $targets, $lang);
                $article[$field] = $content;
                $suggestions = array_merge($suggestions, $used);
            }
        }

        $article['internal_link_suggestions'] = array_values(array_unique($suggestions, SORT_REGULAR));
        return $article;
    }

    private static function targets(string $currentKeyword): array
    {
        $targets = [
            ['type' => 'page', 'label' => ['ar' => 'خدماتنا', 'en' => 'Our Services'], 'url' => '/services'],
            ['type' => 'page', 'label' => ['ar' => 'المدونة', 'en' => 'Blog'], 'url' => '/blog'],
            ['type' => 'page', 'label' => ['ar' => 'من نحن', 'en' => 'About Us'], 'url' => '/about-us'],
            ['type' => 'page', 'label' => ['ar' => 'تواصل معنا', 'en' => 'Contact Us'], 'url' => '/contact-us'],
        ];

        Post::query()
            ->where('is_published', true)
            ->where('slug', '!=', $currentKeyword)
            ->get(['title', 'slug'])
            ->each(function (Post $post) use (&$targets) {
                $title = is_array($post->title) ? $post->title : [];
                foreach (['ar', 'en'] as $lang) {
                    $label = trim((string) ($title[$lang] ?? ''));
                    if ($label !== '') {
                        $targets[] = ['type' => 'post', 'label' => [$lang => $label], 'url' => '/blog/' . $post->slug];
                    }
                }
            });

        Service::query()
            ->where('is_active', true)
            ->get(['name', 'slug'])
            ->each(function (Service $service) use (&$targets) {
                $name = is_array($service->name) ? $service->name : [];
                foreach (['ar', 'en'] as $lang) {
                    $label = trim((string) ($name[$lang] ?? ''));
                    if ($label !== '') {
                        $targets[] = ['type' => 'service', 'label' => [$lang => $label], 'url' => '/services/services-' . $service->slug];
                    }
                }
            });

        return $targets;
    }

    private static function linkText(string $html, array $targets, string $lang): array
    {
        $used = [];
        $linksAdded = 0;

        $result = preg_replace_callback('/(<a\b[^>]*>.*?<\/a\s*>|<[^>]+>|[^<]+)/isu', function (array $match) use (&$used, &$linksAdded, $targets, $lang) {
            $segment = $match[0];
            if ($linksAdded >= 4 || str_starts_with($segment, '<')) {
                return $segment;
            }

            foreach ($targets as $target) {
                $label = trim((string) ($target['label'][$lang] ?? ''));
                if ($label === '') {
                    continue;
                }

                $pattern = '/(?<![\p{L}\p{N}])(' . preg_quote($label, '/') . ')(?![\p{L}\p{N}])/iu';
                $updated = preg_replace_callback($pattern, function (array $labelMatch) use (&$used, &$linksAdded, $target) {
                    if ($linksAdded >= 4) {
                        return $labelMatch[0];
                    }
                    $linksAdded++;
                    $used[] = ['label' => $labelMatch[0], 'url' => $target['url'], 'type' => $target['type']];
                    return '<a href="' . e($target['url']) . '" class="internal-link">' . $labelMatch[0] . '</a>';
                }, $segment, 1);

                if ($updated !== $segment) {
                    return $updated;
                }
            }

            return $segment;
        }, $html);

        return [$result ?? $html, $used];
    }

    private static function languageIsEnabled(string $languageMode, string $lang): bool
    {
        return $languageMode === 'both' || $languageMode === $lang;
    }
}
