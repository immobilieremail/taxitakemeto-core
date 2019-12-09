<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Shell;
use App\Models\ShellUserFacet;

class ShellController extends Controller
{
    public function store()
    {
        $shell = Shell::create();
        $shell->userFacet()->save(new ShellUserFacet);

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'ShellUserFacet',
            'url' => route('obj.show', ['obj' => $shell->userFacet->id])
        ]);
    }

    /**
     * Get facet which will be linked to shell
     *
     * @param array $audiolist
     * @return mixed
     */
    private function mapUpdateRequest(Array $audiolist)
    {
        $condition = isset($audiolist['ocap']) && isset($audiolist['ocapType']);

        $ocap_class = ($condition) ? 'App\\' . $audiolist['ocapType'] . 'Facet' : null;
        $facet_swiss_number = ($condition) ? getSwissNumberFromUrl($audiolist['ocap']) : '';

        return (class_exists($ocap_class)) ? $ocap_class::find($facet_swiss_number) : null;
    }

    public function update(Request $request, $shell_id)
    {
        $shell_user = ShellUserFacet::findOrFail($shell_id);
        $condition = isset($request["data"]) && array_key_exists("audiolists", $request["data"]);

        $audiolists = ($condition) ? $request["data"]["audiolists"] : null;
        $new_audiolists = collect($audiolists)->map(function ($audiolist) {
            return $this->mapUpdateRequest($audiolist);
        });
        if (!$new_audiolists->contains(null) && $audiolists !== null) {
            $shell_user->updateShell($new_audiolists);
            return response()->json(
                $shell_user->getJsonShell());
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
        preg_match('#[^/]+$#', str_replace(
            "/edit", "", $data["ocap"]), $ocap_id);

        $ocap_class = 'App\\' . $data["ocapType"] . 'Facet';
        if (method_exists($ocap_class, 'shells')) {
            $facet = $ocap_class::find($ocap_id[0]);
            if ($facet)
                return $facet;
        }

        return null;
    }

    public function send(Request $request, $shell_id)
    {
        $dropbox = ShellDropboxFacet::findOrFail($shell_id);

        if (!isset($request['data']))
            abort(400);

        $valid_data = collect($request["data"])->map(function ($data) {
            return $this->sendGetElementsID($data);
        });
        if (!$valid_data->contains(null) && !$valid_data->isEmpty()) {
            foreach ($valid_data as $data)
                $data->shells()->save($dropbox->shell);
            return response('', 200);
        } else
            abort(400);
    }
}
