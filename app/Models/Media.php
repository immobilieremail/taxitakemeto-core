<?php

namespace App\Models;

use App\Models\MediaViewFacet;
use App\Models\MediaEditFacet;

use Illuminate\Database\Eloquent\Model;

abstract class Media extends Model
{
    /**
     * Store manipulated file extension
     * @var String
     */
    protected $extension;

    /**
     * Store manipulated media type
     * @var String
     */
    protected $media_type;

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
        'path', 'media_type', 'mimetype'
    ];

    /**
     * Constructor for eloquent model hierarchy
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->extension = $attributes['extension'];
    }

    /**
     * EditFacet for specific media
     *
     * @return [type] [description]
     */
    public function editFacet()
    {
        return $this->hasOne(MediaEditFacet::class, 'target_id')
                    ->where('facet_type', 'edit');
    }

    /**
     * ViewFacet for specific media
     *
     * @return [type] [description]
     */
    public function viewFacet()
    {
        return $this->hasOne(MediaViewFacet::class, 'target_id')
                    ->where('facet_type', 'view');
    }


    /**
     * Model boot function, init path when model saved
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function (Media $item) {
            $item->setPathAttribute();
            $item->setMediaType();
        });
    }

    /**
     * Set path variable attribute based
     * - on a swiss number and file extension
     */
    protected function setPathAttribute()
    {
        $this->attributes['path'] = swiss_number().'.'.$this->getExtension();
    }

    /**
     * Set media_type variable attribute based
     * - on media type
     */
    protected function setMediaType()
    {
        $this->attributes['media_type'] = $this->getMediaType();
    }

    /**
     * Get file extention from protected variable
     *
     * @return String
     */
    protected function getExtension(): String
    {
        return $this->extension;
    }

    /**
     * Get file extention from protected variable
     *
     * @return String
     */
    protected function getMediaType(): String
    {
        return $this->media_type;
    }
}
