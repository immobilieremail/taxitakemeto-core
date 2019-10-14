<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audio;
use Faker\Generator as Faker;

$factory->define(Audio::class, function (Faker $faker) {
    return [
        'path' => $faker->url(),
        'mimetype' => $faker->mimeType,
        'media_type' => "audio"
    ];
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->viewFacet()->save(factory(MediaView::class)->make());
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->editFacet()->save(factory(MediaEdit::class)->make());
});
