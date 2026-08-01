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
            $usedUrls = [];
            $usedLabels = [];
            foreach (["introduction_{$lang}", "body_{$lang}"] as $field) {
                if (empty($article[$field])) {
                    continue;
                }

                $content = self::removeGeneratedLinkCollections((string) $article[$field]);
                [$content, $existing] = self::deduplicateExistingLinks($content, $targets, $lang, $usedUrls, $usedLabels);
                $languageSuggestions = array_merge($languageSuggestions, $existing);
                $suggestions = array_merge($suggestions, $existing);

                [$content, $used] = self::linkText($content, $targets, $lang, $usedUrls, $usedLabels);
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
            $fallbackLinks = [];

            // Always include the actual site homepage among the general links.
            $homeUrl = SiteUrlBuilder::home($lang);
            if (!in_array($homeUrl, $usedByType['page'], true)) {
                $homeTarget = collect($targets)->first(fn (array $target): bool => ($target['type'] ?? null) === 'page' && ($target['urls'][$lang] ?? null) === $homeUrl);
                if ($homeTarget) {
                    self::addFallbackLink($homeTarget, $lang, $usedByType, $fallbackLinks, $suggestions, $usedLabels);
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
                    self::addFallbackLink($candidate, $lang, $usedByType, $fallbackLinks, $suggestions, $usedLabels);
                    if (count($usedByType[$type]) >= $requiredCount) {
                        break;
                    }
                }
            }

            if ($fallbackLinks !== []) {
                $article[$field] = self::distributeFallbackLinks((string) $article[$field], $fallbackLinks, $lang);
            }

            if (count($usedByType['service']) < self::MIN_SERVICE_LINKS_PER_LANGUAGE
                || count($usedByType['page']) < self::MIN_GENERAL_LINKS_PER_LANGUAGE) {
                throw new \RuntimeException("Unable to satisfy the required internal-link mix for language '{$lang}'.");
            }
        }

        $article['internal_link_suggestions'] = array_values(array_unique($suggestions, SORT_REGULAR));

        return $article;
    }

    /**
     * Distributes a prepared set of real links through separate paragraphs.
     * Existing duplicate URLs are unwrapped and old generated link sections
     * are removed before insertion.
     */
    public static function distributeUniqueLinks(string $html, array $links, string $lang): string
    {
        $html = self::removeGeneratedLinkCollections($html);
        $seenUrls = [];
        $seenLabels = [];
        $html = preg_replace_callback(
            '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/isu',
            function (array $match) use (&$seenUrls, &$seenLabels): string {
                $url = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $normalizedLabel = self::normalizeText(trim(strip_tags($match[2])));
                if (in_array($url, $seenUrls, true) || isset($seenLabels[$normalizedLabel])) {
                    return $match[2];
                }
                $seenUrls[] = $url;
                $seenLabels[$normalizedLabel] = true;

                return $match[0];
            },
            $html
        ) ?? $html;

        $missing = [];
        foreach ($links as $link) {
            $url = $link['url'] ?? null;
            $normalizedLabel = self::normalizeText((string) ($link['label'] ?? ''));
            if (!$url || $normalizedLabel === '' || in_array($url, $seenUrls, true) || isset($seenLabels[$normalizedLabel])) {
                continue;
            }
            $seenUrls[] = $url;
            $seenLabels[$normalizedLabel] = true;
            $missing[] = $link;
        }

        return self::distributeFallbackLinks($html, $missing, $lang);
    }

    private static function addFallbackLink(array $target, string $lang, array &$usedByType, array &$links, array &$suggestions, array &$usedLabels): void
    {
        $type = $target['type'] ?? null;
        $url = $target['urls'][$lang] ?? null;
        $label = $target['labels'][$lang][0] ?? null;
        $normalizedLabel = self::normalizeText((string) $label);
        if (!isset($usedByType[$type]) || !$url || !$label || in_array($url, $usedByType[$type], true) || isset($usedLabels[$normalizedLabel])) {
            return;
        }

        $usedByType[$type][] = $url;
        $usedLabels[$normalizedLabel] = true;
        $suggestion = ['label' => $label, 'url' => $url, 'type' => $type];
        $links[] = $suggestion;
        $suggestions[] = $suggestion;
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

    private static function removeGeneratedLinkCollections(string $html): string
    {
        $html = preg_replace(
            '/<p\b[^>]*class=["\'][^"\']*\binternal-links(?:-[a-z]+)?\b[^"\']*["\'][^>]*>.*?<\/p>/isu',
            '',
            $html
        ) ?? $html;

        return preg_replace(
            '/<section\b[^>]*class=["\'][^"\']*\btopic-cluster-links\b[^"\']*["\'][^>]*>.*?<\/section>/isu',
            '',
            $html
        ) ?? $html;
    }

    private static function deduplicateExistingLinks(string $html, array $targets, string $lang, array &$usedUrls, array &$usedLabels): array
    {
        $suggestions = [];
        $targetByUrl = [];
        foreach ($targets as $target) {
            $url = $target['urls'][$lang] ?? null;
            if ($url && in_array($target['type'] ?? null, ['post', 'service', 'page'], true)) {
                $targetByUrl[$url] = $target;
            }
        }

        $result = preg_replace_callback(
            '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/isu',
            function (array $match) use (&$usedUrls, &$usedLabels, &$suggestions, $targetByUrl): string {
                $url = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $normalizedLabel = self::normalizeText(trim(strip_tags($match[2])));
                if (in_array($url, $usedUrls, true) || isset($usedLabels[$normalizedLabel])) {
                    return $match[2];
                }

                $usedUrls[] = $url;
                $usedLabels[$normalizedLabel] = true;
                if (isset($targetByUrl[$url])) {
                    $suggestions[] = [
                        'label' => trim(strip_tags($match[2])),
                        'url' => $url,
                        'type' => $targetByUrl[$url]['type'],
                    ];
                }

                return $match[0];
            },
            $html
        );

        return [$result ?? $html, $suggestions];
    }

    private static function distributeFallbackLinks(string $html, array $links, string $lang): string
    {
        if ($links === []) {
            return $html;
        }

        $boundary = preg_match('/<section\b[^>]*class=["\'][^"\']*\barticle-faq\b[^"\']*["\'][^>]*>/iu', $html, $match, PREG_OFFSET_CAPTURE)
            ? $match[0][1]
            : strlen($html);
        $prefix = substr($html, 0, $boundary);
        $suffix = substr($html, $boundary);

        preg_match_all('/<(p|li)\b[^>]*>.*?<\/\1>/isu', $prefix, $matches, PREG_OFFSET_CAPTURE);
        $candidates = array_values(array_filter(
            $matches[0] ?? [],
            fn (array $candidate): bool => !preg_match('/<a\b/i', $candidate[0])
        ));

        $placementCount = min(count($links), count($candidates));
        $replacements = [];
        for ($index = 0; $index < $placementCount; $index++) {
            $candidateIndex = $placementCount === 1
                ? intdiv(count($candidates), 2)
                : (int) round($index * (count($candidates) - 1) / ($placementCount - 1));
            [$block, $offset] = $candidates[$candidateIndex];
            $addition = self::contextualLinkSentence($links[$index], $lang, $index);
            $updated = preg_replace('/<\/(p|li)>$/iu', $addition . '$0', $block, 1) ?? $block;
            $replacements[] = [$offset, strlen($block), $updated];
        }

        usort($replacements, fn (array $left, array $right): int => $right[0] <=> $left[0]);
        foreach ($replacements as [$offset, $length, $replacement]) {
            $prefix = substr_replace($prefix, $replacement, $offset, $length);
        }

        // Extremely short drafts may not contain enough paragraphs. Keep each
        // remaining link in its own sentence instead of recreating a link list.
        foreach (array_slice($links, $placementCount) as $index => $link) {
            $prefix .= '<p>' . ltrim(self::contextualLinkSentence($link, $lang, $placementCount + $index)) . '</p>';
        }

        return $prefix . $suffix;
    }

    private static function contextualLinkSentence(array $link, string $lang, int $index): string
    {
        $anchor = '<a href="' . e($link['url']) . '" class="internal-link">' . e($link['label']) . '</a>';
        $type = $link['type'] ?? 'page';
        if ($type === 'cluster_post') {
            $type = 'post';
        }
        $phrases = $lang === 'ar'
            ? [
                'post' => [' ولمزيد من التفاصيل، راجع %s.', ' وستجد شرحًا أوسع في %s.', ' ويمكنك استكمال الفكرة من خلال %s.'],
                'service' => [' ومن الحلول المرتبطة بالموضوع %s.', ' ويمكن أن تساعدك %s في مرحلة التنفيذ.', ' ولتنفيذ هذه الخطوة عمليًا، تعرّف على %s.'],
                'page' => [' وللمزيد عن Serv5، تفضل بزيارة %s.', ' ويمكنك متابعة التفاصيل عبر %s.', ' وللتعرّف على الخيارات المتاحة، انتقل إلى %s.'],
            ]
            : [
                'post' => [' For more detail, read %s.', ' You can explore the idea further in %s.', ' Continue with the practical context in %s.'],
                'service' => [' A related solution is %s.', ' %s can support this implementation stage.', ' For practical delivery, explore %s.'],
                'page' => [' Learn more about Serv5 through %s.', ' You can find the relevant details on %s.', ' To review the available options, visit %s.'],
            ];
        $variants = $phrases[$type] ?? $phrases['page'];

        return sprintf($variants[$index % count($variants)], $anchor);
    }

    private static function linkText(string $html, array $targets, string $lang, ?array &$usedUrls = null, ?array &$usedLabels = null): array
    {
        $usedUrls ??= [];
        $usedLabels ??= [];
        $used = [];
        $linksAdded = 0;
        $insideBlock = false;
        $blockHasLink = false;
        $plainTextHasLink = false;

        $result = preg_replace_callback(
            '/(<a\b[^>]*>.*?<\/a\s*>|<[^>]+>|[^<]+)/isu',
            function (array $match) use (&$used, &$usedUrls, &$usedLabels, &$linksAdded, &$insideBlock, &$blockHasLink, &$plainTextHasLink, $targets, $lang): string {
                $segment = $match[0];

                if (preg_match('/^<(p|li)\b/i', $segment)) {
                    $insideBlock = true;
                    $blockHasLink = false;
                    return $segment;
                }
                if (preg_match('/^<\/(p|li)>/i', $segment)) {
                    $insideBlock = false;
                    $blockHasLink = false;
                    return $segment;
                }
                if (preg_match('/^<a\b/i', $segment)) {
                    $insideBlock ? $blockHasLink = true : $plainTextHasLink = true;
                    return $segment;
                }
                if ($linksAdded >= 4 || str_starts_with($segment, '<') || ($insideBlock ? $blockHasLink : $plainTextHasLink)) {
                    return $segment;
                }

                foreach ($targets as $target) {
                    $url = $target['urls'][$lang] ?? null;
                    if (!$url || in_array($url, $usedUrls, true)) {
                        continue;
                    }
                    foreach (($target['labels'][$lang] ?? []) as $label) {
                        $label = trim((string) $label);
                        $normalizedLabel = self::normalizeText($label);
                        if ($label === '' || isset($usedLabels[$normalizedLabel])) {
                            continue;
                        }

                        $pattern = '/(?<![\p{L}\p{N}])(' . preg_quote($label, '/') . ')(?![\p{L}\p{N}])/iu';
                        $updated = preg_replace_callback(
                            $pattern,
                            function (array $labelMatch) use (&$used, &$usedUrls, &$usedLabels, &$linksAdded, &$blockHasLink, &$plainTextHasLink, $insideBlock, $target, $lang): string {
                                if ($linksAdded >= 4) {
                                    return $labelMatch[0];
                                }

                                $linksAdded++;
                                $usedUrls[] = $target['urls'][$lang];
                                $usedLabels[self::normalizeText($labelMatch[0])] = true;
                                $insideBlock ? $blockHasLink = true : $plainTextHasLink = true;
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
