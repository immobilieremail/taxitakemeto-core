<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    /**
     * Table where travels are stored
     * @var String
     */
    protected $table = 'travels';

    /**
     * Model fillable data
     *
     * @var Array
     */
    protected $fillable = [
        'title'
    ];

    /**
     * Constructor for eloquent model hierarchy
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (Travel $travel) {
            $travel->editFacet()->save(new TravelEditFacet);
            $travel->viewFacet()->save(new TravelViewFacet);
        });
    }


    /**
     * OcapList facets for Travel PI list
     *
     * @return [type] [description]
     */
    public function piOcapListFacets()
    {
        return $this->belongsToMany(Facet::class);
    }

    /**
     * EditFacet for specific Travel
     *
     * @return [type] [description]
     */
    public function editFacet()
    {
        return $this->hasOne(TravelEditFacet::class, 'target_id')
                    ->where('type', 'App\Models\TravelEditFacet');
    }

    /**
     * ViewFacet for specific Travel
     *
     * @return [type] [description]
     */
    public function viewFacet()
    {
        return $this->hasOne(TravelViewFacet::class, 'target_id')
                    ->where('type', 'App\Models\TravelViewFacet');
    }
}
