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
            ['key' => 'features_subtitle'],
            [
                'value' => json_encode(['en' => 'Who We Are', 'ar' => 'من نحن'], JSON_UNESCAPED_UNICODE),
                'type' => 'text',
                'value_source' => 'db'
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'features_title'],
            [
                'value' => json_encode([
                    'en' => 'Serv5 For Integrated <span class="text-[#D98B50]">Web & Mobile Solutions</span>',
                    'ar' => 'سيرف فايف للحلول الرقمية <span class="text-[#D98B50]">وخدمات الويب المتكاملة</span>'
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'text',
                'value_source' => 'db'
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'features_desc'],
            [
                'value' => json_encode([
                    'en' => 'You have reached your right destination, what you are looking for is here. With us you will get website design services, mobile application programming, web hosting, motion graphics, and distinguished digital marketing services through a professional team working around the clock.',
                    'ar' => 'لقد وصلت لوجهتك الصحيحة، ما تبحث عنه موجود هنا. معنا ستحصل على خدمات تصميم المواقع الإلكترونية، برمجة تطبيقات الجوال (Android & iOS)، استضافة المواقع، تصميم فيديوهات الموشن جرافيك، وخدمات التسويق الإلكتروني والسيو المتكاملة من خلال فريق عمل محترف على مدار الساعة.'
                ], JSON_UNESCAPED_UNICODE),
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
