<?php

namespace App\Models;

use Illuminate\Http\Request;

class PIViewFacet extends Facet
{
    /**
     * Inverse relation of ViewFacet for specific PI
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(PI::class);
    }

    public function show()
    {
        $ocapListFacet = $this->target->mediaOcapListFacets->first();

        return $this->jsonResponse([
            'type' => 'PIViewFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'data' => [
                'title' => $this->target->title,
                'description' => $this->target->description,
                'address' => $this->target->address,
                'medias' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->target->viewFacet->id]) : null
            ]
        ]);
    }
}
