<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SwissObject extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'swiss_number';
    protected $keyType = 'string';

    public function __construct() {
        $this->swiss_number = base64_encode(gmp_export(gmp_random_bits(128)));
    }
}
