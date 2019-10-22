<?php

namespace App\Http\Controllers;

use App\Models\OcapList;
use Illuminate\Http\Request;
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
    public function store(Request $request)
    {
        $ocapList = OcapList::create();
        $ocapList->editFacet()->save(new OcapListEditFacet);
        $ocapList->viewFacet()->save(new OcapListViewFacet);

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'OcapListEditFacet',
            'url' => route('obj.show', ['obj' => $ocapList->editFacet->id])
        ]);
    }
}
