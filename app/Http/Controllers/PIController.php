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
        $pi = PI::create();
        $pi->editFacet()->save(new PIEditFacet);
        $pi->viewFacet()->save(new PIViewFacet);

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'PIEditFacet',
            'url' => route('obj.show', ['obj' => $pi->editFacet->id])
        ]);
    }
}
