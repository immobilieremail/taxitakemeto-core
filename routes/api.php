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
Route::post('audiolist/{audiolist_id}/add_audio', 'AudioListController@add_audio')->name('audiolist.add_audio');
Route::post('audiolist/{audiolist_id}/remove_audio', 'AudioListController@remove_audio')->name('audiolist.remove_audio');

Route::any('/{catchall}', function() {
    return abort(404);
})->where('catchall', '(.*)');