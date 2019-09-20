<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SwissObject extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'swiss_number';
    protected $keyType = 'string';

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $forbidden_char = array("+", "/");
        $substitute_char = array("@", "_");
        $swiss_number = base64_encode(gmp_export(gmp_random_bits(128)));

        $swiss_number = str_replace($forbidden_char, $substitute_char, $swiss_number);
        $this->swiss_number = $swiss_number;
    }
}
