<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PI extends Model
{
    /**
     * Table where PI are stored
     * @var String
     */
    protected $table = 'p_i_s';

    /**
     * Model fillable data
     *
     * @var Array
     */
    protected $fillable = [
        'title', 'description', 'address'
    ];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (PI $pi) {
            $pi->editFacet()->save(new PIEditFacet);
            $pi->viewFacet()->save(new PIViewFacet);
        });
    }

    /**
     * OcapList facets for PI media list
     *
     * @return [type] [description]
     */
    public function mediaOcapListFacets()
    {
        return $this->belongsToMany(Facet::class);
    }

    /**
     * EditFacet for specific PI
     *
     * @return [type] [description]
     */
    public function editFacet()
    {
        return $this->hasOne(PIEditFacet::class, 'target_id')
                    ->where('type', 'App\Models\PIEditFacet');
    }

    /**
     * ViewFacet for specific PI
     *
     * @return [type] [description]
     */
    public function viewFacet()
    {
        return $this->hasOne(PIViewFacet::class, 'target_id')
                    ->where('type', 'App\Models\PIViewFacet');
    }
}
