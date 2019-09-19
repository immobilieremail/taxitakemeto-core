<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Shell,
    App\ShellUserFacet,
    App\ShellDropboxFacet;

use App\AudioListEditFacet,
    App\AudioListViewFacet;

use App\Jobs\ProcessDropboxMessage;

use App\Http\Requests\SendDropboxMessageRequest;

class ShellController extends Controller
{
    public function store()
    {
        $shell = Shell::create();
        $shell_user = ShellUserFacet::create(['id_shell' => $shell->id]);
        $shell_dropbox = ShellDropboxFacet::create(['id_shell' => $shell->id]);

        return response()->json(
            [
                'type' => 'ocap',
                'ocapType' => 'Shell',
                'url' => "/api/shell/$shell_user->swiss_number"
            ]
        );
    }

    public function show($shell_id)
    {
        $shell = ShellUserFacet::findOrFail($shell_id);

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
        $shell = ShellUserFacet::findOrFail($shell_id);

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

    /**
     * Get objects needed to send a message to ShellDropbox
     *
     * @param mixed $data (return null if not array)
     * @return mixed (array : null)
     */
    private function sendGetElementsID($data)
    {
        preg_match('#[^/]+$#', $data["ocap"], $ocap_id);
        preg_match('#[^/]+$#', $data["dropbox"],$dropbox_id);

        $shell_dropbox = ShellDropboxFacet::find($dropbox_id[0]);
        $ocap_class = 'App\\' . $data["ocapType"] . 'Facet';
        if (class_exists($ocap_class)) {
        $facet = $ocap_class::find($ocap_id[0]);
        if ($shell_dropbox && $facet) {
            return [
                'dropbox' => $shell_dropbox,
                'facet' => $facet
            ];
        } else
            return null;
        } else
            return null;
    }

    public function send(SendDropboxMessageRequest $request, $shell_id)
    {
        $shell_user = ShellUserFacet::findOrFail($shell_id);

        $valid_data = collect($request["data"])->map(function ($data) {
            return $this->sendGetElementsID($data);
        });
        if (!$valid_data->contains(null)) {
            $valid_data->map(function ($data) {
                $this->dispatch(new ProcessDropboxMessage($data));
            });
            return response('', 200);
        } else
            abort(400);
    }
}
