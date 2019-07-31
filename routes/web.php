<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/{lang?}', 'ShellController@index')->name('index')->where('lang', implode('|', array_flip(config('app.languages'))));
Route::post('/{lang}/shell', 'ShellController@store')->name('shell.store');
Route::get('/{lang}/shell/{shell_id}', 'ShellController@show')->name('shell.show');

Route::get('/{lang}/upload-audio/{suisse_nbr}', 'UploadAudioController@index')->name('upload-audio.index');
Route::post('/{lang}/upload-audio/{suisse_nbr}', 'UploadAudioController@store')->name('upload-audio.store');
Route::post('{lang}/upload-audio/{suisse_nbr}/share', 'UploadAudioController@share')->name('upload-audio.share');
Route::patch('/{lang}/upload-audio/{suisse_nbr}/{audio_id}', 'UploadAudioController@update')->name('upload-audio.update');
Route::delete('/{lang}/upload-audio/{suisse_nbr}/{audio_id}', 'UploadAudioController@destroy')->name('upload-audio.destroy');

Route::get('/{lang}/list-audio/{suisse_nbr}', 'ListAudioController@index')->name('list-audio.index');

Route::any('/{lang}/{catchall}', function() {
    return response(view('404'), 404);
})->where('catchall', '(.*)');
