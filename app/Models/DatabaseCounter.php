<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseCounter extends Model
{
    private $initialCounts = [];

    public function __construct(...$classes)
    {
        foreach ($classes as $class){
            $this->initialCounts[$class] = $class::all()->count();
        }
    }

    public function hasDiff($class, $value){
        return $value == $class::all()->count() - $this->initialCounts[$class];
    }
}
