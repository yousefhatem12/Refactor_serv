<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    /**
     * Return a valid page number, or null when the endpoint should return the
     * unpaginated collection. Keeping this bounded prevents abusive query
     * strings from creating very large offsets or cache keys.
     */
    protected function pageNumber(Request $request, int $default = 1): int
    {
        $value = $request->query('page', $default);

        if ($value === null || $value === '' || ! is_numeric($value)) {
            return $default;
        }

        return max(1, min((int) $value, 1000));
    }

    protected function perPage(Request $request, int $default = 12, int $max = 50): int
    {
        $value = $request->query('per_page', $default);

        if (! is_numeric($value)) {
            return $default;
        }

        return max(1, min((int) $value, $max));
    }

    protected function sanitizeSlugParam(mixed $value): string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return 'all';
        }

        $clean = strtolower(trim((string) $value));
        if ($clean === '' || $clean === 'all') {
            return 'all';
        }

        return preg_match('/^[a-z0-9_-]{1,64}$/', $clean) ? $clean : 'invalid';
    }

    protected function sanitizeCountryParam(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $clean = trim($value);
        $allowed = ['Egypt', 'Saudi Arabia', 'UAE', 'Kuwait', 'Qatar', 'Bahrain', 'Oman', 'EG', 'SA', 'AE', 'KW', 'QA', 'BH', 'OM'];

        foreach ($allowed as $country) {
            if (strcasecmp($clean, $country) === 0) {
                return $country;
            }
        }

        return preg_match('/^[a-zA-Z\s]{2,32}$/', $clean) ? $clean : null;
    }

    protected function cacheVersion(string $scope): int
    {
        return \App\Traits\ClearsApiCache::cacheVersion($scope);
    }

    protected function limit(mixed $value, int $max = 50): ?int
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        return max(1, min((int) $value, $max));
    }

    protected function getLang(): string
    {
        $lang = request('lang', request()->header('Accept-Language', 'en'));
        if (is_array($lang)) {
            $lang = reset($lang);
        }
        return str_starts_with(strtolower((string)$lang), 'ar') ? 'ar' : 'en';
    }

    protected function trans(mixed $field, string $lang): string
    {
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
    }

    protected function translateCategory(?string $category, string $lang): string
    {
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
            
            // Other possible categories
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
    }

    protected function formatDate(mixed $date, string $lang): string
    {
        if (!$date) return '';
        if ($lang === 'ar') {
            $months = [
                'Jan' => 'يناير', 'Feb' => 'فبراير', 'Mar' => 'مارس', 'Apr' => 'أبريل',
                'May' => 'مايو', 'Jun' => 'يونيو', 'Jul' => 'يوليو', 'Aug' => 'أغسطس',
                'Sep' => 'سبتمبر', 'Oct' => 'أكتوبر', 'Nov' => 'نوفمبر', 'Dec' => 'ديسمبر'
            ];
            $formatted = $date->format('d M Y'); // e.g. "21 May 2026"
            foreach ($months as $en => $ar) {
                $formatted = str_replace($en, $ar, $formatted);
            }
            return $formatted;
        }
        return $date->format('M d, Y');
    }
}
