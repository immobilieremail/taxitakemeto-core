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
Route::post('/{lang}/shell/{shell_id}/new_audio_list', 'ShellController@new_audio_list')->name('shell.new_audio_list');
Route::post('/{lang}/shell/{shell_id}/{msg_id}/accept', 'ShellController@accept')->name('shell.accept');

Route::get('/{lang}/audiolist_edit/{swiss_number}', 'AudioListController@edit')->name('audiolist.edit');
Route::post('/{lang}/audiolist_edit/{swiss_number}/new_audio', 'AudioListController@new_audio')->name('audiolist.new_audio');
Route::patch('/{lang}/audiolist_edit/{swiss_number}/{audio_id}', 'AudioListController@update')->name('audiolist.update');
Route::delete('/{lang}/audiolist_edit/{swiss_number}/{audio_id}', 'AudioListController@destroy')->name('audiolist.destroy');

Route::post('/{lang}/audiolist_share/{swiss_number}', 'AudioListController@share')->name('audiolist.share');

Route::get('/{lang}/list-audio/{swiss_number}', 'ListAudioController@index')->name('list-audio.index');

Route::any('/{lang}/{catchall}', function() {
    return response(view('404'), 404);
})->where('catchall', '(.*)');
