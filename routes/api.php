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


Route::resource('shell', 'ShellController')->only('create', 'show', 'update');
Route::post('shell/{shell}', 'ShellController@send')->name('shell.send');

Route::post('media', 'MediaController@store')->name('media.store');
Route::post('list', 'OcapListController@store')->name('list.store');
Route::resource('obj', 'FacetController');

