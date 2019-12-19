<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class OcapListEditFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show', 'update', 'destroy'
    ];

    /**
     * Inverse relation of EditFacet for specific ocaplist
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(OcapList::class);
    }

    public function description()
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
        return [
            'type' => 'OcapListEditFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'contents' => $collection->toArray()
        ];
    }

    public function destroyTarget()
    {
        $this->target->viewFacet->delete();
        $this->target->delete();
        $this->delete();
    }

    public function updateTarget(Request $request)
    {
        if (!isset($request["ocaps"]) || !is_array($request["ocaps"])) {
            return false;
        }

        $ocapCollection = collect($request["ocaps"])->map(function ($ocap) {
            return Facet::find(getSwissNumberFromUrl($ocap));
        });

        if ($ocapCollection->search(null) !== false) {
            return false;
        } else {
            $this->target->contents()->detach();
            $this->target->contents()->saveMany($ocapCollection);
            return true;
        }
    }
}
