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
        $facet_obj = Facet::findOrFail($facet);

        if ($facet_obj->has_show() == true) {
            return response()->json($facet_obj->description());
        } else {
            return response('Method Not Allowed', 405);
        }
    }

    public function update()
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param String $facet
     * @return void
     */
    public function destroy(String $facet)
    {
        $facet_obj = Facet::findOrFail($facet);

        if ($facet_obj != null && $facet_obj->has_destroy() == true) {
            $facet_obj->destroyTarget();
            return response('', 204);
        } else {
            return response('Method Not Allowed', 405);
        }
    }

    public function edit()
    {
        //
    }
}
