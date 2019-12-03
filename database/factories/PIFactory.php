<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PI;
use App\Models\PIViewFacet;
use App\Models\PIEditFacet;
use Faker\Generator as Faker;

$factory->define(PI::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->afterCreating(PI::class, function ($ocaplist, $faker) {
    $ocaplist->viewFacet()->save(factory(PIViewFacet::class)->make());
});

$factory->afterCreating(PI::class, function ($ocaplist, $faker) {
    $ocaplist->editFacet()->save(factory(PIEditFacet::class)->make());
});