<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinListSound extends Model
{
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
}
