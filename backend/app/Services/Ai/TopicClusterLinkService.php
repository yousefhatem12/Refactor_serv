<?php

namespace App\Services\Ai;

use App\Models\AiGenerationItem;
use Illuminate\Support\Collection;

class TopicClusterLinkService
{
    /**
     * @param Collection<int, AiGenerationItem> $items
     */
    public static function apply(array $article, AiGenerationItem $current, Collection $items, string $languageMode, string $pillarKeyword): array
    {
        foreach (['ar', 'en'] as $lang) {
            if (!(($languageMode === 'both') || $languageMode === $lang) || empty($article["body_{$lang}"])) {
                continue;
            }

            $others = $items
                ->filter(fn (AiGenerationItem $item): bool => $item->id !== $current->id && $item->post !== null)
                ->sortByDesc(fn (AiGenerationItem $item): int => $item->keyword === $pillarKeyword ? 1 : 0)
                ->take(4)
                ->values();

            if ($others->isEmpty()) {
                continue;
            }

            $suggestions = [];
            foreach ($others as $item) {
                $title = is_array($item->post->title) ? trim((string) ($item->post->title[$lang] ?? '')) : '';
                $label = $title !== '' ? $title : $item->keyword;
                $url = SiteUrlBuilder::blogPost($lang, $item->post->slug);
                $suggestions[] = ['label' => $label, 'url' => $url, 'type' => 'cluster_post'];
            }

            $article["body_{$lang}"] = InternalLinkService::distributeUniqueLinks(
                (string) $article["body_{$lang}"],
                $suggestions,
                $lang
            );
            $article['internal_link_suggestions'] = array_values(array_unique(array_merge(
                $article['internal_link_suggestions'] ?? [],
                $suggestions
            ), SORT_REGULAR));
        }

        return ArticleContentService::sanitizeHtmlFields($article);
    }
}
