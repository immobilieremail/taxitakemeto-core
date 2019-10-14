<?php

namespace App;

use App\Extensions\SwissNumber;
use Illuminate\Database\Eloquent\Model;

class Audio extends Media
{
    /**
     * Audio file are store in MP3 format
     * @var String
     */
    protected $extension = 'mp3';
    








    /*
    protected $fillable = ['path'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $swiss_number = new SwissNumber;
        if (isset($attributes['extension']))
            $this->path = $swiss_number() . '.' . $attributes['extension'];
    }

    public static function create(array $attributes = array())
    {
        $audio = new Audio($attributes);

        $audio->save();
        return $audio;
    }

    public function viewFacet()
    {
        return $this->hasOne(AudioViewFacet::class, 'id_audio', 'id');
    }

    public function editFacet()
    {
        return $this->hasOne(AudioEditFacet::class, 'id_audio', 'id');
    }*/
}
