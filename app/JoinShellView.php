<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinShellView extends Model
{
    protected $fillable = ['id_shell', 'id_view'];

    public static function addToDB($shell_id, $view_id)
    {
        try {
            $joinshlledit = new JoinShellEdit;

            $joinshlledit->id_shell = $shell_id;
            $joinshlledit->id_view = $view_id;
            $joinshlledit->save();
            return $joinshlledit;
        } catch (\Exception $e) {
            return null;
        }
    }
}