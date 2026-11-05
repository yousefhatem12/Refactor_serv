<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\LocalizationHelper;
use App\Traits\ClearsApiCache;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    /**
     * Return a bounded page number (1..1000).
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

        $decoded = rawurldecode((string) $value);
        $clean = mb_strtolower(trim($decoded), 'UTF-8');
        if ($clean === '' || $clean === 'all') {
            return 'all';
        }

        return preg_match('/^[\p{L}\p{N}_-]{1,191}$/u', $clean) ? $clean : 'invalid';
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
        return ClearsApiCache::cacheVersion($scope);
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
        return LocalizationHelper::resolveLang();
    }

    protected function trans(mixed $field, string $lang): string
    {
        return LocalizationHelper::trans($field, $lang);
    }

    protected function translateCategory(?string $category, string $lang): string
    {
        return LocalizationHelper::translateCategory($category, $lang);
    }

    protected function formatDate(mixed $date, string $lang): string
    {
        return LocalizationHelper::formatDate($date, $lang);
    }
}
