<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'id' => $this->slug,
            'slug' => $this->slug,
            'name' => $trans($this->name),
            'category' => $this->category,
            'description' => $trans($this->description),
            'image' => $this->image ? (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/') ? $this->image : asset('storage/' . $this->image)) : null,
            'client' => $this->client,
            'url' => $this->url,
            'tags' => $this->tags ?? [],
        ];
    }
}
