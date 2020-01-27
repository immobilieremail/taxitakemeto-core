<?php

namespace App\Http\Controllers;

use App\Models\Facet;
use Illuminate\Http\Request;

class FacetController extends Controller
{
    /**
     * Respond to GET
     *
     * @param String $facet
     * @return void
     */
    public function show(String $facet, Request $request)
    {
        return Facet::findOrFail($facet)->show($request);
    }

    /**
     * Respond to POST
     *
     * @param String $facet
     * @param Request $request
     * @return void
     */
    public function store(String $facet, Request $request)
    {
        return Facet::findOrFail($facet)->store($request);
    }

    /**
     * Respond to PUT/PATCH
     *
     * @param String $facet
     * @param Request $request
     * @return void
     */
    public function update(String $facet, Request $request)
    {
        return Facet::findOrFail($facet)->httpUpdate($request);
    }

    /**
     * Respond to DESTROY
     *
     * @param String $facet
     * @return void
     */
    public function destroy(String $facet)
    {
        return Facet::findOrFail($facet)->httpDestroy();
    }
}
