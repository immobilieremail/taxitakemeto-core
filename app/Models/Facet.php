<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Facet extends SwissObject
{
    /**
     * Specific table to use
     * @var string
     */
    protected   $table          = 'facets';
    
    /**
     * Fillable data
     * @var array
     */
    protected $fillable         = [
        'id', 'target_id', 'facet_type'
    ];

    /**
     * Constructor for eloquent model hierarchy
     * 
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

}
