<?php

namespace App\Models;

use App\Models\Facet;

class MediaViewFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->facet_type = 'App\Models\MediaViewFacet';
    }

    /**
     * Inverse relation of ViewFacet for specific media
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Media::class);
    }

    public function has_show()
    {
        return true;
    }

    public function description()
    {
        return [
            'a' => 'view'
        ];
    }
}
