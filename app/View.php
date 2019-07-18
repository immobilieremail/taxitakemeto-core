<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = ['id_view', 'id_list'];

    public static function getViews($view_id)
    {
        $views = View::all()->where('id_view', $view_id);

        return $views;
    }

    public static function getFirstView($view_id)
    {
        $first_view = View::where('id_view', $view_id)->first();

        return $first_view;
    }

    public static function getSoundListNbr($view_id)
    {
        $soundlist_nbr = 0;
        $view = View::getFirstView($view_id);

        $soundlist_nbr = $view->id_list;
        return $soundlist_nbr;
    }
}
