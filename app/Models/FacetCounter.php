<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacetCounter extends Model
{
    private $initialCounts = [];

    public static function count($type) {
        return Facet::where('type', $type)->count();
    }

    public function __construct(...$types)
    {
        foreach($types as $type) {
            $this->initialCounts[$type] = self::count($type);
        }
    }

    public function hasDiff($type, $value){
        return $value == self::count($type) - $this->initialCounts[$type];
    }
}
