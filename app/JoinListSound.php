<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinListSound extends Model
{
    protected $fillable = ['id_list', 'id_sound'];

    public static function addToDB($sound_nbr, $soundlist_nbr)
    {
        try {
            $joinlstsnd = new JoinListSound;

            $joinlstsnd->id_list = $soundlist_nbr;
            $joinlstsnd->id_sound = $sound_nbr;
            $joinlstsnd->save();
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function countJoinListSounds()
    {
        $count = 0;
        $joinlstsnds = JoinListSound::all();

        foreach ($joinlstsnds as $joinlstsnd)
            $count += 1;
        return $count;
    }
}
