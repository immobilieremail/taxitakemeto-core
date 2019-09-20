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

<<<<<<< HEAD
Route::get('/', 'IndexController@index');

Route::resource('upload-audio', 'UploadAudioController');

Route::resource('list-audio', 'ListAudioController');


/*
|--------------------------------------------------------------------------
| Setup Language via session
|--------------------------------------------------------------------------
*/
Route::get('/language/{lang}', 'IndexController@language')->name('language');
Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
=======
>>>>>>> c84982b970c5d4b5b8285329b8d125ce4f645e27
