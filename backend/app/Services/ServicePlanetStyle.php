<?php

namespace App\Services;

final class ServicePlanetStyle
{
    private const DEFAULT_PLANET = 'planet4.webp';

    private const STYLES = [
        'blue.webp' => ['color' => '#245183', 'from' => '#3b82f6', 'to' => '#1e3a8a'],
        'green.webp' => ['color' => '#285446', 'from' => '#22c55e', 'to' => '#14532d'],
        'yellow.webp' => ['color' => '#A5661E', 'from' => '#eab308', 'to' => '#78350f'],
        'planet1.webp' => ['color' => '#84694F', 'from' => '#d4a373', 'to' => '#5c4033'],
        'planet2.webp' => ['color' => '#602E23', 'from' => '#ef4444', 'to' => '#450a0a'],
        'planet3.webp' => ['color' => '#1F3B4C', 'from' => '#06b6d4', 'to' => '#083344'],
        'planet4.webp' => ['color' => '#673E87', 'from' => '#a855f7', 'to' => '#3b0764'],
        'planet5.webp' => ['color' => '#783B55', 'from' => '#ec4899', 'to' => '#500724'],
        'planet6.webp' => ['color' => '#CBB8CC', 'from' => '#e9d5ff', 'to' => '#7e22ce'],
        'planet7.webp' => ['color' => '#593480', 'from' => '#8b5cf6', 'to' => '#2e1065'],
        'planet8.webp' => ['color' => '#26537B', 'from' => '#60a5fa', 'to' => '#172554'],
        'outline 6.webp' => ['color' => '#26262C', 'from' => '#9ca3af', 'to' => '#111827'],
    ];

    private const LEGACY_ALIASES = [
        'blue planet.webp' => 'blue.webp',
        'purple planet.webp' => 'planet4.webp',
    ];

    public static function forImage(?string $image): array
    {
        $path = parse_url((string) $image, PHP_URL_PATH) ?: (string) $image;
        $filename = strtolower(rawurldecode(basename(str_replace('\\', '/', $path))));
        $filename = self::LEGACY_ALIASES[$filename] ?? $filename;
        $style = self::STYLES[$filename] ?? self::STYLES[self::DEFAULT_PLANET];

        return [
            'dotColor' => $style['color'],
            'gradient' => "linear-gradient(135deg, {$style['from']}, {$style['to']})",
            'shadow' => "shadow-[0_0_20px_{$style['color']}]",
        ];
    }
}
