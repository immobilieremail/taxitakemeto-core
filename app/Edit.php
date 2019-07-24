<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Edit extends Model
{
    protected $fillable = ['id_edit', 'id_view'];

    public static function findByID($edit_id)
    {
        $first_edit = Edit::where('id_edit', $edit_id)->first();

        return $first_edit;
    }
}
