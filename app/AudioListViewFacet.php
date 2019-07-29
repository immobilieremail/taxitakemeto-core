<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudioListViewFacet extends Model
{
    protected $fillable = ['id', 'id_list', 'id_shell'];

    public static function addToDB($id, $list_id, $shell_id)
    {
        try {
            $view = new AudioListViewFacet;

            $view->id = $id;
            $view->id_list = $list_id;
            $view->id_shell = $shell_id;
            $view->save();
            return $view;
        } catch (\Exception $e) {
            return null;
        }
    }
}
