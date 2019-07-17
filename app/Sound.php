<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sound extends Model
{
    public static function addToDB($id, $filename)
    {
        try {
            $sound = new Sound;

            $sound->id = $id;
            $sound->path = 'storage/uploads/' . $filename;
            $sound->save();
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }
}
