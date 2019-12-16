<?php

namespace App\Models;

use Illuminate\Http\Request;

class TravelEditFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show', 'update', 'destroy'
    ];

    /**
     * Check if Facet has permissions for specific request method
     *
     * @return bool permission
     */
    public function has_access(String $method): bool
    {
        return in_array($method, $this->permissions, true);
    }

    /**
     * Inverse relation of EditFacet for specific travel
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Travel::class);
    }

    public function description()
    {
        $ocapListFacet = $this->target->piOcapListFacets->first();

        return [
            'type' => 'TravelEditFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'data' => [
                'title' => $this->target->title,
                'pis' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->id]) : null
            ]
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
        $new_data = intersectFields(['title', 'pis'], $request->all());
        $tested_data = array_filter($new_data, function ($value, $key) {
            $tests = [
                'title' => is_string($value),
                'pis' => is_string($value)
                    && Facet::find(getSwissNumberFromUrl($value))
            ];

            return $tests[$key];
        }, ARRAY_FILTER_USE_BOTH);

        if ($new_data != $tested_data) return false;

        if (array_key_exists('title', $tested_data)) {
            $this->target->update(['title' => $request->title]);
        }

        if (array_key_exists('pis', $tested_data)) {
            $this->target->piOcapListFacets()->detach();
            $this->target->piOcapListFacets()->save(
                Facet::find(getSwissNumberFromUrl($request->pis))
            );
        }
        return true;
    }
}
