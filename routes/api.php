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


Route::resource('audiolist', 'AudioListController')->only('store', 'show', 'edit');
Route::resource('audiolist/{audiolist_id}/audio', 'AudioController')->only('store', 'update', 'destroy');

Route::get('/audiolist', function() {
    return view('audiolist_index',
        [
            'edits' => \App\AudioListEditFacet::all()->toArray(),
            'views' => \App\AudioListViewFacet::all()->toArray()
        ]
    );
});

Route::get('/audiolist/{audiolist_id}/audio', function($edit_id) {
    $audiolist_edit = \App\AudioListEditFacet::find($edit_id);

    if ($audiolist_edit != NULL) {
        return view('audio_index',
            [
                'audios' => $audiolist_edit->getAudios(),
                'edit_id' => $edit_id,
            ]
        );
    } else
        abort(404);
});

Route::any('/{catchall}', function() {
    return abort(404);
})->where('catchall', '(.*)');