<?php

namespace App\Services\Ai;

class GenerationRequestNormalizer
{
    /**
     * Normalizes and cleans the generation parameters.
     *
     * @param array $params
     * @return array
     */
    public static function normalize(array $params): array
    {
        $normalized = $params;

        // 1. Clean and normalize generation mode and basic parameters
        if (isset($normalized['generation_mode'])) {
            $normalized['generation_mode'] = trim(strtolower($normalized['generation_mode']));
        }

        if (isset($normalized['provider'])) {
            $normalized['provider'] = trim(strtolower($normalized['provider']));
        }

        if (isset($normalized['model'])) {
            $normalized['model'] = trim(strtolower($normalized['model']));
        }

        if (isset($normalized['language'])) {
            $normalized['language'] = trim(strtolower($normalized['language']));
        }

        // 2. Normalize keywords depending on mode
        if (($normalized['generation_mode'] ?? '') === 'one_per_keyword') {
            // Sort keywords list when order is not meaningful to ensure deterministic hashing
            if (isset($normalized['keywords']) && is_array($normalized['keywords'])) {
                $normalized['keywords'] = self::cleanAndSortKeywords($normalized['keywords']);
            }
        } elseif (($normalized['generation_mode'] ?? '') === 'topic_cluster') {
            // For topic clusters, main topic order is meaningful, but we still trim/clean suggestions list
            if (isset($normalized['main_topic'])) {
                $normalized['main_topic'] = trim($normalized['main_topic']);
            }
            if (isset($normalized['ideas']) && is_array($normalized['ideas'])) {
                $normalized['ideas'] = self::cleanAndSortKeywords($normalized['ideas']);
            }
        }

        // Clean main_keyword & secondary_keywords at the root level if present
        if (isset($normalized['main_keyword'])) {
            $normalized['main_keyword'] = trim(strtolower($normalized['main_keyword']));
        }

        if (isset($normalized['secondary_keywords'])) {
            if (is_array($normalized['secondary_keywords'])) {
                $normalized['secondary_keywords'] = self::cleanStringArray($normalized['secondary_keywords']);
                sort($normalized['secondary_keywords']); // Order is not meaningful for secondary keywords list
            } elseif (is_string($normalized['secondary_keywords'])) {
                $normalized['secondary_keywords'] = trim(strtolower($normalized['secondary_keywords']));
            }
        }

        // 3. Clean settings snapshot
        if (isset($normalized['settings_snapshot']) && is_array($normalized['settings_snapshot'])) {
            // Remove sensitive fields
            unset(
                $normalized['settings_snapshot']['api_token'],
                $normalized['settings_snapshot']['new_api_token'],
                $normalized['settings_snapshot']['api_key']
            );
            $normalized['settings_snapshot'] = self::recursiveCleanAndSort($normalized['settings_snapshot']);
        }

        // 4. Sort associative array keys recursively
        return self::recursiveCleanAndSort($normalized);
    }

    /**
     * Produces a SHA-256 hash from normalized params, excluding idempotency key and timestamps.
     *
     * @param array $params
     * @return string
     */
    public static function calculateHash(array $params): string
    {
        $normalized = self::normalize($params);

        // Exclude transient/idempotency fields from request hash
        unset(
            $normalized['idempotency_key'],
            $normalized['created_at'],
            $normalized['updated_at'],
            $normalized['started_at'],
            $normalized['completed_at']
        );

        return hash('sha256', json_encode($normalized));
    }

    private static function cleanAndSortKeywords(array $keywordsList): array
    {
        $cleaned = [];
        foreach ($keywordsList as $item) {
            if (is_array($item)) {
                $main = isset($item['main_keyword']) ? trim(strtolower($item['main_keyword'])) : '';
                if ($main === '') {
                    continue;
                }
                
                $secondaries = isset($item['secondary_keywords']) && is_array($item['secondary_keywords'])
                    ? self::cleanStringArray($item['secondary_keywords'])
                    : [];
                sort($secondaries); // Sort secondary keywords list

                $cleaned[] = [
                    'main_keyword' => $main,
                    'secondary_keywords' => $secondaries
                ];
            } elseif (is_string($item)) {
                $trimmed = trim(strtolower($item));
                if ($trimmed !== '') {
                    $cleaned[] = [
                        'main_keyword' => $trimmed,
                        'secondary_keywords' => []
                    ];
                }
            }
        }

        // Deduplicate list by main_keyword
        $unique = [];
        foreach ($cleaned as $item) {
            $unique[$item['main_keyword']] = $item;
        }

        // Sort by main_keyword alphabetically to make it deterministic
        ksort($unique);

        return array_values($unique);
    }

    private static function cleanStringArray(array $arr): array
    {
        $result = [];
        foreach ($arr as $str) {
            if (is_string($str)) {
                $trimmed = trim(strtolower($str));
                if ($trimmed !== '') {
                    $result[] = $trimmed;
                }
            }
        }
        return array_values(array_unique($result));
    }

    private static function recursiveCleanAndSort(array $array): array
    {
        ksort($array);
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = self::recursiveCleanAndSort($value);
            } elseif (is_string($value)) {
                $value = trim($value);
            }
        }
        return $array;
    }
}
