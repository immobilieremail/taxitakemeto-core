<?php

namespace App;

use App\SwissObject;

class ShellDropboxFacet extends SwissObject
{
    protected $fillable = ['id_shell'];

    public static function create(Array $param)
    {
        $obj = new ShellDropboxFacet;

        $obj->id_shell = $param["id_shell"];
        $obj->save();
        return $obj;
    }
}
