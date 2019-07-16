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

Route::get('upload-audio/{hash}', 'UploadAudioController@index')->name('upload-audio.index');
Route::post('upload-audio/{hash}', 'UploadAudioController@store')->name('upload-audio.store');

Route::get('list-audio/{hash}', 'ListAudioController@index')->name('list-audio.index');
