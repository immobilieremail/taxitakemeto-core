<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Travel;
use Faker\Generator as Faker;

$factory->define(Travel::class, function (Faker $faker) {
    return [
        'title' => $faker->country()
    ];
});
