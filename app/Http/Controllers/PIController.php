<?php

namespace App\Http\Controllers;

use App\Models\PI;
use App\Models\Facet;
use App\Models\PIEditFacet;
use App\Models\PIViewFacet;

use App\Rules\PIRules;
use App\Http\Requests\PIRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $pi->editFacet()->save(new PIEditFacet);
        $pi->viewFacet()->save(new PIViewFacet);

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
