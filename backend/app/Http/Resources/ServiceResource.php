<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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

        // If it's a detail request (show/getService endpoint)
        if ($request->routeIs('*.show') || $request->routeIs('*.getService')) {
            $sections = $this->sections ?: [];
            
            // blocks array from Page Builder - translate all text fields
            $blocks = null;
            if (!empty($sections['blocks']) && is_array($sections['blocks'])) {
                // Recursively translate any {en, ar} objects to strings
                $translateDeep = function ($value) use (&$translateDeep, $trans) {
                    if (is_array($value)) {
                        // If it looks like a translation object {en:..., ar:...}
                        // Use array_key_exists (not isset) to handle null values!
                        if (array_key_exists('en', $value) || array_key_exists('ar', $value)) {
                            return $trans($value);
                        }
                        // Otherwise recurse into array
                        return array_map($translateDeep, $value);
                    }
                    return $value;
                };

                $blocks = collect($sections['blocks'])->map(function ($block) use ($translateDeep) {
                    $data = $block['data'] ?? [];
                    // Translate all fields in data recursively
                    $translatedData = array_map($translateDeep, $data);
                    return [
                        'type' => $block['type'],
                        'data' => $translatedData,
                    ];
                })->toArray();
            }

            // flat keys for backward compat
            $featuresList = collect($sections['features_list'] ?? [])->map(function($item) use ($trans) {
                return [
                    'title' => $trans($item['title'] ?? null),
                    'desc' => $trans($item['desc'] ?? null),
                ];
            })->toArray();

            $howWeWorkList = collect($sections['how_we_work_list'] ?? [])->map(function($item) use ($trans) {
                return [
                    'title' => $trans($item['title'] ?? null),
                    'desc' => $trans($item['desc'] ?? null),
                ];
            })->toArray();

            $hostingOffersList = collect($sections['hosting_offers_list'] ?? [])->map(function($item) use ($trans) {
                return [
                    'title' => $trans($item['title'] ?? null),
                    'desc' => $trans($item['desc'] ?? null),
                ];
            })->toArray();

            $digitalMarketingList = collect($sections['digital_marketing_list'] ?? [])->map(function($item) use ($trans) {
                return [
                    'title' => $trans($item['title'] ?? null),
                    'desc' => $trans($item['desc'] ?? null),
                ];
            })->toArray();

            $whyChooseUsList = collect($sections['why_choose_us_list'] ?? [])->map(function($item) use ($trans) {
                return [
                    'title' => $trans($item['title'] ?? null),
                    'desc' => $trans($item['desc'] ?? null),
                ];
            })->toArray();

            $templateSlug = $sections['template'] ?? null;
            $heroStyle = 'social_media_hero';
            $defaultHeroImage = null;
            if ($templateSlug) {
                $tmpl = \App\Models\ServiceTemplate::where('slug', $templateSlug)->first();
                if ($tmpl) {
                    $heroStyle = $tmpl->hero_style;
                    $defaultHeroImage = $tmpl->default_hero_image;
                }
            }

            return [
                'id' => $this->slug,
                'title' => $trans($this->name),
                'description' => $trans($this->description),
                'icon' => $this->icon,
                'template' => $templateSlug,
                'hero_style' => $heroStyle,
                'default_hero_image' => $defaultHeroImage,
                'blocks' => $blocks,
                'hero_title' => $trans($sections['hero_title'] ?? null),
                'hero_subtitle' => $trans($sections['hero_subtitle'] ?? null),
                'features_title' => $trans($sections['features_title'] ?? null),
                'features_subtitle' => $trans($sections['features_subtitle'] ?? null),
                'features_list' => $featuresList,
                'how_we_work_list' => $howWeWorkList,
                'hosting_offers_list' => $hostingOffersList,
                'digital_marketing_title' => $trans($sections['digital_marketing_title'] ?? null),
                'digital_marketing_subtitle' => $trans($sections['digital_marketing_subtitle'] ?? null),
                'digital_marketing_list' => $digitalMarketingList,
                'why_choose_us_title' => $trans($sections['why_choose_us_title'] ?? null),
                'why_choose_us_subtitle' => $trans($sections['why_choose_us_subtitle'] ?? null),
                'why_choose_us_list' => $whyChooseUsList,
                'show_hero' => $sections['show_hero'] ?? true,
                'show_features' => $sections['show_features'] ?? true,
                'show_how_we_work' => $sections['show_how_we_work'] ?? true,
                'show_portfolio' => $sections['show_portfolio'] ?? true,
                'show_request' => $sections['show_request'] ?? true,
                'show_blogs' => $sections['show_blogs'] ?? true,
                'show_hosting_offers' => $sections['show_hosting_offers'] ?? true,
                'show_digital_marketing' => $sections['show_digital_marketing'] ?? true,
                'show_why_choose_us' => $sections['show_why_choose_us'] ?? true,
            ];
        }

        // List representation
        $img = $this->icon ?: '/Services/Purple Planet.webp';
        
        $style = ['from' => '#A855F7', 'to' => '#6366F1', 'dot' => '#A855F7', 'shadow' => 'shadow-[0_0_20px_#A855F7]']; // Default
        
        $imgLower = strtolower($img);
        if (str_contains($imgLower, 'yellow')) {
            $style = ['from' => '#F97316', 'to' => '#EAB308', 'dot' => '#EAB308', 'shadow' => 'shadow-[0_0_20px_#EAB308]'];
        } elseif (str_contains($imgLower, 'green')) {
            $style = ['from' => '#10B981', 'to' => '#059669', 'dot' => '#10B981', 'shadow' => 'shadow-[0_0_20px_#10B981]'];
        } elseif (str_contains($imgLower, 'blue')) {
            $style = ['from' => '#3B82F6', 'to' => '#2563EB', 'dot' => '#3B82F6', 'shadow' => 'shadow-[0_0_20px_#3B82F6]'];
        } elseif (str_contains($imgLower, 'purple')) {
            $style = ['from' => '#A855F7', 'to' => '#6366F1', 'dot' => '#A855F7', 'shadow' => 'shadow-[0_0_20px_#A855F7]'];
        }

        $name = $trans($this->name);

        return [
            'id' => $this->slug,
            'title' => $lang === 'en' ? strtolower($name) : $name,
            'label' => $name,
            'description' => $trans($this->description),
            'img' => $img,
            'gradient' => "from-[{$style['from']}] to-[{$style['to']}]",
            'dotColor' => "bg-[{$style['dot']}]",
            'shadow' => $style['shadow'],
        ];
    }
}
