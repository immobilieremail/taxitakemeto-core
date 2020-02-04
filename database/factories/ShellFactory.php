<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Shell;
use App\Models\ShellUserFacet;
use App\Models\ShellInviteFacet;
use App\Models\ShellDropboxFacet;

use Faker\Generator as Faker;

$factory->define(Shell::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->afterCreating(Shell::class, function ($shell, $faker) {
    $shell->userFacet()->save(factory(ShellUserFacet::class)->make());
});

$factory->afterCreating(Shell::class, function ($shell, $faker) {
    $shell->dropboxFacets()->save(factory(ShellInviteFacet::class)->make());
});

$factory->afterCreating(Shell::class, function ($shell, $faker) {
    $shell->dropboxFacets()->save(factory(ShellDropboxFacet::class)->make());
});
