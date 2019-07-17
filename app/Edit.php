<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Edit extends Model
{
    public static function getEdits($edit_nbr)
    {
        $edits = Edit::all()->where('id_edit', $edit_nbr);

        return $edits;
    }

    public static function getViewNbr($edit_nbr)
    {
        $view_nbr = 0;
        $edits = Edit::getEdits($edit_nbr);

        foreach ($edits as $edit)
            $view_nbr = $edit->id_view;
        return $view_nbr;
    }
}
