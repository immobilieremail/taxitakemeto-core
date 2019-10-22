<?php

namespace App\Models;

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
        return [
            'type' => 'OcapListEditFacet',
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
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

    public function has_update()
    {
        return true;
    }
}
