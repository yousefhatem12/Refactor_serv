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

            // Ensure unique slug generation if not provided
            if (empty($data['slug'])) {
                $title = '';
                if (isset($data['title']) && is_array($data['title'])) {
                    $title = $data['title']['en'] ?? $data['title']['ar'] ?? '';
                } else {
                    $title = $data['title'] ?? '';
                }
                
                $slug = Str::slug($title);
                if (empty($slug)) {
                    // Fallback to random post slug to prevent empty/numeric slugs
                    $slug = 'post-' . rand(1000, 9999);
                }
                
                $originalSlug = $slug;
                $count = 1;
                while (Post::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $data['slug'] = $slug;
            } else {
                $data['slug'] = Str::slug($data['slug']);
            }

            return Post::create($data);
        });
    }
}
