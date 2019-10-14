<?php

namespace App\Models;

use App\Extensions\SwissNumber;
use Illuminate\Database\Eloquent\Model;

abstract class SwissObject extends Model
{
    /**
     * Setup to use string primary keys
     * @var boolean
     */
    public      $incrementing   = false;
    protected   $keyType        = 'string';

    /**
     * Swiss model have string casted PrimaryKey
     * @var [type]
     */
    protected   $cast           = [
        'id'    => 'string'
    ];

    /**
     * Constructor to affect swiss number @ model creation
     * 
     * @param array $attributes [description]
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->id = swiss_number();
    }
}
