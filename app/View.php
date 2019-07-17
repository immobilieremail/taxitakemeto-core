<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    public static function getViews($view_id)
    {
        $views = View::all()->where('id_view', $view_id);

        return $views;
    }

    public static function getFirstView($view_id)
    {
        $first_view = NULL;
        $views = View::getViews($view_id);

        foreach ($views as $view)
            $first_view = $view;
        return $first_view;
    }

    public static function getSoundListNbr($view_id)
    {
        $soundlist_nbr = 0;
        $views = View::getViews($view_id);

        foreach ($views as $view)
            $soundlist_nbr = $view->id_list;
        return $soundlist_nbr;
    }
}
