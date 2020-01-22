<?php

namespace App\Models;

use App\Models\Facet;
use Illuminate\Support\Facades\Storage;

class MediaEditFacet extends Facet
{
    /**
     * Inverse relation of EditFacet for specific media
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Media::class);
    }

    public function show()
    {
        return $this->jsonResponse([
            'type' => 'MediaEditFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'media_type' => $this->target->media_type,
            'path' => Storage::disk('converts')->url($this->target->path)
        ]);
    }

    public function httpDestroy()
    {
        $this->target->viewFacet->delete();
        $this->target->delete();
        $this->delete();
        return $this->response('', 204);
    }
}
