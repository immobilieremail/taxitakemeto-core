<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Shell;
use Carbon\Carbon;
use App\ShellUserFacet;
use App\ShellDropboxFacet;
use Faker\Generator as Faker;

$factory->define(Shell::class, function (Faker $faker) {
    return [
        'created_at'    =>  Carbon::now(),
        'updated_at'    =>  Carbon::now(),
    ];
});


$factory->afterCreating(Shell::class, function ($shell, $faker) {
    $shell->userFacet()->save(factory(ShellUserFacet::class)->make([
        'id_shell'   =>  $shell->id
    ]));
});

$factory->afterCreating(Shell::class, function ($shell, $faker) {
    $shell->dropboxFacet()->save(factory(ShellDropboxFacet::class)->make([
        'id_shell'   =>  $shell->id
    ]));
});