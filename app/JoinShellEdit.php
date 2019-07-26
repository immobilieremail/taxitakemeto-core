<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinShellEdit extends Model
{
    protected $fillable = ['id_shell', 'id_edit'];

    public static function addToDB($shell_id, $edit_id)
    {
        try {
            $joinshlledit = new JoinShellEdit;

            $joinshlledit->id_shell = $shell_id;
            $joinshlledit->id_edit = $edit_id;
            $joinshlledit->save();
            return $joinshlledit;
        } catch (\Exception $e) {
            return null;
        }
    }
}