<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

require_once __DIR__ . "/Http/Controllers/myfunctions/rand_nbr.php";

class QueueList extends Model
{
    protected $fillable = ['id', 'id_list', 'id_sound', 'path'];

    public static function insertIntoDB($id_list, $id_sound, $filename)
    {
        $return_value = QueueList::create([
            'id' => rand_large_nbr(),
            'id_list' => $id_list,
            'id_sound' => $id_sound,
            'path' => $filename
        ]);
        if (empty($return_value))
            return false;
        return true;
    }
}
