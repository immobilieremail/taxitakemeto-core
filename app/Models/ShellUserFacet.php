<?php

namespace App\Models;

use Illuminate\Http\Request;

class ShellUserFacet extends Facet
{
    /**
     * Inverse relation of UserFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }

    public function has_show()
    {
        return true;
    }

    public function description()
    {
        $travelListFacet = $this->target->travelOcapListFacets->first();
        $contactListFacet = $this->target->contactOcapListFacets->first();

        return [
            'type' => 'ShellUserFacet',
            'data' => [
                'travels' => ($travelListFacet != null)
                    ? route('obj.show', ['obj' => $travelListFacet->id]) : null,
                'contacts' => ($contactListFacet != null)
                    ? route('obj.show', ['obj' => $contactListFacet->id]) : null
            ]
        ];
    }

    public function has_update()
    {
        return true;
    }

    private function processRequest(Request $request) : array
    {
        $allowed = ['travels'];
        $new_data = array_intersect_key($request->all(), array_flip($allowed));
        $tested_data = array_filter($new_data, function ($value, $key) {
            $tests = [
                'travels' => is_string($value)
                    && Facet::find(getSwissNumberFromUrl($value))
            ];

            return $tests[$key];
        }, ARRAY_FILTER_USE_BOTH);

        return $tested_data;
    }

    public function updateTarget(Request $request)
    {
        $tested_data = $this->processRequest($request);
        $updatables = [
            'travels' => $this->target->travelOcapListFacets()
        ];

        foreach ($tested_data as $i => $value) {
            $facet = Facet::find(getSwissNumberFromUrl($value));

            $updatables[$i]->detach();
            $updatables[$i]->save($facet);
        }
        return !empty($tested_data);
    }
}
