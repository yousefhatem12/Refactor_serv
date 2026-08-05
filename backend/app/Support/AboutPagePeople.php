<?php

namespace App\Support;

class AboutPagePeople
{
    private const LANGUAGES = ['en', 'ar'];

    public static function extract(array $sections): array
    {
        return [
            'founders' => self::mergeLocalizedPeople(
                self::collection($sections, 'en', 'team', 'items'),
                self::collection($sections, 'ar', 'team', 'items'),
                ['image'],
            ),
            'team' => self::mergeLocalizedPeople(
                self::collection($sections, 'en', 'team_work', 'members'),
                self::collection($sections, 'ar', 'team_work', 'members'),
                ['dept', 'image', 'linkedin', 'github', 'twitter'],
            ),
        ];
    }

    public static function sync(array $sections, array $people): array
    {
        foreach (self::LANGUAGES as $language) {
            $sections[$language] = self::syncCollection(
                $sections[$language] ?? [],
                'team',
                'items',
                $people['founders'] ?? [],
                $language,
                ['image'],
            );

            $sections[$language] = self::syncCollection(
                $sections[$language],
                'team_work',
                'members',
                $people['team'] ?? [],
                $language,
                ['dept', 'image', 'linkedin', 'github', 'twitter'],
            );
        }

        return $sections;
    }

    private static function collection(
        array $sections,
        string $language,
        string $blockType,
        string $collection,
    ): array {
        foreach ($sections[$language] ?? [] as $block) {
            if (($block['type'] ?? null) === $blockType) {
                return array_values($block['data'][$collection] ?? []);
            }
        }

        return [];
    }

    private static function mergeLocalizedPeople(
        array $englishPeople,
        array $arabicPeople,
        array $sharedFields,
    ): array {
        $people = [];
        $count = max(count($englishPeople), count($arabicPeople));

        for ($index = 0; $index < $count; $index++) {
            $english = $englishPeople[$index] ?? [];
            $arabic = $arabicPeople[$index] ?? [];

            $person = [
                'name_en' => $english['name'] ?? '',
                'name_ar' => $arabic['name'] ?? '',
                'role_en' => $english['role'] ?? '',
                'role_ar' => $arabic['role'] ?? '',
            ];

            foreach ($sharedFields as $field) {
                $person[$field] = self::sharedValue($english, $arabic, $field);
            }

            $people[] = $person;
        }

        return $people;
    }

    private static function sharedValue(array $english, array $arabic, string $field): mixed
    {
        $englishValue = $english[$field] ?? null;

        return $englishValue !== null && $englishValue !== ''
            ? $englishValue
            : ($arabic[$field] ?? null);
    }

    private static function syncCollection(
        array $blocks,
        string $blockType,
        string $collection,
        array $people,
        string $language,
        array $sharedFields,
    ): array {
        foreach ($blocks as &$block) {
            if (($block['type'] ?? null) !== $blockType) {
                continue;
            }

            $existingPeople = array_values($block['data'][$collection] ?? []);
            $localizedPeople = [];

            foreach (array_values($people) as $index => $person) {
                $localizedPerson = $existingPeople[$index] ?? [];
                $localizedPerson['name'] = $person["name_{$language}"] ?? '';
                $localizedPerson['role'] = $person["role_{$language}"] ?? '';

                foreach ($sharedFields as $field) {
                    $localizedPerson[$field] = $person[$field] ?? null;
                }

                $localizedPeople[] = $localizedPerson;
            }

            $block['data'][$collection] = $localizedPeople;
        }
        unset($block);

        return $blocks;
    }
}
