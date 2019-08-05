<?php

namespace App;

use App\SwissObject;

class AudioListViewFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public static function create(Array $param)
    {
        $obj = new AudioListViewFacet;

        $obj->id_list = $param["id_list"];
        $obj->save();
        return $obj;
    }
}
