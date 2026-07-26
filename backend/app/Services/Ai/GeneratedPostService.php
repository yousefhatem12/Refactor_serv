<?php

namespace App\Services\Ai;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeneratedPostService
{
    /**
     * Creates an AI generated blog post, forcing it to be an unpublished draft.
     *
     * @param array $data
     * @return Post
     */
    public static function createDraft(array $data): Post
    {
        return DB::transaction(function () use ($data) {
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
