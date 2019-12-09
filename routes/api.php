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

Route::post('media', 'MediaController@store')->name('media.store');
Route::post('list', 'OcapListController@store')->name('list.store');
Route::post('pi', 'PIController@store')->name('pi.store');
Route::post('travel', 'TravelController@store')->name('travel.store');
Route::post('shell', 'ShellController@store')->name('shell.store');

Route::apiResource('obj', 'FacetController');

