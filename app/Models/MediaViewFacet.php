<?php

namespace App\Models;

use App\Models\Facet;
use Illuminate\Support\Facades\Storage;

class MediaViewFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show'
    ];

    /**
     * Inverse relation of ViewFacet for specific media
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Media::class);
    }

    public function description()
    {
        return [
            'type' => 'MediaViewFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'media_type' => $this->target->media_type,
            'path' => Storage::disk('converts')->url($this->target->path)
        ];
    }
}
