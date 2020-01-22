<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcapListViewFacet extends Facet
{
    /**
     * Inverse relation of ViewFacet for specific ocaplist
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(OcapList::class);
    }

    public function show()
    {
        $facetList = $this->target->contents;
        $collection = $facetList->map(function ($facet) {
            preg_match('#([^\\\])+$#', get_class($facet), $class_names);
            $ocapType = $class_names[0];

            return [
                'type' => 'ocap',
                'ocapType' => $ocapType,
                'url' => route('obj.show', ['obj' => $facet->id])
            ];
        });
        return $this->jsonResponse([
            'type' => 'OcapListViewFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'contents' => $collection->toArray()
        ]);
    }
}
