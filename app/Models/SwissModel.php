<?php

namespace App\Models;

use App\Extensions\SwissNumber;
use Illuminate\Database\Eloquent\Model;

abstract class SwissModel extends Model
{
    protected $casts = [
        'id' => 'string'
    ];
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating( function($model) {
            $swiss_number = new SwissNumber;
            $model->id = $swiss_number();
        });
    }
}