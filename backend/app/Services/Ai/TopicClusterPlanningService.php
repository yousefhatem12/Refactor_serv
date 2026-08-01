<?php

namespace App\Services\Ai;

class TopicClusterPlanningService
{
    public static function buildPrompt(
        string $mainTopic,
        array $secondaryKeywords,
        string $language,
        string $arabicDialect,
        int $subtopicCount,
        bool $suggestKeywords
    ): string {
        $dialect = ArabicDialectCatalog::instruction($arabicDialect);
        $keywordRule = $suggestKeywords
            ? 'You may discover closely related search phrases when they improve coverage and search intent.'
            : 'Use only the supplied topic and secondary keywords as your source material; do not introduce unrelated keyword ideas.';
        $secondaryKeywordsText = implode(', ', $secondaryKeywords);

        return <<<PROMPT
You are an SEO content strategist planning a topic cluster for Serv5.

Main pillar topic: {$mainTopic}
Secondary keywords: {$secondaryKeywordsText}
Requested language mode: {$language}
Arabic style when Arabic is used: {$dialect}
Required supporting articles: exactly {$subtopicCount}

{$keywordRule}

Return exactly one JSON object with this schema:
{
  "subtopics": [
    {"keyword": "a specific search-focused supporting article topic", "search_intent": "informational|commercial|transactional", "angle": "one sentence explaining its unique scope"}
  ]
}

HARD RULES:
- Return exactly {$subtopicCount} subtopics, no more and no fewer.
- Every keyword must be a natural article title/topic, never a placeholder such as "Subtopic 1".
- Each subtopic must target a distinct search intent or decision stage and must support the pillar topic without duplicating it.
- Keep every keyword between 3 and 14 words and every angle under 30 words.
- Do not repeat the pillar topic, another subtopic, or the same idea with slightly different wording.
- Keep the subtopic language consistent with the main topic. Use {$dialect} for Arabic wording.
- Do not include URLs, Markdown, commentary, or fields outside the schema.
PROMPT;
    }

    public static function validatePlan(array $response, string $mainTopic, int $expectedCount): array
    {
        $rawSubtopics = $response['subtopics'] ?? null;
        if (!is_array($rawSubtopics)) {
            throw new \RuntimeException('Topic cluster planner did not return a subtopics array.');
        }

        $seen = [self::normalizeForComparison($mainTopic) => true];
        $plan = [];

        foreach ($rawSubtopics as $rawSubtopic) {
            if (!is_array($rawSubtopic)) {
                continue;
            }

            $keyword = trim((string) ($rawSubtopic['keyword'] ?? ''));
            $normalized = self::normalizeForComparison($keyword);
            if ($keyword === '' || isset($seen[$normalized])) {
                continue;
            }

            $wordCount = count(preg_split('/\s+/u', $keyword, -1, PREG_SPLIT_NO_EMPTY));
            if ($wordCount < 3 || $wordCount > 14 || preg_match('/\bsub[ -]?topic\s*\d*\b/i', $keyword)) {
                continue;
            }

            $intent = strtolower(trim((string) ($rawSubtopic['search_intent'] ?? 'informational')));
            if (!in_array($intent, ['informational', 'commercial', 'transactional'], true)) {
                $intent = 'informational';
            }

            $seen[$normalized] = true;
            $plan[] = [
                'keyword' => $keyword,
                'search_intent' => $intent,
                'angle' => trim((string) ($rawSubtopic['angle'] ?? '')),
            ];
        }

        if (count($plan) !== $expectedCount) {
            throw new \RuntimeException("Topic cluster planner returned " . count($plan) . " valid unique subtopics; exactly {$expectedCount} are required.");
        }

        return $plan;
    }

    private static function normalizeForComparison(string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value) ?? $value), 'UTF-8');
    }
}
