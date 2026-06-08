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

        return [
            'slug' => $this->slug,
            'title' => $trans($this->title),
            'subtitle' => $trans($this->subtitle),
            'hero_image' => $this->hero_image ? asset('storage/' . $this->hero_image) : null,
            'content' => $trans($this->content),
            'seo' => [
                'title' => $trans($this->seo_title) ?: $trans($this->title),
                'description' => $trans($this->seo_description) ?: $trans($this->subtitle),
            ],
            'sections' => [
                'template' => $this->sections['template'] ?? null,
                'blocks' => $this->sections[$lang] ?? []
            ],
        ];
    }
}
