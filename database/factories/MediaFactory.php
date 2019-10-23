<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Media;
use App\Models\MediaEdit;
use App\Models\MediaView;
use Faker\Generator as Faker;

$factory->define(Media::class, function (Faker $faker) {
    return [
        'path' => $faker->url(),
        'mimetype' => $faker->mimeType
    ];
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->viewFacet()->save(factory(MediaView::class)->make());
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->editFacet()->save(factory(MediaEdit::class)->make());
});
