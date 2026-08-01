<?php

namespace App\Services\Ai;

use App\Models\Page;
use App\Models\Service;
use App\Models\Setting;

class BrandContextService
{
    public static function build(string $keyword, string $languageMode, string $categoryContext = ''): string
    {
        $languages = $languageMode === 'both' ? ['ar', 'en'] : [$languageMode === 'en' ? 'en' : 'ar'];
        $queryTerms = self::terms($keyword . ' ' . $categoryContext);

        $services = Service::query()
            ->where('is_active', true)
            ->get(['name', 'slug', 'description', 'sections'])
            ->map(function (Service $service) use ($languages, $queryTerms): array {
                $localized = [];
                foreach ($languages as $language) {
                    $localized[$language] = [
                        'name' => self::localized($service->name, $language, 160),
                        'description' => self::localized($service->description, $language, 420),
                    ];
                }

                $searchText = self::flatten([$service->name, $service->description, $service->sections]);

                return [
                    'slug' => $service->slug,
                    'url_ar' => SiteUrlBuilder::service('ar', $service->slug),
                    'url_en' => SiteUrlBuilder::service('en', $service->slug),
                    'localized' => $localized,
                    '_score' => self::overlap($queryTerms, self::terms($searchText)),
                ];
            })
            ->sortByDesc('_score')
            ->take(5)
            ->values()
            ->map(function (array $service): array {
                unset($service['_score']);
                return $service;
            })
            ->all();

        $pages = Page::query()
            ->whereIn('slug', ['about-us', 'services', 'project-consultation', 'contact-us'])
            ->get(['slug', 'title', 'subtitle', 'content', 'seo_description'])
            ->map(function (Page $page) use ($languages): array {
                $localized = [];
                foreach ($languages as $language) {
                    $localized[$language] = [
                        'title' => self::localized($page->title, $language, 160),
                        'summary' => self::localized(
                            $page->content ?: $page->seo_description ?: $page->subtitle,
                            $language,
                            350
                        ),
                    ];
                }

                return [
                    'slug' => $page->slug,
                    'url_ar' => SiteUrlBuilder::page('ar', $page->slug),
                    'url_en' => SiteUrlBuilder::page('en', $page->slug),
                    'localized' => $localized,
                ];
            })
            ->all();

        $settings = Setting::query()
            ->whereIn('key', ['site_title', 'site_email', 'site_phone', 'site_address'])
            ->get(['key', 'value'])
            ->mapWithKeys(fn (Setting $setting): array => [
                $setting->key => self::localized($setting->value, $languages[0], 300),
            ])
            ->filter()
            ->all();

        $context = [
            'identity' => [
                'brand' => $settings['site_title'] ?? 'Serv5',
                'website' => 'https://serv5.com.eg',
                'contact' => array_filter([
                    'email' => $settings['site_email'] ?? null,
                    'phone' => $settings['site_phone'] ?? null,
                    'address' => $settings['site_address'] ?? null,
                ]),
            ],
            'relevant_services' => $services,
            'official_pages' => $pages,
        ];

        $encode = static fn (array $data): string => (string) json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
                | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
        $json = $encode($context);
        while (mb_strlen($json) > 12000 && count($context['official_pages']) > 1) {
            array_pop($context['official_pages']);
            $json = $encode($context);
        }
        while (mb_strlen($json) > 12000 && count($context['relevant_services']) > 1) {
            array_pop($context['relevant_services']);
            $json = $encode($context);
        }

        return $json;
    }

    private static function localized(mixed $value, string $language, int $limit): string
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (is_array($value)) {
            $value = $value[$language] ?? $value['en'] ?? $value['ar'] ?? self::flatten($value);
        }

        return mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)) ?? ''), 0, $limit);
    }

    private static function flatten(mixed $value): string
    {
        if (is_string($value)) {
            return strip_tags($value);
        }
        if (!is_array($value)) {
            return '';
        }

        $parts = [];
        array_walk_recursive($value, function (mixed $part) use (&$parts): void {
            if (is_string($part)) {
                $parts[] = strip_tags($part);
            }
        });
        return implode(' ', $parts);
    }

    private static function terms(string $text): array
    {
        preg_match_all('/[\p{L}\p{N}]{3,}/u', mb_strtolower($text, 'UTF-8'), $matches);
        return array_values(array_unique($matches[0] ?? []));
    }

    private static function overlap(array $left, array $right): int
    {
        return count(array_intersect($left, $right));
    }
}
