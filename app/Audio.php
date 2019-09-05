<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $fillable = ['path'];

    public static function create(Array $param)
    {
        $audio = new Audio;

        $audio->save();
        $audio->path = $audio->id . '.' . $param["extension"];
        $audio->save();
        return $audio;
    }
}
