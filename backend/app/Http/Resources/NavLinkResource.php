<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = request('lang', request()->header('Accept-Language', 'en'));
        if (is_array($lang)) {
            $lang = reset($lang);
        }
        $lang = str_starts_with(strtolower((string)$lang), 'ar') ? 'ar' : 'en';

        $trans = function ($field, $overrideLang = null) use ($lang) {
            $targetLang = $overrideLang ?: $lang;
            if (is_string($field)) {
                $decoded = json_decode($field, true);
                if (is_array($decoded)) {
                    $field = $decoded;
                }
            }
            if (!is_array($field)) {
                return (string) $field;
            }
            return (string) ($field[$targetLang] ?? $field['en'] ?? '');
        };

        $data = [
            'name' => $trans($this->label),
            'href' => $this->url,
        ];

        $dropdown = [];
        if ($this->children && $this->children->count() > 0) {
            $dropdown = $this->children->map(function ($child) use ($trans) {
                return [
                    'name' => $trans($child->label),
                    'href' => $child->url,
                ];
            })->toArray();
        }

        // If this is the Services link, inject the dynamic services from the database
        $enName = strtolower($trans($this->label, 'en'));
        if (str_contains($enName, 'service')) {
            $services = \App\Models\Service::where('is_active', true)
                ->select(['id', 'name', 'slug'])
                ->get();
            $data['dropdown'] = $services->map(function ($service) use ($trans) {
                return [
                    'name' => $trans($service->name),
                    'href' => '/services/services-' . $service->slug,
                ];
            })->toArray();
        } elseif (!empty($dropdown)) {
            $data['dropdown'] = $dropdown;
        }

        return $data;
    }
}
