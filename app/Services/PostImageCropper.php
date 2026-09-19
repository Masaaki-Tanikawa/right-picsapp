<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class PostImageCropper
{
    /**
     * Max height/width ratio (4:5 portrait — beyond this the top/bottom is cropped).
     */
    public const MAX_ASPECT = 1.25;

    /**
     * Min height/width ratio (1.91:1 landscape — below this the left/right is cropped).
     */
    public const MIN_ASPECT = 1 / 1.91;

    public function __construct(private ImageManager $manager) {}

    /**
     * Crop the image if its aspect ratio falls outside the allowed range, then
     * persist it to the given disk and return the stored relative path.
     */
    public function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $image = $this->manager->decodePath($file->getRealPath());

        $width = $image->width();
        $height = $image->height();
        $aspect = $height / $width;

        if ($aspect > self::MAX_ASPECT) {
            $newHeight = (int) round($width * self::MAX_ASPECT);
            $offsetY = (int) round(($height - $newHeight) / 2);
            $image = $image->crop($width, $newHeight, 0, $offsetY);
        } elseif ($aspect < self::MIN_ASPECT) {
            $newWidth = (int) round($height / self::MIN_ASPECT);
            $offsetX = (int) round(($width - $newWidth) / 2);
            $image = $image->crop($newWidth, $height, $offsetX, 0);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::random(40).'.'.$extension;
        $path = trim($directory, '/').'/'.$filename;

        Storage::disk($disk)->put($path, (string) $image->encodeUsingFileExtension($extension));

        return $path;
    }
}
