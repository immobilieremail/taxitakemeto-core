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


Route::resource('shell', 'ShellController')->only('store', 'show', 'update');
Route::resource('audiolist', 'AudioListController')->except('index', 'create', 'destroy');
Route::resource('audio', 'AudioController')->except('index', 'create', 'update');

Route::post('shell/{shell}', 'ShellController@send')->name('shell.send');