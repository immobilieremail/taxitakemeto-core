<?php

namespace App;

use App\SwissObject;

class AudioViewFacet extends SwissObject
{
    protected $fillable = ['id_audio'];

    public function audio()
    {
        return $this->belongsTo(Audio::class, 'id_audio');
    }

    public function audiolists()
    {
        return $this->morphToMany('App\AudioList', 'join_audio');
    }
}
