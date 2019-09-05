<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Audio,
    App\AudioList,
    App\AudioListEditFacet,
    App\AudioListViewFacet;

class AudioListController extends Controller
{
    public function create()
    {
        $audiolist = AudioList::create();
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);

        return response()->json(
            [
                'type' => 'ocap',
                'ocapType' => 'AudioListEdit',
                'url' => route('audiolist.show', ['audiolist' => $audiolist_edit->swiss_number])
            ]
        );
    }

    public function show($facet_id)
    {
        $view_facet = AudioListViewFacet::find($facet_id);
        $edit_facet = AudioListEditFacet::find($facet_id);
        $facet_type = ($view_facet) ? "AudioListView" : "AudioListEdit";
        $facet = ($view_facet) ? $view_facet : $edit_facet;

        if ($facet != NULL) {
            return response()->json(
                [
                    "type" => $facet_type,
                    "contents" => $facet->getAudios()
                ]
            );
        } else
            abort(404);
    }

    public function edit($edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::find($edit_facet_id);

        if ($edit_facet != NULL) {
            return response()->json(
                [
                    'type' => 'AudioListEdit',
                    'new_audio' => route('audiolist.audio.store', ['audiolist' => $edit_facet_id]),
                    'view_facet' => route('audiolist.show', ['audiolist' => $edit_facet->getViewFacet()->swiss_number]),
                    'contents' => $edit_facet->getEditableAudios()
                ]
            );
        } else
            abort(404);
    }
}
