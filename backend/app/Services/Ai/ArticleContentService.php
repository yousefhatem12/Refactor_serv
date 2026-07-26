<?php

namespace App\Services\Ai;

class ArticleContentService
{
    /**
     * Builds the system instructions prompt including structural rules.
     *
     * @param string $mainKeyword
     * @param array $secondaryKeywords
     * @param string $language  ar|en|both
     * @param int $length
     * @return string
     */
    public static function buildPrompt(
        string $mainKeyword,
        array $secondaryKeywords,
        string $language,
        int $length
    ): string {
        $secondaryKeywordsStr = implode(', ', $secondaryKeywords);
        $editorialAngles = [
            'a practical implementation roadmap with decision points',
            'a problem-and-solution walkthrough built around a realistic scenario',
            'a comparison of options, trade-offs, and when each option fits',
            'a mistake-prevention guide with a diagnostic checklist',
            'a step-by-step guide that takes a reader from planning to measurement',
        ];
        $editorialAngle = $editorialAngles[random_int(0, count($editorialAngles) - 1)];

        $prompt = "You are a senior SEO editor and native-level copywriter. Generate a useful, specific, human-sounding blog article in " . strtoupper($language) . ".\n";
        $prompt .= "Target Word Count: approximately {$length} words.\n";
        $prompt .= "Primary Keyword: '{$mainKeyword}'\n";
        $prompt .= "Secondary Keywords: '{$secondaryKeywordsStr}'\n\n";
        $prompt .= "Private editorial angle for this draft: {$editorialAngle}. Use it to make this version materially different from generic articles; do not mention this instruction or the angle label in the output.\n\n";

        $prompt .= "CRITICAL OUTPUT RULES:\n";
        $prompt .= "- Return exactly one JSON object. Do not use Markdown fences, commentary, or text before/after the object.\n";
        $prompt .= "- Escape quotation marks and backslashes inside JSON strings. Never emit raw newlines, tabs, or control characters inside strings.\n";
        $prompt .= "- Use valid UTF-8 text and never output mojibake.\n";
        if ($language === 'both') {
            $prompt .= "You are in BOTH mode. You MUST generate BOTH the Arabic and English fields! Never leave the English fields or Arabic fields as null. Complete all 'title_en', 'introduction_en', 'body_en', 'seo_title_en', 'meta_description_en' AND 'title_ar', 'introduction_ar', 'body_ar', 'seo_title_ar', 'meta_description_ar' fields. The English fields must be a high-quality translation/adaptation of the Arabic content.\n";
        } else {
            $prompt .= "Depending on the selected language mode ('{$language}'), you must populate only the requested fields in the JSON response, leaving others as null.\n";
        }
        $prompt .= "1. You must respond with a single valid JSON object containing exactly the fields matching this JSON schema:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title_en\": \"English Title (or null if language is ar)\",\n";
        $prompt .= "  \"title_ar\": \"Arabic Title (or null if language is en)\",\n";
        $prompt .= "  \"slug\": \"short-english-only-slug\",\n";
        $prompt .= "  \"seo_title_en\": \"English SEO Title 50-60 chars (or null if language is ar)\",\n";
        $prompt .= "  \"seo_title_ar\": \"Arabic SEO Title 50-60 chars (or null if language is en)\",\n";
        $prompt .= "  \"meta_description_en\": \"English meta description 140-160 chars with keyword & CTA (or null if language is ar)\",\n";
        $prompt .= "  \"meta_description_ar\": \"Arabic meta description 140-160 chars with keyword & CTA (or null if language is en)\",\n";
        $prompt .= "  \"main_keyword\": \"{$mainKeyword}\",\n";
        $prompt .= "  \"secondary_keywords\": [\"keyword1\", \"keyword2\"],\n";
        $prompt .= "  \"lsi_keywords\": [\"lsi1\", \"lsi2\"],\n";
        $prompt .= "  \"introduction_en\": \"English introduction paragraph, 100-150 words containing main keyword (or null if language is ar)\",\n";
        $prompt .= "  \"introduction_ar\": \"Arabic introduction paragraph, 100-150 words containing main keyword (or null if language is en)\",\n";
        $prompt .= "  \"body_en\": \"English full HTML body with H2, H3, lists, bold text. No H1 tags (or null if language is ar)\",\n";
        $prompt .= "  \"body_ar\": \"Arabic full HTML body with H2, H3, lists, bold text. No H1 tags (or null if language is en)\",\n";
        $prompt .= "  \"faq\": [\n";
        $prompt .= "    {\n";
        $prompt .= "       \"question_en\": \"English Q (or null if language is ar)\",\n";
        $prompt .= "       \"question_ar\": \"Arabic Q (or null if language is en)\",\n";
        $prompt .= "       \"answer_en\": \"English A (or null if language is ar)\",\n";
        $prompt .= "       \"answer_ar\": \"Arabic A (or null if language is en)\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"internal_link_suggestions\": [],\n";
        $prompt .= "  \"image\": {\n";
        $prompt .= "    \"name\": \"seo-image-name.jpg\",\n";
        $prompt .= "    \"alt\": \"Image alt description including primary keyword\",\n";
        $prompt .= "    \"title\": \"Image title attribute\",\n";
        $prompt .= "    \"caption\": \"Image caption visible to readers\"\n";
        $prompt .= "  },\n";
        $prompt .= "  \"schema_type\": \"BlogPosting\",\n";
        $prompt .= "  \"schema_markup\": {},\n";
        $prompt .= "  \"word_count\": 0,\n";
        $prompt .= "  \"readability_score\": 0,\n";
        $prompt .= "  \"final_quality_notes\": []\n";
        $prompt .= "}\n\n";

        $prompt .= "ORIGINALITY AND EDITORIAL VALUE — THIS IS MANDATORY:\n";
        $prompt .= "- Write from an original editorial brief that you create internally before drafting: identify the reader's exact intent, one clear thesis, and a practical outcome. Do not output the brief.\n";
        $prompt .= "- Do not copy, quote, stitch together, translate, or lightly paraphrase any article, search result, template, or memorized web passage. Do not reproduce familiar opening or conclusion formulas.\n";
        $prompt .= "- Every article must contain at least three concrete, topic-specific value elements: an original scenario or example, a practical workflow/checklist, and a trade-off, mistake, or decision criterion. Do not invent statistics, customer results, prices, names, or first-hand experiences.\n";
        $prompt .= "- Explain the topic with your own reasoning and structure. Avoid generic statements that could fit any business or any SEO article. Replace them with details that are useful for this exact keyword and audience.\n";
        $prompt .= "- Do not begin English content with phrases such as 'In today's digital world', 'SEO is a fundamental aspect', 'In this article, we will', 'In conclusion', or 'cannot be overstated'. Do not begin Arabic content with 'في عالم اليوم', 'لا شك أن', 'مما لا شك فيه', or 'في ظل التطور التكنولوجي'. Start with the reader's problem, a concrete situation, or a specific observation.\n";
        $prompt .= "- English and Arabic must each be written natively. In BOTH mode, create an independent English adaptation with different natural sentence patterns; do not translate sentence by sentence or preserve the Arabic paragraph order when that makes the English sound formulaic.\n";
        $prompt .= "- Vary paragraph openings, sentence rhythm, transitions, examples, and heading wording. Do not repeat the same claim in the introduction, body, FAQ, and conclusion.\n";
        $prompt .= "- The final draft must be useful even if the primary keyword is removed. Keywords support the explanation; they must never control the prose.\n\n";

        $prompt .= "CONTENT QUALITY RULES (Based on Professional SEO Writing Guidelines):\n";
        $prompt .= "- **No AI Clichés:** Never use clichés like 'في عالم اليوم', 'لا شك أن', 'مما لا شك فيه', 'في ظل التطور التكنولوجي', 'يجدر بالذكر', 'من المهم أن تعرف'. Start directly into the topic.\n";
        $prompt .= "- Tone: professional, engaging, natural, and specific; never make unsupported claims.\n";
        $prompt .= "- Grammar, spelling, punctuation, and terminology must be polished.\n";
        $prompt .= "- **No H1 in Content:** Do not output any <h1> tags inside 'body_en' or 'body_ar'. H1 is rendered by the frontend automatically using the page title.\n";
        $prompt .= "- Paragraphs: keep most paragraphs between 40 and 90 words; split long ideas naturally.\n";
        $prompt .= "- Sentences: vary sentence length and avoid sentences longer than 30 words.\n";
        $prompt .= "- Structure: use one page H1 only (the title), then a logical H2 > H3 > H4 hierarchy. Use H2 for every main idea and H3/H4 only when needed.\n";
        $prompt .= "- Article balance: aim for approximately 10% introduction, 80% useful main content, and 10% conclusion. Make the conclusion a distinct final section.\n";
        $prompt .= "- Readability: use short paragraphs, varied sentence lengths, simple vocabulary, and logical transitions. Add a list when it improves scanning.\n";
        $prompt .= "- **Keyword Placement:** The primary keyword '{$mainKeyword}' must appear in the title, first 100 words of the introduction, at least one H2, naturally in content, and in the conclusion.\n";
        $prompt .= "- Keyword density: use the primary keyword naturally; never force repetition or awkward phrasing.\n";
        $prompt .= "- **Lists:** Structure details using HTML lists (<ul>, <li>) where appropriate.\n";
        $prompt .= "- **No External Links:** Do not include external links to websites outside allowed local domains.\n";
        $prompt .= "- Internal links: mention relevant related topics naturally (for example SEO, web design, e-commerce, or app development) and list them in 'internal_link_suggestions'; the system will convert only matching real blog/service/page titles into clickable local links. Never invent URLs or external links.\n";
        $prompt .= "- **No Emojis:** Do not include Emojis anywhere in the content, title, or descriptions.\n";
        $prompt .= "- **FAQ:** Include 4 to 6 questions and answers in the 'faq' section.\n";
        $prompt .= "- **Conclusion:** Summarize key points, include primary keyword, and end with a simple Call to Action (CTA).\n";
        $prompt .= "- Do not copy or repeat the same sentence, paragraph, or idea. Every paragraph must add a new useful point. Do not invent statistics, sources, prices, guarantees, or facts.\n";
        $prompt .= "- Before returning JSON, perform an originality pass: remove generic filler, stock introductions, repeated claims, and any wording that sounds like a standard online article. Keep only specific, useful, defensible content.\n";
        $prompt .= "- Then run a final self-check: SEO titles 50-60 characters, meta descriptions 140-160 characters, introductions 100-150 words, natural keyword density around 0.8%-1.5%, and no empty required fields. Fix these values in the draft before responding.\n";

        return $prompt;
    }

    /**
     * Validates parsed article properties and returns the quality report.
     *
     * @param array $article
     * @param string $mainKeyword
     * @param string $languageMode ar|en|both
     * @return array
     */
    public static function validateAndGenerateReport(array $article, string $mainKeyword, string $languageMode = 'ar'): array
    {
        $errors = [];
        $warnings = [];

        // 1. Validate Required Fields by Language Mode
        $requiredFields = [];
        if ($languageMode === 'ar' || $languageMode === 'both') {
            $requiredFields = array_merge($requiredFields, ['title_ar', 'introduction_ar', 'body_ar', 'seo_title_ar', 'meta_description_ar']);
        }
        if ($languageMode === 'en' || $languageMode === 'both') {
            $requiredFields = array_merge($requiredFields, ['title_en', 'introduction_en', 'body_en', 'seo_title_en', 'meta_description_en']);
        }
        $requiredFields[] = 'slug';

        foreach ($requiredFields as $field) {
            if (empty($article[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        // 2. Validate URL Slug (Short, lowercase, English-only alphanumeric/dashes)
        $slug = $article['slug'] ?? '';
        if (!empty($slug)) {
            if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
                $errors[] = "Invalid URL Slug: must be short, lowercase, and in English alphanumeric/dashes only (Page 7 of PDF).";
            }
        }

        // Validate contents by language mode
        $langs = [];
        if ($languageMode === 'ar' || $languageMode === 'both') { $langs[] = 'ar'; }
        if ($languageMode === 'en' || $languageMode === 'both') { $langs[] = 'en'; }
        $h2Count = 0;
        $h3Count = 0;
        $overallWordCount = 0;
        $readabilityScores = [];
        $densityByLanguage = [];

        foreach ($langs as $lang) {
            $body = $article["body_{$lang}"] ?? '';
            $intro = $article["introduction_{$lang}"] ?? '';
            $title = $article["title_{$lang}"] ?? '';
            $seoTitle = $article["seo_title_{$lang}"] ?? '';
            $metaDesc = $article["meta_description_{$lang}"] ?? '';

            $combinedContent = $intro . "\n" . $body;

            // 3. Prevent duplicate H1 heading and validate tag order (Hard Errors)
            if (preg_match('/<h1[^>]*>/i', $body) || preg_match('/<h1[^>]*>/i', $intro)) {
                $errors[] = "Content contains an <h1> tag, which is forbidden to avoid duplicate titles (Page 2 & 8 of PDF).";
            }

            // Heading Hierarchy Check (Warning)
            preg_match_all('/<(h1|h2|h3|h4|h5|h6)[^>]*>/i', $body, $matches);
            $h2Count += count(array_filter($matches[1], fn ($tag) => strtolower($tag) === 'h2'));
            $h3Count += count(array_filter($matches[1], fn ($tag) => strtolower($tag) === 'h3'));
            $levels = array_map(fn ($tag) => (int) substr($tag, 1), $matches[1]);
            $lastLevel = 1;
            foreach ($levels as $lvl) {
                if ($lvl - $lastLevel > 1) {
                    $warnings[] = "({$lang}) Heading hierarchy order is invalid: jumped from H{$lastLevel} to H{$lvl} (Page 8 of PDF).";
                }
                $lastLevel = $lvl;
            }

            // 4. Clichés / Forbidden Phrasing Checks (Warning)
            if ($lang === 'ar') {
                foreach (['في عالم اليوم', 'لا شك أن', 'مما لا شك فيه', 'في ظل التطور التكنولوجي', 'يجدر بالذكر', 'من المهم أن تعرف'] as $phrase) {
                    if (mb_stripos($combinedContent, $phrase) !== false) {
                        $warnings[] = "Arabic content contains a generic AI phrase: '{$phrase}'.";
                    }
                }
                $clichés = ['في عالم اليوم', 'لا شك أن', 'مما لا شك فيه', 'في ظل التطور التكنولوجي', 'يجدر بالذكر', 'من المهم أن تعرف'];
                foreach ($clichés as $cliché) {
                    if (mb_strpos($combinedContent, $cliché) !== false) {
                        $warnings[] = "Arabic content contains forbidden AI cliché: '{$cliché}' (Page 2 of PDF).";
                    }
                }
            }

            // Run the same stock-phrase check with correctly encoded text for
            // both languages. This supplements legacy phrase data and keeps
            // the quality report useful for existing records.
            $qualityPhrases = $lang === 'ar'
                ? ['في عالم اليوم', 'لا شك أن', 'مما لا شك فيه', 'في ظل التطور التكنولوجي', 'يجدر بالذكر', 'من المهم أن تعرف']
                : ["in today's digital world", 'seo is a fundamental aspect', 'in this article, we will', 'in conclusion', 'cannot be overstated', 'in the ever-evolving digital landscape'];
            foreach ($qualityPhrases as $phrase) {
                if (mb_stripos($combinedContent, $phrase) !== false) {
                    $warnings[] = "({$lang}) Generic stock phrase detected: '{$phrase}'.";
                }
            }

            // Emojis Check (Warning)
            $emojiRegex = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';
            if (preg_match($emojiRegex, $combinedContent) || preg_match($emojiRegex, $title) || preg_match($emojiRegex, $seoTitle)) {
                $warnings[] = "({$lang}) Content contains Emojis, which are forbidden (Page 10 of PDF).";
            }

            // 5. Paragraph Length Validation (Warning)
            preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $body, $pMatches);
            $paragraphOutliers = [];
            foreach ($pMatches[1] as $idx => $p) {
                $pText = strip_tags($p);
                $pWords = preg_split('/[\s,.]+/', $pText, -1, PREG_SPLIT_NO_EMPTY);
                $pWordCount = count($pWords);
                if ($pWordCount > 100) {
                    $paragraphOutliers[] = ($idx + 1) . " ($pWordCount words)";
                } elseif ($pWordCount < 25) {
                    // Very short paragraphs are actionable; 25-39 words is a soft guideline.
                    $paragraphOutliers[] = ($idx + 1) . " ($pWordCount words)";
                }
            }
            if ($paragraphOutliers) {
                $warnings[] = "({$lang}) " . count($paragraphOutliers) . " paragraph(s) need restructuring: " . implode(', ', array_slice($paragraphOutliers, 0, 3)) . " (Page 3 of PDF).";
            }

            // 6. Sentence Length Validation (Warning)
            $plainText = strip_tags($combinedContent);
            $sentences = preg_split('/[.\?\!|؟]/u', $plainText, -1, PREG_SPLIT_NO_EMPTY);
            $totalWords = 0;
            $sentenceCount = 0;
            foreach ($sentences as $s) {
                $sWords = preg_split('/[\s,.]+/', trim($s), -1, PREG_SPLIT_NO_EMPTY);
                $sCount = count($sWords);
                if ($sCount === 0) continue;
                $totalWords += $sCount;
                $sentenceCount++;
                if ($sCount > 35) {
                    $warnings[] = "({$lang}) Detected an extremely long sentence ($sCount words) (Page 4 of PDF).";
                }
            }
            if ($sentenceCount > 0) {
                $avgS = $totalWords / $sentenceCount;
                $readabilityScores[] = max(0, min(100, 100 - abs($avgS - 18) * 3 - (count($pMatches[1]) > 0 ? 0 : 15)));
                $minimumSentenceWords = $lang === 'ar' ? 10 : 15;
                // Sentence range is a soft guideline. Warn only when the average is clearly poor.
                if ($avgS < 8 || $avgS > 25) {
                    $warnings[] = "({$lang}) Average sentence length (" . round($avgS, 1) . " words) is outside optimal {$minimumSentenceWords}-22 range (Page 4 of PDF).";
                }
            }
            $overallWordCount += $totalWords;

            $paragraphTexts = array_values(array_filter(array_map(
                fn ($paragraph) => mb_strtolower(trim(preg_replace('/\s+/', ' ', strip_tags($paragraph)))),
                $pMatches[1]
            )));
            if (count($paragraphTexts) !== count(array_unique($paragraphTexts))) {
                $warnings[] = "({$lang}) Content repeats the same paragraph or idea.";
            }

            // 7. SEO Meta Details Check (Warning)
            $seoLen = mb_strlen($seoTitle);
            if ($seoLen < 45 || $seoLen > 65) {
                $warnings[] = "({$lang}) SEO Title length ({$seoLen} chars) is outside optimal 50-60 range (Page 7 of PDF).";
            }
            $keywordApplies = !($lang === 'en' && preg_match('/[\x{0600}-\x{06FF}]/u', $mainKeyword));
            if ($keywordApplies && !empty($mainKeyword) && !str_contains(mb_strtolower($seoTitle), mb_strtolower($mainKeyword))) {
                $warnings[] = "({$lang}) SEO Title does not contain the primary keyword.";
            }

            $metaLen = mb_strlen($metaDesc);
            if ($metaLen < 125 || $metaLen > 175) {
                $warnings[] = "({$lang}) Meta Description length ({$metaLen} chars) is outside optimal 140-160 range (Page 7 of PDF).";
            }
            if ($keywordApplies && !empty($mainKeyword) && !str_contains(mb_strtolower($metaDesc), mb_strtolower($mainKeyword))) {
                $warnings[] = "({$lang}) Meta Description does not contain the primary keyword.";
            }

            // 8. Keyword Placement in Introduction and Conclusion
            $introWords = count(preg_split('/[\s,.]+/', strip_tags($intro), -1, PREG_SPLIT_NO_EMPTY));
            if ($introWords < 75 || $introWords > 170) {
                $warnings[] = "({$lang}) Introduction length ({$introWords} words) is outside optimal 100-150 words (Page 2 of PDF).";
            }
            if ($keywordApplies && !empty($mainKeyword) && !str_contains(mb_strtolower(strip_tags($intro)), mb_strtolower($mainKeyword))) {
                $warnings[] = "({$lang}) Primary keyword '{$mainKeyword}' was not found in the introduction paragraph.";
            }

            // Check primary keyword density in full content body (0.8% - 1.5%)
            $totalBodyWords = count(preg_split('/[\s,.]+/', $plainText, -1, PREG_SPLIT_NO_EMPTY));
            $keywordCount = 0;
            if ($keywordApplies && $totalBodyWords > 0 && !empty($mainKeyword)) {
                $keywordCount = mb_substr_count(mb_strtolower($plainText), mb_strtolower($mainKeyword));
                $density = ($keywordCount / $totalBodyWords) * 100;
            } else {
                $density = null;
            }
            $densityByLanguage[$lang] = $density === null ? null : round($density, 2);
            if ($density !== null && ($density < 0.8 || $density > 1.5)) {
                $warnings[] = "({$lang}) Primary keyword density is " . number_format($density, 2) . "%. Recommended range is 0.8% - 1.5% (Page 5 of PDF).";
            }

            // 9. Unauthorized External Links check (Hard Error)
            $allowedDomains = ['serv5.com.eg', 'api.serv5.com.eg', '127.0.0.1', 'localhost'];
            preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $body, $linkMatches);
            foreach ($linkMatches[1] as $linkUrl) {
                $host = parse_url($linkUrl, PHP_URL_HOST);
                if ($host) {
                    $isAllowed = false;
                    foreach ($allowedDomains as $allowedDomain) {
                        if ($host === $allowedDomain || str_ends_with($host, '.' . $allowedDomain)) {
                            $isAllowed = true;
                            break;
                        }
                    }
                    if (!$isAllowed) {
                        $errors[] = "Unauthorized external link detected: '{$linkUrl}' (Page 6 of PDF).";
                    }
                }
            }
        }

        // 10. Image alt tag contains primary keyword check (Warning)
        $imgAlt = $article['image']['alt'] ?? '';
        if (!empty($imgAlt) && !empty($mainKeyword)) {
            if (!str_contains(mb_strtolower($imgAlt), mb_strtolower($mainKeyword))) {
                $warnings[] = "Image Alt Text does not contain the primary keyword (Page 6 of PDF).";
            }
        }

        return [
            'keyword_density' => $densityByLanguage['ar'] ?? $densityByLanguage['en'] ?? null,
            'keyword_density_by_language' => $densityByLanguage,
            'h2_count' => $h2Count ?? 0,
            'h3_count' => $h3Count ?? 0,
            'word_count' => $overallWordCount,
            'readability_score' => round(!empty($readabilityScores) ? array_sum($readabilityScores) / count($readabilityScores) : 0),
            'warnings' => $warnings,
            'errors' => $errors,
        ];
    }

    /**
     * Sanitizes raw JSON string and decodes it safely.
     *
     * @param string $json
     * @return array
     */
    public static function cleanAndDecodeJson(string $json): array
    {
        // Models may add Markdown fences, a preamble, or raw control bytes.
        $json = trim(preg_replace('/^\xEF\xBB\xBF/', '', $json));
        $start = strpos($json, '{');
        $end = strrpos($json, '}');
        if ($start !== false && $end !== false && $end >= $start) {
            $json = substr($json, $start, $end - $start + 1);
        }

        // Scan character-by-character so escaped quotes are handled correctly.
        $clean = '';
        $inString = false;
        $escaped = false;
        for ($i = 0, $length = strlen($json); $i < $length; $i++) {
            $byte = ord($json[$i]);
            $char = $json[$i];
            if ($inString) {
                if ($escaped) {
                    $clean .= $char;
                    $escaped = false;
                    continue;
                }
                if ($char === '\\') {
                    $clean .= $char;
                    $escaped = true;
                    continue;
                }
                if ($char === '"') {
                    $clean .= $char;
                    $inString = false;
                    continue;
                }
                if ($byte < 0x20) {
                    $clean .= match ($byte) {
                        0x08 => '\\b', 0x0C => '\\f', 0x0A => '\\n',
                        0x0D => '\\r', 0x09 => '\\t',
                        default => sprintf('\\u%04x', $byte),
                    };
                    continue;
                }
            } elseif ($char === '"') {
                $inString = true;
            } elseif ($byte < 0x20) {
                $char = ' ';
            }
            $clean .= $char;
        }

        try {
            $decoded = json_decode($clean, true, 512, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\JsonException $e) {
            throw new \Exception("Response content is not valid JSON: {$e->getMessage()} (Raw length: " . strlen($json) . " bytes)", 0, $e);
        }
        if (!is_array($decoded)) {
            throw new \Exception('AI response must be a JSON object.');
        }
        return $decoded;
    }
}
