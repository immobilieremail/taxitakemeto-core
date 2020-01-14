<?php

namespace App\Models;

use App\Models\MediaViewFacet;
use App\Models\MediaEditFacet;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * Table where media are stored
     * @var String
     */
    protected $table = 'media';

    /**
     * Model fillable data
     *
     * @var Array
     */
    protected $fillable = [
        'path', 'media_type'
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

        static::created(function (Media $media) {
            $media->editFacet()->save(new MediaEditFacet);
            $media->viewFacet()->save(new MediaViewFacet);
        });
    }

    /**
     * EditFacet for specific media
     *
     * @return [type] [description]
     */
    public function editFacet()
    {
        return $this->hasOne(MediaEditFacet::class, 'target_id')
                    ->where('type', 'App\Models\MediaEditFacet');
    }

    /**
     * ViewFacet for specific media
     *
     * @return [type] [description]
     */
    public function viewFacet()
    {
        return $this->hasOne(MediaViewFacet::class, 'target_id')
                    ->where('type', 'App\Models\MediaViewFacet');
    }
}
