<?php

namespace App\Models;

use Illuminate\Http\Request;

class TravelEditFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\TravelEditFacet';
    }

    /**
     * Inverse relation of EditFacet for specific travel
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Travel::class);
    }

    public function has_show()
    {
        return true;
    }

    public function description()
    {
        $ocapListFacet = $this->target->piOcapListFacets->first();

        return [
            'type' => 'TravelEditFacet',
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'data' => [
                'title' => $this->target->title,
                'pis' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->target->viewFacet->id]) : null
            ]
        ];
    }

    public function has_destroy()
    {
        return true;
    }

    public function destroyTarget()
    {
        $this->target->viewFacet->delete();
        $this->target->delete();
        $this->delete();
    }

    public function has_update()
    {
        return true;
    }

    public function updateTarget(Request $request)
    {
        if (!$request->has('title') || !is_string($request->title)) {
            return false;
        } else {
            $this->target->update(['title' => $request->title]);
        }

        if ($request->has('pis') && is_string($request->medias)) {
            $listFacet = Facet::all()->where('id', getSwissNumberFromUrl($request->medias))->first(); // BAAD
            if ($listFacet == null) {
                return false;
            } else {
                $this->target->piOcapListFacets()->detach();
                $this->target->piOcapListFacets()->save($listFacet);
            }
        }
        return true;
    }
}
