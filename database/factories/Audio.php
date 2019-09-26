<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audio;
use Carbon\Carbon;
use App\AudioEditFacet;
use App\AudioViewFacet;
use Faker\Generator as Faker;

$factory->define(Audio::class, function (Faker $faker) {
    return [
        'id'            =>  rand(200, 560),
        'path'          =>  str_random(24) . '.mp3',
        'created_at'    =>  Carbon::now(),
        'updated_at'    =>  Carbon::now(),
    ];
});

$factory->afterCreating(Audio::class, function ($audio, $faker) {
    $audio->viewFacet()->save(factory(AudioViewFacet::class)->make([
        'id_audio'   =>  $audio->id
    ]));
});

$factory->afterCreating(Audio::class, function ($audio, $faker) {
    $audio->editFacet()->save(factory(AudioEditFacet::class)->make([
        'id_audio'   =>  $audio->id
    ]));
});