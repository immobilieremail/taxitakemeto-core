<?php

namespace App\Models;

use App\Models\Facet;
use Illuminate\Support\Facades\Storage;

class MediaEditFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\MediaEditFacet';
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
            'type' => 'MediaEditFacet',
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'path' => Storage::disk('converts')->url($this->target->path)
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
