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
        $rawFeatures = $this->features ?? [];
        $price = $this->price;
        $period = $trans($this->period);
        $currency = $this->currency;

        $parseLines = function($lines) use (&$price, &$period, &$currency, &$features) {
            foreach ($lines as $line) {
                $lower = strtolower($line);
                if (str_starts_with($lower, 'price:') || str_contains($line, '$') || str_contains($lower, 'price') || str_contains($lower, 'سعر') || str_contains($lower, 'السعر')) {
                    $price = trim(str_ireplace(['price:', 'price', 'سعر', 'السعر', ':'], '', $line));
                } elseif (str_starts_with($lower, 'period:') || str_contains($lower, 'month') || str_contains($lower, 'year') || str_contains($line, '/mo') || str_contains($line, '/yr') || str_contains($line, '/') || str_contains($lower, 'شهر') || str_contains($lower, 'سنة')) {
                    $period = trim(str_ireplace(['period:', 'period', ':'], '', $line));
                } else {
                    $features[] = $line;
                }
            }
        };

        if (is_array($rawFeatures)) {
            $langFeatures = $rawFeatures[$lang] ?? $rawFeatures['en'] ?? $rawFeatures;
            if (is_string($langFeatures)) {
                $lines = collect(explode("\n", $langFeatures))
                    ->map(fn($f) => trim($f))
                    ->filter()
                    ->values();
                $parseLines($lines);
            } elseif (is_array($langFeatures)) {
                foreach ($langFeatures as $f) {
                    if (isset($f['feature'])) {
                        $features[] = $f['feature'];
                    } elseif (is_string($f)) {
                        $features[] = $f;
                    }
                }
            }
        } elseif (is_string($rawFeatures)) {
            $lines = collect(explode("\n", $rawFeatures))
                ->map(fn($f) => trim($f))
                ->filter()
                ->values();
            $parseLines($lines);
        }

        if ($price && (str_contains($price, '$') || str_contains($price, 'SAR') || str_contains($price, 'ريال') || str_contains($price, 'EGP') || str_contains($price, 'LE') || str_contains($price, 'ج.م'))) {
            $currency = '';
        }

        return [
            'id' => $this->id,
            'name' => $trans($this->name),
            'price' => $price,
            'currency' => $currency,
            'period' => $period,
            'features' => $features,
            'is_popular' => $this->is_popular,
            'button_text' => $trans($this->button_text),
            'button_url' => $this->button_url,
        ];
    }
}
