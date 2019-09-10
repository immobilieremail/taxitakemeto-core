<?php

namespace App;

use App\SwissObject;

class AudioViewFacet extends SwissObject
{
    protected $fillable = ['id_audio'];

    public static function create(Array $param)
    {
        $obj = new AudioViewFacet;

        $obj->id_audio = $param["id_audio"];
        $obj->save();
        return $obj;
    }

    public function lists()
    {
        return $this->morphToMany('App\AudioList', 'join_audio');
    }
}
