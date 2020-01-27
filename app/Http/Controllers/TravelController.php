<?php

namespace App\Http\Controllers;

use App\Models\Facet;

use App\Models\Travel;
use App\Models\TravelViewFacet;
use App\Models\TravelEditFacet;

use App\Http\Requests\TravelRequest;

class TravelController extends Controller
{
    /**
     * Create Travel
     *
     * @param TravelRequest $request
     * @return void
     */
    public function store(TravelRequest $request)
    {
        $travel = Travel::create($request->all());

        if ($request->has('pis')) {
            $listFacet = Facet::find(getSwissNumberFromUrl($request->pis));
            $travel->piOcapListFacets()->save($listFacet);
        }

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'TravelEditFacet',
            'url' => route('obj.show', ['obj' => $travel->editFacet->id])
        ]);
    }
}
