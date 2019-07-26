<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $fillable = ['id', 'path'];

    public static function addToDB($id, $filename)
    {
        try {
            $audio = new Audio;

            $audio->id = $id;
            $audio->path = '/storage/uploads/' . $filename;
            $audio->save();
            return $audio;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function deleteFromDB($id)
    {
        $audio = Audio::find($id);
        if ($audio !== NULL) {
            $audio->delete();
            return true;
        } else {
            return false;
        }
    }
}
