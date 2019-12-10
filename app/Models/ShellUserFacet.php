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
        $ocapListFacet = $this->target->travelOcapListFacets->first();

        return [
            'type' => 'ShellUserFacet',
            'data' => [
                'travels' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->id]) : null
            ]
        ];
    }
}
