<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = request('lang', request()->header('Accept-Language', 'en'));
        if (is_array($lang)) {
            $lang = reset($lang);
        }
        $lang = str_starts_with(strtolower((string)$lang), 'ar') ? 'ar' : 'en';

        $trans = function ($field) use ($lang) {
            if (is_string($field)) {
                $decoded = json_decode($field, true);
                if (is_array($decoded)) {
                    $field = $decoded;
                }
            }
            if (!is_array($field)) {
                return (string) $field;
            }
            return (string) ($field[$lang] ?? $field['en'] ?? '');
        };

        $resource = $this->resource;
        $getProp = function ($key) use ($resource) {
            if (is_array($resource)) {
                return $resource[$key] ?? null;
            }
            if (is_object($resource)) {
                return $resource->{$key} ?? null;
            }
            return null;
        };

        $slug = $getProp('slug');
        $title = $getProp('title');
        $subtitle = $getProp('subtitle');
        $heroImage = $getProp('hero_image');
        $content = $getProp('content');
        $seoTitle = $getProp('seo_title');
        $seoDescription = $getProp('seo_description');
        $sections = $getProp('sections');

        return [
            'slug' => $slug,
            'title' => $trans($title),
            'subtitle' => $trans($subtitle),
            'hero_image' => $heroImage ? asset('storage/' . $heroImage) : null,
            'content' => $trans($content),
            'seo' => [
                'title' => $trans($seoTitle) ?: $trans($title),
                'description' => $trans($seoDescription) ?: $trans($subtitle),
            ],
            'sections' => [
                'template' => $sections['template'] ?? $sections[$lang]['template'] ?? null,
                // Legacy/API pages store localized block arrays while the
                // Filament custom builder stores one bilingual blocks array.
                'blocks' => $sections['blocks'] ?? $sections[$lang]['blocks'] ?? $sections[$lang] ?? []
            ],
        ];
    }
}
