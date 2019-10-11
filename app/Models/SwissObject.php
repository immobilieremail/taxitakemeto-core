<?php

namespace App;

use App\Extensions\SwissNumber;
use Illuminate\Database\Eloquent\Model;

abstract class SwissObject extends Model
{

    public $incrementing = false;
    protected $primaryKey = 'swiss_number';
    protected $keyType = 'string';
    protected $table = 'facets';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->swiss_number = swiss_number();
    }
}
