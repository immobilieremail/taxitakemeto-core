<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PI;
use App\Models\PIViewFacet;
use App\Models\PIEditFacet;
use Faker\Generator as Faker;

$factory->define(PI::class, function (Faker $faker) {
    return [
        'title' => $faker->title() . $faker->name(),
        'description' => $faker->text(),
        'address' => "1 rue des iiyama"
    ];
});

$factory->afterCreating(PI::class, function ($pi, $faker) {
    $pi->viewFacet()->save(factory(PIViewFacet::class)->make());
});

$factory->afterCreating(PI::class, function ($pi, $faker) {
    $pi->editFacet()->save(factory(PIEditFacet::class)->make());
});