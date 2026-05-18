<?php

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\PostImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

function validateStore(array $data): Illuminate\Validation\Validator
{
    return Validator::make($data, (new StorePostRequest)->rules());
}

function validateUpdate(array $data): Illuminate\Validation\Validator
{
    return Validator::make($data, (new UpdatePostRequest)->rules());
}

function fakeImage(): UploadedFile
{
    return UploadedFile::fake()->image('a.jpg');
}

// ── StorePostRequest ──────────────────────────────────────────────

test('store passes with images only', function () {
    expect(validateStore(['images' => [fakeImage()]])->passes())->toBeTrue();
});

test('store passes with title, content, and images', function () {
    $validator = validateStore([
        'title' => 'sample title',
        'content' => 'caption text',
        'images' => [fakeImage(), fakeImage()],
    ]);

    expect($validator->passes())->toBeTrue();
});

test('store fails without images', function () {
    $validator = validateStore(['content' => 'body']);

    expect($validator->passes())->toBeFalse()
        ->and($validator->errors()->has('images'))->toBeTrue();
});

test('store fails when images is empty array', function () {
    $validator = validateStore(['images' => []]);

    expect($validator->errors()->has('images'))->toBeTrue();
});

test('store fails when content exceeds 10000 chars', function () {
    $validator = validateStore([
        'content' => str_repeat('a', 10001),
        'images' => [fakeImage()],
    ]);

    expect($validator->errors()->has('content'))->toBeTrue();
});

test('store fails when title exceeds 255 chars', function () {
    $validator = validateStore([
        'title' => str_repeat('a', 256),
        'images' => [fakeImage()],
    ]);

    expect($validator->errors()->has('title'))->toBeTrue();
});

test('store fails when more than 10 images uploaded', function () {
    $images = array_fill(0, 11, fakeImage());

    $validator = validateStore(['images' => $images]);

    expect($validator->errors()->has('images'))->toBeTrue();
});

test('store fails when a non-image file is uploaded', function () {
    $validator = validateStore([
        'images' => [UploadedFile::fake()->create('doc.pdf', 100)],
    ]);

    expect($validator->errors()->has('images.0'))->toBeTrue();
});

test('store fails when an image exceeds 2MB', function () {
    $validator = validateStore([
        'images' => [UploadedFile::fake()->image('huge.jpg')->size(2049)],
    ]);

    expect($validator->errors()->has('images.0'))->toBeTrue();
});

// ── UpdatePostRequest ─────────────────────────────────────────────

test('update passes with no fields', function () {
    expect(validateUpdate([])->passes())->toBeTrue();
});

test('update passes with content only', function () {
    expect(validateUpdate(['content' => 'updated body'])->passes())->toBeTrue();
});

test('update passes with new images only', function () {
    expect(validateUpdate(['images' => [fakeImage()]])->passes())->toBeTrue();
});

test('update fails when content exceeds 10000 chars', function () {
    $validator = validateUpdate(['content' => str_repeat('a', 10001)]);

    expect($validator->errors()->has('content'))->toBeTrue();
});

test('update accepts deleted_image_ids that exist', function () {
    $image = PostImage::factory()->create();

    $validator = validateUpdate(['deleted_image_ids' => [$image->id]]);

    expect($validator->passes())->toBeTrue();
});

test('update rejects deleted_image_ids that do not exist', function () {
    $validator = validateUpdate(['deleted_image_ids' => [999999]]);

    expect($validator->errors()->has('deleted_image_ids.0'))->toBeTrue();
});
