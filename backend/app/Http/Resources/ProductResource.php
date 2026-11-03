<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

        $data = [
            'id' => $this->slug,
            'name' => $trans($this->name),
            'category' => $trans($this->category),
            'title' => $trans($this->title),
            'description' => $trans($this->description),
            'image' => $this->image ? (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/') ? $this->image : asset('storage/' . $this->image)) : null,
            'imageMobile' => $this->image_mobile
                ? (str_starts_with($this->image_mobile, 'http') || str_starts_with($this->image_mobile, '/') ? $this->image_mobile : asset('storage/' . $this->image_mobile))
                : ($this->image ? (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/') ? $this->image : asset('storage/' . $this->image)) : null),
            'demoLink' => $this->demo_link ?? '#',
            'orderLink' => $this->order_link ?? '#',
            'tags' => $this->tech_specs ?? [],
        ];

        // Return detail fields if detail view is requested
        if ($request->routeIs('*.show') || $request->routeIs('*.getProduct')) {
            $features = [];
            if (is_array($this->features)) {
                $langFeatures = $this->features[$lang] ?? $this->features['en'] ?? [];
                if (is_array($langFeatures)) {
                    foreach ($langFeatures as $f) {
                        if (isset($f['title']) && isset($f['desc'])) {
                            $features[] = [
                                'title' => $f['title'],
                                'desc' => $f['desc'],
                            ];
                        }
                    }
                }
            }

            $samples = collect($this->samples ?? [])->map(function ($sample) {
                if (str_starts_with($sample, 'http') || str_starts_with($sample, '/')) {
                    return $sample;
                }
                return asset('storage/' . $sample);
            })->toArray();

            $data['features'] = $features;
            $data['techSpecs'] = $this->tech_specs ?? [];
            $data['whyChooseUs'] = $trans($this->why_choose_us);
            $data['samples'] = $samples;
        }

        return $data;
    }
}
