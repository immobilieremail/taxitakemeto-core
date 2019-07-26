<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinListAudio extends Model
{
    protected $fillable = ['id_list', 'id_audio'];

    public static function addToDB($audio_nbr, $audiolist_nbr)
    {
        try {
            $joinlstaudio = new JoinListAudio;

            $joinlstaudio->id_list = $audiolist_nbr;
            $joinlstaudio->id_audio = $audio_nbr;
            $joinlstaudio->save();
            return $joinlstaudio;
        } catch (\Exception $e) {
            return null;
        }
    }
}
