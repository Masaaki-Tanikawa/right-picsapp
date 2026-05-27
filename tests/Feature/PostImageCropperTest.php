<?php

use App\Services\PostImageCropper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

beforeEach(function () {
    Storage::fake('public');
    $this->cropper = app(PostImageCropper::class);
    $this->manager = app(ImageManager::class);
});

test('image within allowed aspect range is stored without cropping', function (int $w, int $h) {
    $file = UploadedFile::fake()->image('photo.jpg', $w, $h);

    $path = $this->cropper->store($file, 'post-images');

    Storage::disk('public')->assertExists($path);
    expect($path)->toStartWith('post-images/');

    $stored = $this->manager->decodePath(Storage::disk('public')->path($path));
    expect($stored->width())->toBe($w)
        ->and($stored->height())->toBe($h);
})->with([
    'square 800x800' => [800, 800],
    'portrait 4:5 (800x1000)' => [800, 1000],
    'landscape 1.91:1 (1910x1000)' => [1910, 1000],
]);

test('overly tall image is center-cropped to 4:5', function () {
    $file = UploadedFile::fake()->image('tall.jpg', 800, 1600); // 1:2

    $path = $this->cropper->store($file, 'post-images');

    $stored = $this->manager->decodePath(Storage::disk('public')->path($path));
    expect($stored->width())->toBe(800)
        ->and($stored->height())->toBe(1000); // 800 * 1.25
});

test('overly wide image is center-cropped to 1.91:1', function () {
    $file = UploadedFile::fake()->image('wide.jpg', 2000, 500); // 4:1

    $path = $this->cropper->store($file, 'post-images');

    $stored = $this->manager->decodePath(Storage::disk('public')->path($path));
    expect($stored->width())->toBe(955) // round(500 / (1/1.91))
        ->and($stored->height())->toBe(500);
});
