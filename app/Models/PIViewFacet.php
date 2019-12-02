<?php

namespace App\Models;

use Illuminate\Http\Request;

class PIViewFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\PIViewFacet';
    }

    /**
     * Inverse relation of ViewFacet for specific PI
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(PI::class);
    }

    public function has_show()
    {
        return true;
    }

    public function description()
    {
        return [
            'type' => 'PIViewFacet',
            'data' => []
        ];
    }
}
