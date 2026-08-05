<?php

namespace Tests\Unit;

use App\Services\ServicePlanetStyle;
use PHPUnit\Framework\TestCase;

class ServicePlanetStyleTest extends TestCase
{
    public function test_every_planet_filename_has_the_expected_color_and_gradient(): void
    {
        $cases = [
            'blue.webp' => ['#245183', 'linear-gradient(135deg, #3b82f6, #1e3a8a)'],
            'green.webp' => ['#285446', 'linear-gradient(135deg, #22c55e, #14532d)'],
            'yellow.webp' => ['#A5661E', 'linear-gradient(135deg, #eab308, #78350f)'],
            'planet1.webp' => ['#84694F', 'linear-gradient(135deg, #d4a373, #5c4033)'],
            'planet2.webp' => ['#602E23', 'linear-gradient(135deg, #ef4444, #450a0a)'],
            'planet3.webp' => ['#1F3B4C', 'linear-gradient(135deg, #06b6d4, #083344)'],
            'planet4.webp' => ['#673E87', 'linear-gradient(135deg, #a855f7, #3b0764)'],
            'planet5.webp' => ['#783B55', 'linear-gradient(135deg, #ec4899, #500724)'],
            'planet6.webp' => ['#CBB8CC', 'linear-gradient(135deg, #e9d5ff, #7e22ce)'],
            'planet7.webp' => ['#593480', 'linear-gradient(135deg, #8b5cf6, #2e1065)'],
            'planet8.webp' => ['#26537B', 'linear-gradient(135deg, #60a5fa, #172554)'],
            'Outline 6.webp' => ['#26262C', 'linear-gradient(135deg, #9ca3af, #111827)'],
        ];

        foreach ($cases as $filename => [$color, $gradient]) {
            $style = ServicePlanetStyle::forImage('/Services/'.$filename);

            $this->assertSame($color, $style['dotColor'], $filename);
            $this->assertSame($gradient, $style['gradient'], $filename);
        }
    }

    public function test_legacy_and_url_encoded_filenames_are_supported(): void
    {
        $this->assertSame('#245183', ServicePlanetStyle::forImage('/Services/Blue%20Planet.webp')['dotColor']);
        $this->assertSame('#673E87', ServicePlanetStyle::forImage('/Services/Purple Planet.webp')['dotColor']);
    }
}
