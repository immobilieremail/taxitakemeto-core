<?php

namespace App;

use App\SwissObject;

class AudioEditFacet extends SwissObject
{
    protected $fillable = ['id_audio'];

    public static function create(Array $param)
    {
        $obj = new AudioEditFacet;

        $obj->id_audio = $param["id_audio"];
        $obj->save();
        return $obj;
    }
}
