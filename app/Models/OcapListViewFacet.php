<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcapListViewFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\OcapListViewFacet';
    }

    /**
     * Inverse relation of ViewFacet for specific ocaplist
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(OcapList::class);
    }

    public function has_show()
    {
        return true;
    }

    public function description()
    {
        $facetList = $this->target->contents;
        $collection = $facetList->map(function ($facet){
            return route('obj.show', ['obj' => $facet->id]);
        });
        return [
            'type' => 'OcapListViewFacet',
            'contents' => $collection->toArray()
        ];
    }
}
