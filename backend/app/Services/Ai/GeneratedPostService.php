<?php

namespace App\Services\Ai;

use App\Models\AiGenerationRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeneratedPostService
{
    /**
     * Publishes a validated generated post only when the originating request
     * explicitly enabled auto publishing. Topic clusters must wait until all
     * cross-links have been finalized before any post becomes public.
     */
    public static function autoPublishIfEnabled(
        Post $post,
        AiGenerationRequest $request,
        bool $clusterFinalized = false
    ): bool {
        $enabled = (bool) data_get($request->settings_snapshot, 'auto_publish', false);
        if (!$enabled || ($request->generation_mode === 'topic_cluster' && !$clusterFinalized)) {
            return false;
        }

        $post->update([
            'is_published' => true,
            'ai_status' => 'approved',
        ]);

        return true;
    }

    /**
     * Creates an AI generated blog post, forcing it to be an unpublished draft.
     *
     * @param array $data
     * @return Post
     */
    public static function createDraft(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            foreach (['title', 'content', 'seo_title', 'seo_description'] as $field) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    $data[$field] = ArticleContentService::sanitizeGeneratedArticle($data[$field]);
                }
            }

            // AI HTML is untrusted even when the provider returned valid JSON.
            // Sanitize at the persistence boundary as a final line of defense.
            if (isset($data['content']) && is_array($data['content'])) {
                foreach (['ar', 'en'] as $language) {
                    if (isset($data['content'][$language]) && is_string($data['content'][$language])) {
                        $data['content'][$language] = GeneratedHtmlSanitizer::sanitize($data['content'][$language]);
                    }
                }
            }

            // Force AI tracking and draft state, overriding any incoming values
            $data['is_published'] = false;
            $data['ai_generated'] = true;
            $data['ai_status'] = 'pending_review';

            // Always normalize and uniquify the slug. The AI may return a
            // perfectly valid slug that already belongs to another post.
            $slug = !empty($data['slug']) ? Str::slug((string) $data['slug']) : '';
            if ($slug === '') {
                $title = '';
                if (isset($data['title']) && is_array($data['title'])) {
                    $title = $data['title']['en'] ?? $data['title']['ar'] ?? '';
                } else {
                    $title = $data['title'] ?? '';
                }

                $slug = Str::slug((string) $title);
            }

            if ($slug === '') {
                // Fallback to a random post slug to prevent an empty slug.
                $slug = 'post-' . random_int(1000, 9999);
            }

            $originalSlug = $slug;
            $count = 2;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;

            return Post::create($data);
        });
    }
}
