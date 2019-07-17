<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Edit extends Model
{
    public static function getEdits($edit_id)
    {
        $edits = Edit::all()->where('id_edit', $edit_id);

        return $edits;
    }

    public static function getFirstEdit($edit_id)
    {
        $first_edit = NULL;
        $edits = Edit::getEdits($edit_id);

        foreach ($edits as $edit)
            $first_edit = $edit;
        return $first_edit;
    }

    public static function getViewNbr($edit_id)
    {
        $view_nbr = 0;
        $edits = Edit::getEdits($edit_id);

        foreach ($edits as $edit)
            $view_nbr = $edit->id_view;
        return $view_nbr;
    }
}
