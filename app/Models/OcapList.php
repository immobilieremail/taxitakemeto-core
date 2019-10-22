<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcapList extends Model
{
    /**
     * Table where ocaplist are stored
     * @var String
     */
    protected $table = 'ocap_lists';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public function contents()
    {
        return $this->belongsToMany(Facet::class);
    }

    /**
     * EditFacet for specific ocaplist
     *
     * @return [type] [description]
     */
    public function editFacet()
    {
        return $this->hasOne(OcapListEditFacet::class, 'target_id')
                    ->where('type', 'App\Models\OcapListEditFacet');
    }

    /**
     * ViewFacet for specific ocaplist
     *
     * @return [type] [description]
     */
    public function viewFacet()
    {
        return $this->hasOne(OcapListViewFacet::class, 'target_id')
                    ->where('type', 'App\Models\OcapListViewFacet');
    }
}
