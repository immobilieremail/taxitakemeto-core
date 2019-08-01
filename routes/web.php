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

Route::get('/{lang}/audiolist_edit/{swiss_number}', 'AudioListController@edit')->name('audiolist.edit');
Route::post('/{lang}/audiolist_edit/{swiss_number}/new_audio', 'AudioListController@new_audio')->name('audiolist.new_audio');
Route::delete('/{lang}/audiolist_edit/{swiss_number}/{audio_id}', 'AudioListController@destroy')->name('audiolist.destroy');

Route::get('/{lang}/upload-audio/{swiss_number}', 'UploadAudioController@index')->name('upload-audio.index');
Route::post('/{lang}/upload-audio/{swiss_number}', 'UploadAudioController@store')->name('upload-audio.store');
Route::post('{lang}/upload-audio/{swiss_number}/share', 'UploadAudioController@share')->name('upload-audio.share');
Route::patch('/{lang}/upload-audio/{swiss_number}/{audio_id}', 'UploadAudioController@update')->name('upload-audio.update');
Route::delete('/{lang}/upload-audio/{swiss_number}/{audio_id}', 'UploadAudioController@destroy')->name('upload-audio.destroy');

Route::get('/{lang}/list-audio/{swiss_number}', 'ListAudioController@index')->name('list-audio.index');

Route::any('/{lang}/{catchall}', function() {
    return response(view('404'), 404);
})->where('catchall', '(.*)');
