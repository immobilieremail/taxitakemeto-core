<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Travel;
use App\Models\TravelViewFacet;
use App\Models\TravelEditFacet;

use Faker\Generator as Faker;

$factory->define(Travel::class, function (Faker $faker) {
    return [
        'title' => $faker->country()
    ];
});

$factory->afterCreating(Travel::class, function ($travel, $faker) {
    $travel->viewFacet()->save(factory(TravelViewFacet::class)->make());
});

$factory->afterCreating(Travel::class, function ($travel, $faker) {
    $travel->editFacet()->save(factory(TravelEditFacet::class)->make());
});