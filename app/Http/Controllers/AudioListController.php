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
    public function store()
    {
        $audiolist = AudioList::create();
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);

        return response()->json(
            [
                'type' => 'ocap',
                'ocapType' => 'AudioListEdit',
                'url' => "/api/audiolist/$audiolist_edit->swiss_number/edit"
            ]
        );
    }

    public function show($facet_id)
    {
        $view_facet = AudioListViewFacet::find($facet_id);
        $edit_facet = AudioListEditFacet::find($facet_id);
        $facet = ($view_facet) ? $view_facet : $edit_facet;

        if ($facet != NULL) {
            return response()->json(
                $facet->getJsonViewFacet());
        } else
            abort(404);
    }

    public function edit($edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::findOrFail($edit_facet_id);

        return response()->json(
            $edit_facet->getJsonEditFacet());
    }

    public function update(Request $request, $edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::findOrFail($edit_facet_id);
        $func = function ($audio) {
            if (isset($audio["id"]) && is_string($audio["id"]))
                return AudioViewFacet::find($audio["id"]);
        };

        if ($request->has('data') && isset($request["data"]["audios"])) {
            $new_audios = array_filter(array_map($func, $request["data"]["audios"]));
            if (count($request["data"]["audios"]) == count($new_audios)) {

                $edit_facet->updateAudioList($new_audios);

                return response()->json(
                    $edit_facet->getJsonEditFacet());
            } else
                abort(400);
        } else
            abort(400);
    }
}
