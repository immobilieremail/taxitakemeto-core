<?php

namespace App\Models;

use App\Models\Facet;

class MediaEditFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->facet_type = 'edit';
    }
}
