<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = ['id_view', 'id_list'];

    public static function findByID($view_id)
    {
        $first_view = View::where('id_view', $view_id)->first();

        return $first_view;
    }
}
