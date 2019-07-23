<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoundList extends Model
{
    protected $fillable = ['id'];

    public static function getFirstSoundList($list_id)
    {
        $first_soundlist = SoundList::where('id', $list_id)->first();

        return $first_soundlist;
    }

    public static function countSoundLists()
    {
        $count = 0;
        $soundlists = SoundList::all();

        foreach ($soundlists as $soundlist)
            $count += 1;
        return $count;
    }
}
