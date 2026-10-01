<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Downscales and re-compresses uploaded avatars/logos before they're stored.
 * Without this, phone-camera photos (several MB, thousands of pixels wide)
 * get served at that full size for a 40-80px avatar, on every page that
 * shows one - this is what makes list pages with many photos slow to load.
 */
class ImageOptimizer
{
    public static function resize(UploadedFile $file, int $maxDimension = 600, int $quality = 82): string
    {
        $original = file_get_contents($file->getRealPath());

        $image = @imagecreatefromstring($original);
        if (!$image) {
            return $original;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= $maxDimension && $height <= $maxDimension) {
            imagedestroy($image);

            return $original;
        }

        $ratio = min($maxDimension / $width, $maxDimension / $height);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        $mime = $file->getMimeType();

        ob_start();
        if ($mime === 'image/png') {
            imagepng($resized, null, 6);
        } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
            imagewebp($resized, null, $quality);
        } else {
            // JPEG has no alpha channel - flatten transparency onto white first.
            $flattened = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($flattened, 255, 255, 255);
            imagefill($flattened, 0, 0, $white);
            imagecopy($flattened, $resized, 0, 0, 0, 0, $newWidth, $newHeight);
            imagejpeg($flattened, null, $quality);
            imagedestroy($flattened);
        }
        $contents = ob_get_clean();
        imagedestroy($resized);

        return $contents !== false && $contents !== '' ? $contents : $original;
    }
}
