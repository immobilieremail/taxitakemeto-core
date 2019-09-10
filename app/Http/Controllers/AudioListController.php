<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Audio,
    App\AudioEditFacet,
    App\AudioViewFacet;

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
                'ocapType' => 'AudioListEdit',
                'url' => route('audiolist.edit', ['audiolist' => $audiolist_edit->swiss_number])
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
                    "id" => $facet->swiss_number,
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
                    'view_facet' => route('audiolist.show', ['audiolist' => $edit_facet->getViewFacet()->swiss_number]),
                    'contents' => $edit_facet->getEditableAudios()
                ]
            );
        } else
            abort(404);
    }

    public function add_audio(Request $request, $edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::find($edit_facet_id);
        $audiolist = ($edit_facet) ?
            AudioList::find($edit_facet->id_list) : NULL;
        $audio = ($request->has('audio')) ?
            AudioViewFacet::find($request->audio) : NULL;

        if ($audiolist != NULL) {
            if ($audio != NULL) {
                $audiolist->audioViews()->save($audio);
                return response()->json(
                    [
                        'status' => 200
                    ]
                );
            } else
                abort(400);
        } else
            abort(404);
    }

    public function remove_audio(Request $request, $edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::find($edit_facet_id);
        $audiolist = ($edit_facet) ?
            AudioList::find($edit_facet->id_list) : NULL;
        $audio = ($request->has('audio')) ?
            AudioViewFacet::find($request->audio) : NULL;

        if ($audiolist != NULL) {
            if ($audio != NULL) {
                $audiolist->audioViews()->detach($audio);
                return response()->json(
                    [
                        'status' => 200
                    ]
                );
            } else
                abort(400);
        } else
            abort(404);
    }
}
