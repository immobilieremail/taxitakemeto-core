<?php

namespace App\Models;

class TravelViewFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show'
    ];

    /**
     * Inverse relation of ViewFacet for specific travel
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Travel::class);
    }

    public function description()
    {
        $ocapListFacet = $this->target->piOcapListFacets->first();

        return [
            'type' => 'TravelViewFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'data' => [
                'title' => $this->target->title,
                'pis' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->target->viewFacet->id]) : null
            ]
        ];
    }
}
