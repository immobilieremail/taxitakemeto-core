<?php

namespace App\Http\Controllers;

use App\Models\PI;
use App\Models\Facet;

use App\Http\Requests\PIRequest;

use Illuminate\Http\Request;

class PIController extends Controller
{
    /**
     * Create PI
     *
     * @param Request $request
     * @return void
     */
    public function store(PIRequest $request)
    {
        $pi = PI::create($request->all());

        if ($request->has('medias')) {
            $listFacet = Facet::find(getSwissNumberFromUrl($request->medias));
            $pi->mediaOcapListFacets()->save($listFacet);
        }

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'PIEditFacet',
            'url' => route('obj.show', ['obj' => $pi->editFacet->id])
        ]);
    }
}
