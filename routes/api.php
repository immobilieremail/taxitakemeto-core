<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::resource('audiolist', 'AudioListController')->only('store', 'show', 'edit');
Route::resource('audiolist/{audiolist_id}/audio', 'AudioController')->only('store', 'update', 'destroy');

Route::any('/{catchall}', function() {
    return abort(404);
})->where('catchall', '(.*)');