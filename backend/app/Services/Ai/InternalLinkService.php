<?php

namespace App\Services\Ai;

use App\Models\Post;
use App\Models\Service;

class InternalLinkService
{
    private const MIN_SERVICE_LINKS_PER_LANGUAGE = 3;
    private const TARGET_POST_LINKS_PER_LANGUAGE = 3;
    private const MIN_GENERAL_LINKS_PER_LANGUAGE = 2;

    /**
     * Adds links only for real, existing internal content.
     * The visible phrase is preserved; only its HTML link wrapper is added.
     */
    public static function apply(array $article, string $languageMode, string $currentKeyword = '', string $categoryContext = ''): array
    {
        $targets = self::targets($currentKeyword);
        $suggestions = [];

        foreach (['ar', 'en'] as $lang) {
            if (!self::languageIsEnabled($languageMode, $lang)) {
                continue;
            }

            $languageSuggestions = [];
            foreach (["introduction_{$lang}", "body_{$lang}"] as $field) {
                if (empty($article[$field])) {
                    continue;
                }

                preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/isu', (string) $article[$field], $existingLinks, PREG_SET_ORDER);
                foreach ($existingLinks as $existingLink) {
                    foreach ($targets as $target) {
                        if (($target['urls'][$lang] ?? null) !== $existingLink[1]
                            || !in_array($target['type'] ?? null, ['post', 'service', 'page'], true)) {
                            continue;
                        }

                        $existingSuggestion = [
                            'label' => trim(strip_tags($existingLink[2])),
                            'url' => $existingLink[1],
                            'type' => $target['type'],
                        ];
                        $languageSuggestions[] = $existingSuggestion;
                        $suggestions[] = $existingSuggestion;
                        break;
                    }
                }

                [$content, $used] = self::linkText($article[$field], $targets, $lang);
                $article[$field] = $content;
                $languageSuggestions = array_merge($languageSuggestions, $used);
                $suggestions = array_merge($suggestions, $used);
            }

            $field = !empty($article["body_{$lang}"]) ? "body_{$lang}" : "introduction_{$lang}";
            if (empty($article[$field])) {
                continue;
            }

            $rankedTargets = self::rankRelevantTargets(
                $targets,
                $lang,
                trim($currentKeyword . ' ' . $categoryContext),
                strip_tags((string) ($article["introduction_{$lang}"] ?? '') . ' ' . (string) ($article["body_{$lang}"] ?? ''))
            );
            $usedByType = ['post' => [], 'service' => [], 'page' => []];
            foreach ($languageSuggestions as $suggestion) {
                $type = $suggestion['type'] ?? null;
                if (isset($usedByType[$type]) && !empty($suggestion['url'])) {
                    $usedByType[$type][] = $suggestion['url'];
                }
            }
            foreach ($usedByType as $type => $urls) {
                $usedByType[$type] = array_values(array_unique($urls));
            }

            $availablePosts = array_values(array_unique(array_filter(array_map(
                fn (array $target): ?string => ($target['type'] ?? null) === 'post' ? ($target['urls'][$lang] ?? null) : null,
                $targets
            ))));
            $requirements = [
                'post' => min(self::TARGET_POST_LINKS_PER_LANGUAGE, count($availablePosts)),
                'service' => self::MIN_SERVICE_LINKS_PER_LANGUAGE,
                'page' => self::MIN_GENERAL_LINKS_PER_LANGUAGE,
            ];
            $fallbackGroups = ['post' => [], 'service' => [], 'page' => []];

            // Always include the actual site homepage among the general links.
            $homeUrl = SiteUrlBuilder::home($lang);
            if (!in_array($homeUrl, $usedByType['page'], true)) {
                $homeTarget = collect($targets)->first(fn (array $target): bool => ($target['type'] ?? null) === 'page' && ($target['urls'][$lang] ?? null) === $homeUrl);
                if ($homeTarget) {
                    self::addFallbackLink($homeTarget, $lang, $usedByType, $fallbackGroups, $suggestions);
                }
            }

            foreach ($requirements as $type => $requiredCount) {
                if (count($usedByType[$type]) >= $requiredCount) {
                    continue;
                }
                $candidates = array_merge(
                    array_values(array_filter($rankedTargets, fn (array $target): bool => ($target['type'] ?? null) === $type)),
                    array_values(array_filter($targets, fn (array $target): bool => ($target['type'] ?? null) === $type))
                );
                foreach ($candidates as $candidate) {
                    self::addFallbackLink($candidate, $lang, $usedByType, $fallbackGroups, $suggestions);
                    if (count($usedByType[$type]) >= $requiredCount) {
                        break;
                    }
                }
            }

            $headings = $lang === 'ar'
                ? ['post' => 'مقالات مرتبطة:', 'service' => 'خدمات مرتبطة:', 'page' => 'روابط مفيدة:']
                : ['post' => 'Related articles:', 'service' => 'Related services:', 'page' => 'Useful links:'];
            foreach (['post', 'service', 'page'] as $type) {
                if ($fallbackGroups[$type] !== []) {
                    $linksHtml = '<p class="internal-links internal-links-' . $type . '"><strong>' . e($headings[$type]) . '</strong> ' . implode(' · ', $fallbackGroups[$type]) . '</p>';
                    $article[$field] = ArticleContentService::insertBeforeFaqOrConclusion(
                        (string) $article[$field],
                        $linksHtml,
                        $lang
                    );
                }
            }

            if (count($usedByType['service']) < self::MIN_SERVICE_LINKS_PER_LANGUAGE
                || count($usedByType['page']) < self::MIN_GENERAL_LINKS_PER_LANGUAGE) {
                throw new \RuntimeException("Unable to satisfy the required internal-link mix for language '{$lang}'.");
            }
        }

        $article['internal_link_suggestions'] = array_values(array_unique($suggestions, SORT_REGULAR));

        return $article;
    }

    private static function addFallbackLink(array $target, string $lang, array &$usedByType, array &$groups, array &$suggestions): void
    {
        $type = $target['type'] ?? null;
        $url = $target['urls'][$lang] ?? null;
        $label = $target['labels'][$lang][0] ?? null;
        if (!isset($usedByType[$type]) || !$url || !$label || in_array($url, $usedByType[$type], true)) {
            return;
        }

        $usedByType[$type][] = $url;
        $groups[$type][] = '<a href="' . e($url) . '" class="internal-link">' . e($label) . '</a>';
        $suggestions[] = ['label' => $label, 'url' => $url, 'type' => $type];
    }

    private static function targets(string $currentKeyword): array
    {
        $targets = [];
        $addPage = static function (array &$list, string $type, array $labels, string $path): void {
            $list[] = [
                'type' => $type,
                'labels' => $labels,
                'urls' => [
                    'ar' => SiteUrlBuilder::page('ar', $path),
                    'en' => SiteUrlBuilder::page('en', $path),
                ],
            ];
        };

        $addPage($targets, 'page', ['ar' => ['الرئيسية', 'الصفحة الرئيسية'], 'en' => ['Home', 'Homepage']], '');
        $addPage($targets, 'page', ['ar' => ['من نحن', 'عن الشركة', 'عنا'], 'en' => ['About Us', 'About']], 'about-us');
        $addPage($targets, 'page', ['ar' => ['الحسابات البنكية', 'حسابات بنكية'], 'en' => ['Bank Accounts']], 'bank-accounts');
        $addPage($targets, 'page', ['ar' => ['الوظائف', 'وظائف', 'فرص العمل'], 'en' => ['Careers', 'Jobs']], 'careers');
        $addPage($targets, 'page', ['ar' => ['الأسئلة الشائعة', 'الأسئلة المتكررة'], 'en' => ['FAQ', 'Frequently Asked Questions']], 'faq');
        $addPage($targets, 'page', ['ar' => ['الاستشارات', 'استشارة مشروع', 'استشارات المشاريع'], 'en' => ['Project Consultation']], 'project-consultation');
        $addPage($targets, 'page', ['ar' => ['طلب عرض سعر', 'اطلب عرض سعر', 'عرض سعر'], 'en' => ['Request a Quote', 'Request Quote', 'Quote']], 'request-quote');
        $addPage($targets, 'page', ['ar' => ['الأعمال', 'معرض الأعمال', 'سابقة الأعمال'], 'en' => ['Portfolio', 'Our Work']], 'portfolio');
        $addPage($targets, 'page', ['ar' => ['منتجاتنا', 'المنتجات'], 'en' => ['Our Products', 'Products']], 'our-products');
        $addPage($targets, 'page', ['ar' => ['تواصل معنا', 'اتصل بنا'], 'en' => ['Contact Us', 'Contact']], 'contact-us');
        $addPage($targets, 'page', ['ar' => ['خدماتنا', 'الخدمات', 'خدمات الشركة'], 'en' => ['Our Services', 'Services']], 'services');
        $addPage($targets, 'page', ['ar' => ['المدونة', 'مدونتنا', 'مقالاتنا'], 'en' => ['Blog', 'Our Blog', 'Articles']], 'blog');

        $servicePages = [
            'web-hosting' => ['ar' => ['استضافة المواقع', 'استضافة الويب'], 'en' => ['Web Hosting']],
            'ui-ux-design' => ['ar' => ['تصميم واجهة المستخدم', 'تصميم UI UX', 'تصميم تجربة المستخدم'], 'en' => ['UI UX Design', 'UI/UX Design']],
            'mobile-apps' => ['ar' => ['تطبيقات الجوال', 'تطبيقات الهاتف', 'برمجة تطبيقات الهاتف'], 'en' => ['Mobile Apps', 'Mobile Application Development']],
            'seo-optimization' => ['ar' => ['تحسين محركات البحث', 'السيو'], 'en' => ['SEO Optimization', 'Search Engine Optimization']],
            'web-design' => ['ar' => ['تصميم المواقع', 'تصميم المواقع الإلكترونية'], 'en' => ['Web Design']],
            'ecommerce' => ['ar' => ['المتاجر الإلكترونية', 'تصميم المتاجر الإلكترونية'], 'en' => ['Ecommerce', 'E-commerce']],
            'digital-marketing' => ['ar' => ['التسويق الرقمي', 'التسويق الإلكتروني'], 'en' => ['Digital Marketing']],
            'aldaam-alfny' => ['ar' => ['الدعم الفني', 'الدعم التقني'], 'en' => ['Technical Support']],
        ];

        foreach ($servicePages as $slug => $labels) {
            $targets[] = [
                'type' => 'service',
                'labels' => $labels,
                'urls' => [
                    'ar' => SiteUrlBuilder::service('ar', $slug),
                    'en' => SiteUrlBuilder::service('en', $slug),
                ],
            ];
        }

        Post::query()
            ->with('blogCategory:id,name')
            ->where('is_published', true)
            ->get(['id', 'title', 'slug', 'seo_title', 'seo_description', 'blog_category_id'])
            ->each(function (Post $post) use (&$targets, $currentKeyword): void {
                $title = is_array($post->title) ? $post->title : [];

                foreach (['ar', 'en'] as $lang) {
                    $label = trim((string) ($title[$lang] ?? ''));
                    if ($label !== '' && self::normalizeText($label) !== self::normalizeText($currentKeyword)) {
                        $categoryName = is_array($post->blogCategory?->name) ? ($post->blogCategory->name[$lang] ?? '') : '';
                        $targets[] = [
                            'type' => 'post',
                            'labels' => [$lang => [$label]],
                            'search_text' => [$lang => implode(' ', [
                                $label,
                                $categoryName,
                                self::localizedText($post->seo_title, $lang),
                                self::localizedText($post->seo_description, $lang),
                            ])],
                            'urls' => [
                                'ar' => SiteUrlBuilder::blogPost('ar', $post->slug),
                                'en' => SiteUrlBuilder::blogPost('en', $post->slug),
                            ],
                        ];
                    }
                }
            });

        Service::query()
            ->where('is_active', true)
            ->get(['name', 'slug', 'description'])
            ->each(function (Service $service) use (&$targets): void {
                $name = is_array($service->name) ? $service->name : [];

                foreach (['ar', 'en'] as $lang) {
                    $label = trim((string) ($name[$lang] ?? ''));
                    if ($label !== '') {
                        $targets[] = [
                            'type' => 'service',
                            'labels' => [$lang => [$label]],
                            'search_text' => [$lang => implode(' ', [
                                $label,
                                self::localizedText($service->description, $lang),
                            ])],
                            'urls' => [
                                'ar' => SiteUrlBuilder::service('ar', $service->slug),
                                'en' => SiteUrlBuilder::service('en', $service->slug),
                            ],
                        ];
                    }
                }
            });

        return $targets;
    }

    private static function linkText(string $html, array $targets, string $lang): array
    {
        $used = [];
        $linksAdded = 0;

        $result = preg_replace_callback(
            '/(<a\b[^>]*>.*?<\/a\s*>|<[^>]+>|[^<]+)/isu',
            function (array $match) use (&$used, &$linksAdded, $targets, $lang): string {
                $segment = $match[0];

                if ($linksAdded >= 4 || str_starts_with($segment, '<')) {
                    return $segment;
                }

                foreach ($targets as $target) {
                    foreach (($target['labels'][$lang] ?? []) as $label) {
                        $label = trim((string) $label);
                        if ($label === '') {
                            continue;
                        }

                        $pattern = '/(?<![\p{L}\p{N}])(' . preg_quote($label, '/') . ')(?![\p{L}\p{N}])/iu';
                        $updated = preg_replace_callback(
                            $pattern,
                            function (array $labelMatch) use (&$used, &$linksAdded, $target, $lang): string {
                                if ($linksAdded >= 4) {
                                    return $labelMatch[0];
                                }

                                $linksAdded++;
                                $used[] = [
                                    'label' => $labelMatch[0],
                                    'url' => $target['urls'][$lang],
                                    'type' => $target['type'],
                                ];

                                return '<a href="' . e($target['urls'][$lang]) . '" class="internal-link">' . $labelMatch[0] . '</a>';
                            },
                            $segment,
                            1
                        );

                        if ($updated !== $segment) {
                            return $updated;
                        }
                    }
                }

                return $segment;
            },
            $html
        );

        return [$result ?? $html, $used];
    }

    private static function languageIsEnabled(string $languageMode, string $lang): bool
    {
        return $languageMode === 'both' || $languageMode === $lang;
    }

    /**
     * Selects existing posts/services whose titles share meaningful terms
     * with the keyword or the generated article. This avoids requiring an
     * article title to be copied verbatim into the generated text.
     */
    private static function rankRelevantTargets(array $targets, string $lang, string $keyword, string $articleText): array
    {
        $keywordTerms = self::meaningfulTerms($keyword);
        $articleTerms = self::meaningfulTerms($articleText);
        if ($keywordTerms === [] && $articleTerms === []) {
            return [];
        }

        $ranked = [];
        foreach ($targets as $target) {
            if (!in_array($target['type'], ['post', 'service', 'page'], true)) {
                continue;
            }

            $labels = $target['labels'][$lang] ?? [];
            $targetTerms = self::meaningfulTerms(implode(' ', $labels));
            $searchTerms = self::meaningfulTerms((string) ($target['search_text'][$lang] ?? implode(' ', $labels)));
            $score = (self::overlapScore($keywordTerms, $targetTerms) * 10)
                + (self::overlapScore($keywordTerms, $searchTerms) * 5)
                + (self::overlapScore($articleTerms, $targetTerms) * 3)
                + self::overlapScore($articleTerms, $searchTerms);

            $target['_relevance'] = $score;
            $ranked[] = $target;
        }

        usort($ranked, fn (array $left, array $right): int => $right['_relevance'] <=> $left['_relevance']);

        return array_map(function (array $target): array {
            unset($target['_relevance']);
            return $target;
        }, $ranked);
    }

    private static function meaningfulTerms(string $text): array
    {
        $text = strtr(mb_strtolower($text, 'UTF-8'), [
            'أ' => 'ا',
            'إ' => 'ا',
            'آ' => 'ا',
            'ى' => 'ي',
            'ة' => 'ه',
            'ـ' => '',
        ]);

        preg_match_all('/[\p{L}\p{N}]{3,}/u', $text, $matches);

        $stopWords = [
            'من', 'الى', 'على', 'عن', 'في', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك',
            'the', 'and', 'for', 'with', 'from', 'that', 'this', 'our', 'your',
        ];

        $terms = array_map(function (string $term): string {
            if (mb_strlen($term) > 4 && str_starts_with($term, 'ال')) {
                return mb_substr($term, 2);
            }
            return $term;
        }, $matches[0] ?? []);

        return array_values(array_unique(array_filter(
            $terms,
            fn (string $term): bool => !in_array($term, $stopWords, true)
        )));
    }

    private static function overlapScore(array $sourceTerms, array $targetTerms): int
    {
        $score = 0;
        foreach ($sourceTerms as $source) {
            foreach ($targetTerms as $target) {
                $shortest = min(mb_strlen($source), mb_strlen($target));
                if ($source === $target || ($shortest >= 4 && mb_substr($source, 0, $shortest) === mb_substr($target, 0, $shortest))) {
                    $score++;
                    break;
                }
            }
        }
        return $score;
    }

    private static function localizedText(mixed $value, string $lang): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (!is_array($value)) {
            return '';
        }
        if (isset($value[$lang]) && is_string($value[$lang])) {
            return $value[$lang];
        }

        $parts = [];
        array_walk_recursive($value, function (mixed $part) use (&$parts): void {
            if (is_string($part)) {
                $parts[] = $part;
            }
        });
        return implode(' ', $parts);
    }

    private static function normalizeText(string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value) ?? $value), 'UTF-8');
    }
}
