<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Shell;

use App\AudioListEditFacet,
    App\AudioListViewFacet;

class ShellController extends Controller
{
    public function store()
    {
        $shell = Shell::create();

        return response()->json(
            [
                'type' => 'ocap',
                'ocapType' => 'Shell',
                'url' => "/api/shell/$shell->swiss_number"
            ]
        );
    }

    public function show($shell_id)
    {
        $shell = Shell::findOrFail($shell_id);

        return response()->json(
                $shell->getJsonShell());
    }

    private function mapUpdateRequest(Array $request_audiolists)
    {
        return array_map(function ($audiolist) {
            if (isset($audiolist["id"]) && is_string($audiolist["id"])) {
                $audiolist_view = AudioListViewFacet::find($audiolist["id"]);
                $audiolist_edit = AudioListEditFacet::find($audiolist["id"]);
                return ($audiolist_view) ? $audiolist_view : $audiolist_edit;
            }
        }, $request_audiolists);
    }

    public function update(Request $request, $shell_id)
    {
        $shell = Shell::findOrFail($shell_id);

        if ($request->has('data') && isset($request["data"]["audiolists"])) {
            $new_audiolists = array_filter(
                $this->mapUpdateRequest($request["data"]["audiolists"]));
            if (count($request["data"]["audiolists"]) == count($new_audiolists)) {

                $shell->updateShell($new_audiolists);

                return response()->json(
                    $shell->getJsonShell());
            } else
                abort(400);
        } else
            abort(400);
    }
}
