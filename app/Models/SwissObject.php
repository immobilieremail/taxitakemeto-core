<?php

namespace App;

use App\Extensions\SwissNumber;
use Illuminate\Database\Eloquent\Model;

abstract class SwissObject extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'swiss_number';
    protected $keyType = 'string';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $swiss_number = new SwissNumber;
        $this->swiss_number = $swiss_number();
    }
}
