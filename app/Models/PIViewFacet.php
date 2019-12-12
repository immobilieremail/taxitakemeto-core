<?php

namespace App\Models;

use Illuminate\Http\Request;

class PIViewFacet extends Facet
{
    /**
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [
        'show'
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
     * Inverse relation of ViewFacet for specific PI
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(PI::class);
    }

    public function description()
    {
        $ocapListFacet = $this->target->mediaOcapListFacets->first();

        return [
            'type' => 'PIViewFacet',
            'data' => [
                'title' => $this->target->title,
                'description' => $this->target->description,
                'address' => $this->target->address,
                'medias' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->target->viewFacet->id]) : null
            ]
        ];
    }
}
