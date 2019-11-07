<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class OcapListEditFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\OcapListEditFacet';
    }

    /**
     * Inverse relation of EditFacet for specific ocaplist
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
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'contents' => $collection->toArray()
        ];
    }

    public function has_destroy()
    {
        return true;
    }

    public function destroyTarget()
    {
        $ocapList = $this->target;
        $ocapList-> delete();
    }

    public function updateTarget(Request $request)
    {
        if (!isset($request["ocaps"]) || !is_array($request["ocaps"])) {
            return false;
        }

        $ocapCollection = collect($request["ocaps"])->map(function ($ocap) {
            $ocapId = [];
            if (!preg_match("#([^/])+$#", $ocap, $ocapId)){
                return null;
            } else {
                return Facet::all()->where('id', $ocapId[0])->first(); // BAAD
            }
        });

        if ($ocapCollection->search(null) !== false) {
            return false;
        } else {
            $this->target->contents()->detach();
            foreach ($ocapCollection as $facet) {
                $this->target->contents()->save($facet);
            }
            return true;
        }
    }

    public function has_update()
    {
        return true;
    }
}
