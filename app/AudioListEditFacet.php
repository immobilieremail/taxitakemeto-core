<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudioListEditFacet extends Model
{
    protected $fillable = ['id', 'id_list', 'id_shell'];

    public $incrementing = false;

    public static function addToDB($id, $list_id, $shell_id)
    {
        try {
            $edit = new AudioListEditFacet;

            $edit->id = $id;
            $edit->id_list = $list_id;
            $edit->id_shell = $shell_id;
            $edit->save();
            return $edit;
        } catch (\Exception $e) {
            return null;
        }
    }
}
