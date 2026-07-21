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

            $languageSuggestions = [];
            foreach (["introduction_{$lang}", "body_{$lang}"] as $field) {
                if (empty($article[$field])) {
                    continue;
                }

                [$content, $used] = self::linkText($article[$field], $targets, $lang);
                $article[$field] = $content;
                $languageSuggestions = array_merge($languageSuggestions, $used);
                $suggestions = array_merge($suggestions, $used);
            }

            // If this language contains none of the existing target phrases,
            // add a small contextual links block instead of silently producing
            // zero first-party links.
            if ($languageSuggestions === []) {
                $field = !empty($article["body_{$lang}"]) ? "body_{$lang}" : "introduction_{$lang}";
                if (empty($article[$field])) {
                    continue;
                }

                $fallbackTargets = self::rankRelevantTargets(
                    $targets,
                    $lang,
                    $currentKeyword,
                    strip_tags((string) ($article["introduction_{$lang}"] ?? '') . ' ' . (string) ($article["body_{$lang}"] ?? ''))
                );

                if ($fallbackTargets === []) {
                    $fallbackTargets = array_values(array_filter(
                        $targets,
                        fn (array $target): bool => in_array(
                            $target['urls']['ar'] ?? null,
                            [
                                'https://serv5.com.eg/ar/services',
                                'https://serv5.com.eg/ar/blog',
                            ],
                            true
                        )
                    ));
                }

                $fallbackTargets = array_slice($fallbackTargets, 0, 2);

                $fallbackLinks = [];
                foreach ($fallbackTargets as $target) {
                    $label = $target['labels'][$lang][0] ?? null;
                    if (!$label) {
                        continue;
                    }

                    $targetUrl = $target['urls'][$lang] ?? null;
                    if (!$targetUrl) {
                        continue;
                    }

                    $fallbackLinks[] = '<a href="' . e($targetUrl) . '" class="internal-link">' . e($label) . '</a>';
                    $suggestions[] = [
                        'label' => $label,
                        'url' => $targetUrl,
                        'type' => $target['type'],
                    ];
                }

                if ($fallbackLinks !== []) {
                    $heading = $lang === 'ar' ? 'اقرأ أيضًا:' : 'Read also:';
                    $article[$field] .= "\n\n<p class=\"internal-links\"><strong>{$heading}</strong> " . implode(' · ', $fallbackLinks) . '</p>';
                }
            }
        }

        $article['internal_link_suggestions'] = array_values(array_unique($suggestions, SORT_REGULAR));

        return $article;
    }

    private static function targets(string $currentKeyword): array
    {
        $targets = [];
        $addPage = static function (array &$list, string $type, array $labels, string $path): void {
            $list[] = [
                'type' => $type,
                'labels' => $labels,
                'urls' => [
                    'ar' => 'https://serv5.com.eg/ar/' . ltrim($path, '/'),
                    'en' => 'https://serv5.com.eg/en/' . ltrim($path, '/'),
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
            $addPage($targets, 'service', $labels, 'services/services-' . $slug);
        }

        Post::query()
            ->where('is_published', true)
            ->where('slug', '!=', $currentKeyword)
            ->get(['title', 'slug'])
            ->each(function (Post $post) use (&$targets): void {
                $title = is_array($post->title) ? $post->title : [];

                foreach (['ar', 'en'] as $lang) {
                    $label = trim((string) ($title[$lang] ?? ''));
                    if ($label !== '') {
                        $targets[] = [
                            'type' => 'post',
                            'labels' => [$lang => [$label]],
                            'urls' => [
                                'ar' => 'https://serv5.com.eg/ar/blog/' . $post->slug,
                                'en' => 'https://serv5.com.eg/en/blog/' . $post->slug,
                            ],
                        ];
                    }
                }
            });

        Service::query()
            ->where('is_active', true)
            ->get(['name', 'slug'])
            ->each(function (Service $service) use (&$targets): void {
                $name = is_array($service->name) ? $service->name : [];

                foreach (['ar', 'en'] as $lang) {
                    $label = trim((string) ($name[$lang] ?? ''));
                    if ($label !== '') {
                        $targets[] = [
                            'type' => 'service',
                            'labels' => [$lang => [$label]],
                            'urls' => [
                                'ar' => 'https://serv5.com.eg/ar/services/services-' . $service->slug,
                                'en' => 'https://serv5.com.eg/en/services/services-' . $service->slug,
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
        $contextTerms = self::meaningfulTerms($keyword . ' ' . $articleText);
        if ($contextTerms === []) {
            return [];
        }

        $ranked = [];
        foreach ($targets as $target) {
            if (!in_array($target['type'], ['post', 'service'], true)) {
                continue;
            }

            $labels = $target['labels'][$lang] ?? [];
            $targetTerms = self::meaningfulTerms(implode(' ', $labels));
            $score = count(array_intersect($contextTerms, $targetTerms));

            if ($score > 0) {
                $target['_relevance'] = $score;
                $ranked[] = $target;
            }
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

        return array_values(array_unique(array_filter(
            $matches[0] ?? [],
            fn (string $term): bool => !in_array($term, $stopWords, true)
        )));
    }
}
