<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Media;
use App\Models\MediaEditFacet;
use App\Models\MediaViewFacet;
use Faker\Generator as Faker;

$factory->define(Media::class, function (Faker $faker) {
    return [
        'path' => $faker->url(),
        'media_type' => $faker->randomElement($array = ['audio', 'video', 'image'])
    ];
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->viewFacet()->save(factory(MediaViewFacet::class)->make());
});

$factory->afterCreating(Media::class, function ($media, $faker) {
    $media->editFacet()->save(factory(MediaEditFacet::class)->make());
});
