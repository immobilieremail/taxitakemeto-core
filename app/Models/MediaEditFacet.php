<?php

namespace App\Models;

use App\Models\Facet;
use Illuminate\Support\Facades\Storage;

class MediaEditFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show', 'destroy'
    ];

    /**
     * Inverse relation of EditFacet for specific media
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
            'type' => 'MediaEditFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'media_type' => $this->target->media_type,
            'path' => Storage::disk('converts')->url($this->target->path)
        ];
    }

    public function destroyTarget()
    {
        $this->target->viewFacet->delete();
        $this->target->delete();
        $this->delete();
    }
}
