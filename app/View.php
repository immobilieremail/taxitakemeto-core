<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    public static function getSoundListNbr($view_nbr)
    {
        $soundlist_nbr = 0;
        $views = View::all()->where('id_view', $view_nbr);

        foreach ($views as $views)
            $soundlist_nbr = $views->id_list;
        return $soundlist_nbr;
    }
}
