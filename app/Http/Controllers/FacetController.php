<?php

namespace App\Http\Controllers;

use App\Models\Facet;
use Illuminate\Http\Request;

class FacetController extends Controller
{
    public function index()
    {
        //
    }

    public function store()
    {
        //
    }

    public function create()
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param Facet $facet
     * @return void
     */
    public function show(String $facet)
    {
        $facet_obj = Facet::find($facet);
        $true_facet = ($facet_obj != null) ? $facet_obj->facet_type::find($facet) : null;

        if ($true_facet != null && $true_facet->has_show() == true) {
            return response()->json($true_facet->description());
        } else {
            return response('Not Found', 404);
        }
    }

    public function update()
    {
        //
    }

    public function destroy(String $facet)
    {
        $facet_obj = Facet::find($facet);
        $true_facet = ($facet_obj != null) ? $facet_obj->facet_type::find($facet) : null;

        if ($true_facet != null && $true_facet->has_destroy() == true) {
            $true_facet->destroyTarget();
            return response('Deleted !', 200);
        } else {
            return response('Not Found', 404);
        }
    }

    public function edit()
    {
        //
    }
}
