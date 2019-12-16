<?php

namespace App\Http\Controllers;

use App\Http\Requests\OcapListRequest;

use App\Models\Facet;
use App\Models\OcapList;
use App\Models\OcapListEditFacet;
use App\Models\OcapListViewFacet;

class OcapListController extends Controller
{
    /**
     * Create OcapList
     *
     * @param Request $request
     * @return void
     */
    public function store(OcapListRequest $request)
    {
        $ocapList = OcapList::create();
        $ocapList->editFacet()->save(new OcapListEditFacet);
        $ocapList->viewFacet()->save(new OcapListViewFacet);

        foreach ($request->ocaps as $ocap) {
            $ocapList->contents()->save(
                Facet::find(getSwissNumberFromUrl($ocap))
            );
        }

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'OcapListEditFacet',
            'url' => route('obj.show', ['obj' => $ocapList->editFacet->id])
        ]);
    }
}
