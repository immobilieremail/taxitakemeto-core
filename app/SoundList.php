<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoundList extends Model
{
    public static function getSoundLists($list_id)
    {
        $soundlists = SoundList::all()->where('id', $list_id);

        return $soundlists;
    }

    public static function getFirstSoundList($list_id)
    {
        $first_soundlist = NULL;
        $soundlists = SoundList::getSoundLists($list_id);

        foreach ($soundlists as $soundlist)
            if ($first_soundlist == NULL)
                $first_soundlist = $soundlist;
        return $first_soundlist;
    }
}
