<?php

namespace App\Models;

class TravelViewFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\TravelViewFacet';
    }

    /**
     * Inverse relation of ViewFacet for specific travel
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Travel::class);
    }
}
