<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageService
{
    public static function convertToWebp(TemporaryUploadedFile|UploadedFile $file, string $directory): string
    {
        $image = null;
        $extension = strtolower($file->getClientOriginalExtension());
        $content = $file->get();

        try {
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $image = imagecreatefromjpeg($file->getRealPath());
            } elseif ($extension === 'png') {
                $image = imagecreatefrompng($file->getRealPath());
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            } elseif ($extension === 'webp') {
                // Already webp, save to private disk.
                return $file->store($directory, 'local');
            }

            if ($image) {
                $filename = str()->random(40) . '.webp';
                $path = $directory . '/' . $filename;
                
                ob_start();
                imagewebp($image, null, 80);
                $webpContent = ob_get_clean();
                
                Storage::disk('local')->put($path, $webpContent);
                imagedestroy($image);
                
                return $path;
            }
        } catch (\Exception $e) {
            // Fallback to default storage if conversion fails
        }

        return $file->store($directory, 'local');
    }
}
