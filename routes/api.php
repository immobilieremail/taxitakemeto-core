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

Route::post('/audiolist', 'AudioListController@store');

Route::get('/audiolist_edit/{audiolist_edit}', 'AudioListController@edit');
Route::post('/audiolist_edit/{audiolist_edit}/new_audio', 'AudioListController@new_audio');
Route::put('/audiolist_edit/{audiolist_edit}/audio/{audio_id}', 'AudioListController@update');
Route::delete('/audiolist_edit/{audiolist_edit}/audio/{audio_id}', 'AudioListController@delete');

Route::get('/audiolist_view/{audiolist_view}', 'AudioListController@view');

Route::any('/{catchall}', function() {
    return 404;
})->where('catchall', '(.*)');