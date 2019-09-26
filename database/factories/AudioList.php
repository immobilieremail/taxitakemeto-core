<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AudioList;
use Carbon\Carbon;
use App\AudioListEditFacet;
use App\AudioListViewFacet;
use Faker\Generator as Faker;

$factory->define(AudioList::class, function (Faker $faker) {
    return [
        'created_at'    =>  Carbon::now(),
        'updated_at'    =>  Carbon::now(),
    ];
});


$factory->afterCreating(AudioList::class, function ($audiolist, $faker) {
    $audiolist->viewFacet()->save(factory(AudioListViewFacet::class)->make([
        'id_list'   =>  $audiolist->id
    ]));
});

$factory->afterCreating(AudioList::class, function ($audiolist, $faker) {
    $audiolist->editFacet()->save(factory(AudioListEditFacet::class)->make([
        'id_list'   =>  $audiolist->id
    ]));
});