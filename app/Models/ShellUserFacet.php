<?php

namespace App\Models;

use Illuminate\Http\Request;

class ShellUserFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show', 'update'
    ];

    /**
     * Inverse relation of UserFacet for specific Shell
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(Shell::class);
    }

    public function description()
    {
        $userFacet = $this->target->users->first();
        $travelListFacet = $this->target->travelOcapListFacets->first();
        $contactListFacet = $this->target->contactOcapListFacets->first();

        return [
            'type' => 'ShellUserFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'data' => [
                'user' => ($userFacet != null)
                    ? route('obj.show', ['obj' => $userFacet->id]) : null,
                'travels' => ($travelListFacet != null)
                    ? route('obj.show', ['obj' => $travelListFacet->id]) : null,
                'contacts' => ($contactListFacet != null)
                    ? route('obj.show', ['obj' => $contactListFacet->id]) : null,
                'dropbox' => route('obj.show', ['obj' => $this->target->dropboxFacet->id]),
                'invitation' => route('obj.show', ['obj' => $this->target->inviteFacet->id])
            ]
        ];
    }

    private function processRequest(Request $request) : array
    {
        $new_data = intersectFields(['travels', 'contacts', 'user'], $request->all());
        $tested_data = array_filter($new_data, function ($value, $key) {
            $tests = [
                'travels' => is_string($value)
                    && Facet::find(getSwissNumberFromUrl($value)),
                'contacts' => is_string($value)
                    && Facet::find(getSwissNumberFromUrl($value)),
                'user' => is_string($value)
                    && UserProfileFacet::find(getSwissNumberFromUrl($value))
            ];

            return $tests[$key];
        }, ARRAY_FILTER_USE_BOTH);

        return $tested_data;
    }

    public function updateTarget(Request $request)
    {
        $tested_data = $this->processRequest($request);
        $updatables = [
            'travels' => $this->target->travelOcapListFacets(),
            'contacts' => $this->target->contactOcapListFacets(),
            'user' => $this->target->users()
        ];

        foreach ($tested_data as $i => $value) {
            $facet = Facet::find(getSwissNumberFromUrl($value));

            $updatables[$i]->detach();
            $updatables[$i]->save($facet);
        }
        return !empty($tested_data);
    }
}
