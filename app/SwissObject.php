<?php

namespace App;

use App\Extensions\SwissNumber;
use Illuminate\Database\Eloquent\Model;

abstract class SwissObject extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'swiss_number';
    protected $keyType = 'string';

    // public function __construct(array $attributes = array()) {
    //     parent::__construct($attributes);
    //     $swiss_number = base64_encode(gmp_export(gmp_random_bits(128)));

    //     $swiss_number = str_replace(["+", "/"], ["@", "_"], $swiss_number);
    //     $this->swiss_number = $swiss_number;
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $swiss_number = new SwissNumber;
            $model->swiss_number = $swiss_number();
        });
    }
}
