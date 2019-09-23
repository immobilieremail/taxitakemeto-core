<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\AudioList,
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
                'ocapType' => 'ALEdit',
                'url' => "/api/audiolist/$audiolist_edit->swiss_number/edit"
            ]
        );
    }

    public function show($facet_id)
    {
        $view_facet = AudioListViewFacet::find($facet_id);
        $edit_facet = AudioListEditFacet::find($facet_id);
        $facet_type = ($view_facet) ? "ALView" : "ALEdit";
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
                    'type' => 'ALEdit',
                    'new_audio' => "/api/audiolist/$edit_facet->swiss_number/audio",
                    'view_facet' => "/api/audiolist/" . $edit_facet->audioList->viewFacet->swiss_number,
                    'contents' => $edit_facet->getEditableAudios()
                ]
            );
        } else
            abort(404);
    }
}
