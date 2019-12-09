<?php

namespace App\Models;

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
}
