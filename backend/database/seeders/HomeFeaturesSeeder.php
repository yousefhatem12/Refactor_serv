<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class HomeFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frontendFeaturesDir = base_path('../public/Features');
        $destDir = storage_path('app/public/settings');

        // Ensure settings directory exists
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // 1. Seed features_image (15Y.webp)
        $src15Y = $frontendFeaturesDir . '/15Y.webp';
        $dest15Y = $destDir . '/15Y.webp';
        $seededImage = 'settings/15Y.webp';

        if (file_exists($src15Y)) {
            copy($src15Y, $dest15Y);
        } else {
            // Write a tiny transparent 1x1 WebP placeholder if not found
            $tinyWebp = base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4H');
            file_put_contents($dest15Y, $tinyWebp);
        }

        // 2. Seed features_partners (Extract embedded PNGs from frontend SVG logos and convert to WebP)
        $partnerFiles = [
            '2-300x300-1.png.svg',
            '5-300x300-1.png.svg',
            '6-300x300-1.png.svg',
            'clients-ksa-20-300x300-1.png.svg',
            'clients-ksa-25-300x300-1.png.svg',
        ];

        $seededPartners = [];

        foreach ($partnerFiles as $index => $filename) {
            $srcPath = $frontendFeaturesDir . '/' . $filename;
            $destName = 'partner_' . ($index + 1) . '.webp';
            $destPartnerPath = $destDir . '/' . $destName;

            $success = false;

            if (file_exists($srcPath)) {
                $content = file_get_contents($srcPath);
                
                // Extract base64 PNG data from the SVG wrapper
                if (preg_match('/xlink:href="data:image\/png;base64,([^"]+)"/', $content, $matches)) {
                    $base64Data = $matches[1];
                    $pngData = base64_decode($base64Data);

                    // Try converting to WebP using GD library
                    if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
                        $image = @imagecreatefromstring($pngData);
                        if ($image) {
                            imagepalettetotruecolor($image);
                            imagealphablending($image, true);
                            imagesavealpha($image, true);
                            imagewebp($image, $destPartnerPath, 80);
                            imagedestroy($image);
                            $success = true;
                        }
                    }
                }
            }

            // Fallback: If source is missing or GD conversion is not supported, write a tiny valid WebP
            if (!$success) {
                $tinyWebp = base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4H');
                file_put_contents($destPartnerPath, $tinyWebp);
            }

            $seededPartners[] = 'settings/' . $destName;
        }

        // 3. Database inserts/updates
        Setting::updateOrCreate(
            ['key' => 'features_title'],
            [
                'value' => json_encode(['en' => 'Who We Are', 'ar' => 'من نحن']),
                'type' => 'text',
                'value_source' => 'db'
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'features_desc'],
            [
                'value' => json_encode([
                    'en' => 'Serv5 provides a range of cutting-edge, customized software solutions for global enterprise clients.',
                    'ar' => 'توفر Serv5 مجموعة من الحلول البرمجية المتطورة والمخصصة لعملاء المؤسسات العالمية.'
                ]),
                'type' => 'text',
                'value_source' => 'db'
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'features_image'],
            [
                'value' => $seededImage,
                'type' => 'image',
                'value_source' => 'file'
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'features_partners'],
            [
                'value' => json_encode($seededPartners),
                'type' => 'image',
                'value_source' => 'file'
            ]
        );
    }
}
