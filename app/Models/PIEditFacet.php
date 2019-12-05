<?php

namespace App\Models;

use Illuminate\Http\Request;

class PIEditFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\PIEditFacet';
    }

    /**
     * Inverse relation of EditFacet for specific PI
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
            'type' => 'PIEditFacet',
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'data' => [
                'title' => $this->target->title,
                'description' => $this->target->description,
                'address' => $this->target->address,
                'medias' => $mediaArray
            ]
        ];
    }

    public function has_destroy()
    {
        return true;
    }

    public function destroyTarget()
    {
        $this->target->viewFacet->delete();
        $this->target->delete();
        $this->delete();
    }

    public function has_update()
    {
        return true;
    }

    public function updateTarget(Request $request)
    {
        $allowed = ['title', 'description', 'address'];
        $new_data = array_intersect_key($request->all(), array_flip($allowed));
        $tested_data = array_filter($new_data, function ($value, $key) {
            $tests = [
                'title' => is_string($value),
                'description' => is_string($value),
                'address' => is_string($value)
            ];

            return $tests[$key];
        }, ARRAY_FILTER_USE_BOTH);

        $this->target->update($tested_data);
        if ($request->has('medias') && is_string($request->medias)) {
            $listFacet = Facet::all()->where('id', getSwissNumberFromUrl($request->medias))->first(); // BAAD
            if ($listFacet == null) {
                return false;
            } else {
                $this->target->mediaOcapListFacets()->detach();
                $this->target->mediaOcapListFacets()->save($listFacet);
            }
        }
        return true;
    }
}
