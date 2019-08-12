<?php

namespace App;

use App\SwissObject;

class ShellDropboxMessage extends SwissObject
{
    protected $fillable = ['capability', 'type'];

    public static function create(Array $param)
    {
        $obj = new ShellDropboxMessage;

        $obj->capability = $param["capability"];
        $obj->type = $param["type"];
        $obj->save();
        return $obj;
    }
}
