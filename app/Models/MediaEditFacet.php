<?php

namespace App\Models;

use App\Models\Facet;

class MediaEditFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->facet_type = 'App\Models\MediaEditFacet';
    }

    /**
     * Inverse relation of EditFacet for specific media
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
            'a' => 'edit'
        ];
    }

    public function has_destroy()
    {
        return true;
    }

    public function destroyTarget()
    {
        $media = $this->target;
        $media-> delete();
    }
}
