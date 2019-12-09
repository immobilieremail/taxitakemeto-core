<?php

namespace App\Models;

class TravelViewFacet extends Facet
{
    /**
     * Inverse relation of ViewFacet for specific travel
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
            'type' => 'TravelViewFacet',
            'data' => [
                'title' => $this->target->title,
                'pis' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->target->viewFacet->id]) : null
            ]
        ];
    }
}
