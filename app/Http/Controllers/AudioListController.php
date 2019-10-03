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
        $audiolist_view = $audiolist->viewFacet()->save(new AudioListViewFacet);
        $audiolist_edit = $audiolist->editFacet()->save(new AudioListEditFacet);

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

    private function mapUpdateRequest(Array $request_audios)
    {
        return array_map(function ($audio) {
            if (isset($audio['ocap']) && is_string($audio['ocap'])) {
                if (preg_match('#[^/]+$#', $audio['ocap'], $matches)) {
                    return AudioViewFacet::find($matches[0]);
                }
            }
        }, $request_audios);
    }

    public function update(Request $request, $edit_facet_id)
    {
        $edit_facet = AudioListEditFacet::findOrFail($edit_facet_id);

        if ($request->has('data') && isset($request["data"]["audios"])) {
            $new_audios = array_filter(
                $this->mapUpdateRequest($request["data"]["audios"]));
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
