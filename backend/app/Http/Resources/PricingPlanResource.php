<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingPlanResource extends JsonResource
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

        $features = [];
        if (is_array($this->features)) {
            $langFeatures = $this->features[$lang] ?? $this->features['en'] ?? [];
            if (is_array($langFeatures)) {
                foreach ($langFeatures as $f) {
                    if (isset($f['feature'])) {
                        $features[] = $f['feature'];
                    } elseif (is_string($f)) {
                        $features[] = $f;
                    }
                }
            }
        }

        return [
            'id' => $this->id,
            'name' => $trans($this->name),
            'price' => $this->price,
            'currency' => $this->currency,
            'period' => $trans($this->period),
            'features' => $features,
            'is_popular' => $this->is_popular,
            'button_text' => $trans($this->button_text),
            'button_url' => $this->button_url,
        ];
    }
}
