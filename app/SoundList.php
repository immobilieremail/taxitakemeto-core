<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoundList extends Model
{
    public static function getFirstSoundList($list_id)
    {
        $first_soundlist = SoundList::where('id', $list_id)->first();

        return $first_soundlist;
    }
}
