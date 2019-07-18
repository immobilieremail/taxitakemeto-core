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

Route::get('upload-audio/', function () {return redirect('/');});
Route::get('upload-audio/{suisse_nbr}', 'UploadAudioController@show')->name('upload-audio.show');
Route::post('upload-audio/{suisse_nbr}', 'UploadAudioController@store')->name('upload-audio.store');
Route::patch('upload-audio/{suisse_nbr}', 'UploadAudioController@update')->name('upload-audio.update');
Route::delete('upload-audio/{suisse_nbr}', 'UploadAudioController@destroy')->name('upload-audio.destroy');

Route::get('list-audio/', function () {return redirect('/');});
Route::get('list-audio/{suisse_nbr}', 'ListAudioController@show')->name('list-audio.show');
