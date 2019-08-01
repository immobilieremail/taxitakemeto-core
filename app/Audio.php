<?php

namespace App;

use App\SwissObject;

class Audio extends SwissObject
{
    protected $fillable = ['path'];

    public static function create(Array $param)
    {
        $obj = new Audio;

        $obj->path = $param["path"] . $obj->swiss_number . '.' . $param["extension"];
        $obj->save();
        return $obj;
    }
}
