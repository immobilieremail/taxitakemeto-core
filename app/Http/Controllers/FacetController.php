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

    /**
     * Undocumented function
     * @param String $facet
     * @param Request $request
     * @return void
     */
    public function store(String $facet, Request $request)
    {
        $facet_obj = Facet::findOrFail($facet);

        if ($facet_obj->has_access('store') == true) {
            $response = $facet_obj->post_data($request);
            if ($response === false) {
                return response('Bad Request', 400);
            } else {
                return response()->json($response);
            }
        } else {
            return response('Method Not Allowed', 405);
        }
    }

    /**
     * Undocumented function
     *
     * @param String $facet
     * @return void
     */
    public function show(String $facet)
    {
        $facet_obj = Facet::findOrFail($facet);

        if ($facet_obj->has_access('show') == true) {
            return response()->json($facet_obj->description());
        } else {
            return response('Method Not Allowed', 405);
        }
    }

    /**
     * Undocumented function
     *
     * @param String $facet
     * @param Request $request
     * @return void
     */
    public function update(String $facet, Request $request)
    {
        $facet_obj = Facet::findOrFail($facet);

        if($facet_obj->has_access('update') == true) {
            if ($facet_obj->updateTarget($request) == true) {
                return response('No Content', 204);
            } else {
                return response('Bad Request', 400);
            }
        } else {
            return response('Method Not Allowed', 405);
        }
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

        if ($facet_obj->has_access('destroy') == true) {
            $facet_obj->destroyTarget();
            return response('', 204);
        } else {
            return response('Method Not Allowed', 405);
        }
    }
}
