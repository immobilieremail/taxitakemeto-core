<?php

namespace App\Models;

use App\Models\Facet;
use Illuminate\Support\Facades\Storage;

class MediaViewFacet extends Facet
{
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
            'type' => 'MediaViewFacet',
            'media_type' => $this->target->media_type,
            'path' => Storage::disk('converts')->url($this->target->path)
        ];
    }
}
