<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OcapList;
use App\Models\OcapListViewFacet;
use App\Models\OcapListEditFacet;
use Faker\Generator as Faker;

$factory->define(OcapList::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->afterCreating(OcapList::class, function ($ocaplist, $faker) {
    $ocaplist->viewFacet()->save(factory(OcapListViewFacet::class)->make());
});

$factory->afterCreating(OcapList::class, function ($ocaplist, $faker) {
    $ocaplist->editFacet()->save(factory(OcapListEditFacet::class)->make());
});