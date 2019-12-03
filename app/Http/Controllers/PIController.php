<?php

namespace App\Http\Controllers;

use App\Models\PI;
use App\Models\PIEditFacet;
use App\Models\PIViewFacet;
use Illuminate\Http\Request;

class PIController extends Controller
{
    /**
     * Create PI
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $mediaLists = [];

        if ($request->data == null
            || !array_key_exists('title', $request['data'])
            || !array_key_exists('description', $request['data'])
            || !array_key_exists('address', $request['data'])
            || !array_key_exists('medias', $request['data'])
            || !preg_match("#([^/])+$#", $request['data']['medias'], $mediaLists)) {
            return response('Bad Request', 400);
        }

        $pi = PI::create($request["data"]);
        $pi->editFacet()->save(new PIEditFacet);
        $pi->viewFacet()->save(new PIViewFacet);

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'PIEditFacet',
            'url' => route('obj.show', ['obj' => $pi->editFacet->id])
        ]);
    }
}
