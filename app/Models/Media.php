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
     * @param array
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
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
     * Get file extention from protected variable
     * 
     * @return String
     */
    protected function getExtension(): String
    {
        return $this->extension;
    }


    /**
     * @return [type]
     
    public function viewFacet()
    {
        return $this->morphOne(MediaView::class, 'target');
    }

    public function editFacet()
    {
        return $this->morphOne(MediaEdit::class, 'target');
    }
    */
}
