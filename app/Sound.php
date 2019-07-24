<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sound extends Model
{
    protected $fillable = ['id', 'path'];

    public static function addToDB($id, $filename)
    {
        try {
            $sound = new Sound;

            $sound->id = $id;
            $sound->path = '/storage/uploads/' . $filename;
            $sound->save();
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function deleteFromDB($id)
    {
        $sound = Sound::find($id);
        if ($sound !== NULL) {
            $sound->delete();
            return true;
        } else {
            return false;
        }
    }
}
