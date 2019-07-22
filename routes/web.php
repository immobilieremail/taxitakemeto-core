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

Route::get('/', 'IndexController@index')->name('index');
Route::post('/', 'IndexController@store')->name('index.store');

Route::get('upload-audio/{suisse_nbr}', 'UploadAudioController@index')->name('upload-audio.index');
Route::post('upload-audio/{suisse_nbr}', 'UploadAudioController@store')->name('upload-audio.store');
Route::patch('upload-audio/{suisse_nbr}/{audio_id}', 'UploadAudioController@update')->name('upload-audio.update');
Route::delete('upload-audio/{suisse_nbr}/{audio_id}', 'UploadAudioController@destroy')->name('upload-audio.destroy');

Route::get('list-audio/{suisse_nbr}', 'ListAudioController@index')->name('list-audio.index');

Route::any('{catchall}', function() {
    return view('404');
})->where('catchall', '(.*)');