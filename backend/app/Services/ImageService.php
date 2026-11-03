<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageService
{
    public static function convertToWebp(TemporaryUploadedFile|UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $image = null;
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $image = imagecreatefromjpeg($file->getRealPath());
            } elseif ($extension === 'png') {
                $image = imagecreatefrompng($file->getRealPath());
                imagealphablending($image, true);
                imagesavealpha($image, true);
            } elseif ($extension === 'webp') {
                return $file->store($directory, $disk);
            }

            if ($image) {
                imagepalettetotruecolor($image);

                $width = imagesx($image);
                if ($width > 2560) {
                    $scaled = imagescale($image, 2560);
                    if ($scaled !== false) {
                        $image = $scaled;
                    }
                }

                $filename = str()->random(40) . '.webp';
                $path = $directory . '/' . $filename;
                
                ob_start();
                imagewebp($image, null, 80);
                $webpContent = ob_get_clean();
                
                Storage::disk($disk)->put($path, $webpContent);
                unset($image); // imagedestroy() is deprecated since PHP 8.5; GdImage freed by GC.
                
                return $path;
            }
        } catch (\Throwable $e) {
            // Fallback to default storage if conversion fails
        }

        return $file->store($directory, $disk);
    }
}
