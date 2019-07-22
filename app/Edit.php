<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Edit extends Model
{
    protected $fillable = ['id_edit', 'id_view'];

    public static function getEdits($edit_id)
    {
        $edits = Edit::all()->where('id_edit', $edit_id);

        return $edits;
    }

    public static function getFirstEdit($edit_id)
    {
        $first_edit = Edit::where('id_edit', $edit_id)->first();

        return $first_edit;
    }

    public static function getViewNbr($edit_id)
    {
        $view_nbr = 0;
        $edit = Edit::getFirstEdit($edit_id);

        $view_nbr = $edit->id_view;
        return $view_nbr;
    }
}
