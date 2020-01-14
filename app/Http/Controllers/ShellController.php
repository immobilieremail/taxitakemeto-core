<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShellRequest;

use App\Models\Facet;

use App\Models\Shell;
use App\Models\ShellUserFacet;
use App\Models\ShellInviteFacet;
use App\Models\ShellDropboxFacet;

class ShellController extends Controller
{
    public function store(ShellRequest $request)
    {
        $shell = Shell::create();

        $fields = intersectFields(['user', 'travels', 'contacts'], $request->all());
        $relations = [
            'user' => $shell->users(),
            'travels' => $shell->travelOcapListFacets(),
            'contacts' => $shell->contactOcapListFacets()
        ];

        foreach ($fields as $i => $field) {
            $relations[$i]->save(
                Facet::find(getSwissNumberFromUrl($field))
            );
        }

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'ShellUserFacet',
            'url' => route('obj.show', ['obj' => $shell->userFacet->id])
        ]);
    }
}
