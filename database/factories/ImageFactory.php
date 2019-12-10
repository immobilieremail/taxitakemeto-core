<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'path' => $faker->url(),
        'mimetype' => $faker->mimeType,
        'media_type' => "image"
    ];
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->viewFacet()->save(factory(MediaView::class)->make());
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->editFacet()->save(factory(MediaEdit::class)->make());
});
