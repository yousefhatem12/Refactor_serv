<?php

namespace App\Http\Resources;

use App\Services\PostContentFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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

        $translatedContent = $this->content;
        if (is_string($translatedContent)) {
            $decoded = json_decode($translatedContent, true);
            if (is_array($decoded)) {
                $translatedContent = $decoded;
            }
        }
        if (is_array($translatedContent) && ($translatedContent['type'] ?? null) !== 'doc') {
            $translatedContent = $translatedContent[$lang] ?? $translatedContent['en'] ?? '';
        }
        $formattedContent = PostContentFormatter::format($translatedContent);

        $translateCategory = function ($category) use ($lang) {
            if (!$category) return '';
            $map = [
                'design' => ['en' => 'Design', 'ar' => 'تصميم'],
                'التصميم' => ['en' => 'Design', 'ar' => 'تصميم'],
                'hosting' => ['en' => 'Hosting', 'ar' => 'استضافة'],
                'الاستضافة' => ['en' => 'Hosting', 'ar' => 'استضافة'],
                'marketing' => ['en' => 'Marketing', 'ar' => 'تسويق'],
                'التسويق' => ['en' => 'Marketing', 'ar' => 'تسويق'],
                'تسويق' => ['en' => 'Marketing', 'ar' => 'تسويق'],
                'development' => ['en' => 'Development', 'ar' => 'تطوير'],
                'التطوير' => ['en' => 'Development', 'ar' => 'تطوير'],
                'security' => ['en' => 'Security', 'ar' => 'حماية'],
                'الحماية' => ['en' => 'Security', 'ar' => 'حماية'],
                'artificial intelligence' => ['en' => 'Artificial Intelligence', 'ar' => 'الذكاء الاصطناعي'],
                'الذكاء الاصطناعي' => ['en' => 'Artificial Intelligence', 'ar' => 'الذكاء الاصطناعي'],
                'software' => ['en' => 'Software', 'ar' => 'برمجيات'],
                'برمجيات' => ['en' => 'Software', 'ar' => 'برمجيات'],
                'startup' => ['en' => 'Startup', 'ar' => 'شركات ناشئة'],
                'شركات ناشئة' => ['en' => 'Startup', 'ar' => 'شركات ناشئة'],
                'technology' => ['en' => 'Technology', 'ar' => 'تكنولوجيا'],
                'تكنولوجيا' => ['en' => 'Technology', 'ar' => 'تكنولوجيا'],
                'web development' => ['en' => 'Web Development', 'ar' => 'تطوير الويب'],
                'تطوير الويب' => ['en' => 'Web Development', 'ar' => 'تطوير الويب'],
                'drop shipping' => ['en' => 'Drop Shipping', 'ar' => 'دروب شيبنج'],
                'دروب شيبنج' => ['en' => 'Drop Shipping', 'ar' => 'دروب شيبنج'],
                'hardware' => ['en' => 'Hardware', 'ar' => 'عتاد هاردوير'],
                'عتاد هاردوير' => ['en' => 'Hardware', 'ar' => 'عتاد هاردوير'],
            ];

            $key = strtolower(trim($category));
            if (isset($map[$key])) {
                return $map[$key][$lang] ?? $category;
            }
            return $category;
        };

        $formatDate = function ($date) use ($lang) {
            if (!$date) return '';
            if ($lang === 'ar') {
                $months = [
                    'Jan' => 'يناير', 'Feb' => 'فبراير', 'Mar' => 'مارس', 'Apr' => 'أبريل',
                    'May' => 'مايو', 'Jun' => 'يونيو', 'Jul' => 'يوليو', 'Aug' => 'أغسطس',
                    'Sep' => 'سبتمبر', 'Oct' => 'أكتوبر', 'Nov' => 'نوفمبر', 'Dec' => 'ديسمبر'
                ];
                $formatted = $date->format('d M Y');
                foreach ($months as $en => $ar) {
                    $formatted = str_replace($en, $ar, $formatted);
                }
                return $formatted;
            }
            return $date->format('M d, Y');
        };

        $categoryName = '';
        $categoryRaw = '';
        if ($this->blogCategory) {
            $categoryName = $trans($this->blogCategory->name);
            $categoryRaw = strtoupper(trim($trans($this->blogCategory->name, 'en')));
        } else if ($this->category) {
            $categoryName = $translateCategory($this->category);
            $categoryRaw = strtoupper(trim($this->category));
        }

        $data = [
            'id' => $this->id,
            'title' => $trans($this->title),
            'slug' => $this->slug,
            'content' => $formattedContent['content'],
            'table' => $formattedContent['table'],
            'list' => $formattedContent['list'],
            'author' => $this->author?->name ?? 'Admin',
            'category' => $categoryName,
            'category_raw' => $categoryRaw,
            'image' => $this->image ? (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/') ? $this->image : asset('storage/' . $this->image)) : null,
            'published_at' => $formatDate($this->created_at),
            'created_at_iso' => $this->created_at?->toIso8601String(),
            'updated_at_iso' => $this->updated_at?->toIso8601String(),
        ];

        // Return SEO fields if detail view is requested
        if ($request->routeIs('*.show') || $request->routeIs('*.getPost')) {
            $data['seo'] = [
                'title' => $trans($this->seo_title) ?: $trans($this->title),
                'description' => $trans($this->seo_description),
            ];
        }

        return $data;
    }
}
