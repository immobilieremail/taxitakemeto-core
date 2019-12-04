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
        $mediaArray = [];
        $mediaListFacet = $this->target->mediaOcapListFacets->first();

        if ($mediaListFacet != null) {
            $mediaListContents = $mediaListFacet->target->contents;
            $mediaArray = $mediaListContents->map(function ($mediaFacet) {
                return [
                    'type' => 'ocap',
                    'ocapType' => 'MediaViewFacet',
                    'url' => route('obj.show', ['obj' => $mediaFacet->target->viewFacet->id])
                ];
            })->toArray();
        }

        return [
            'type' => 'PIViewFacet',
            'data' => [
                'title' => $this->target->title,
                'description' => $this->target->description,
                'address' => $this->target->address,
                'medias' => $mediaArray
            ]
        ];
    }
}
