<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    /**
     * Table where travels are stored
     * @var String
     */
    protected $table = 'travels';

    /**
     * Model fillable data
     *
     * @var Array
     */
    protected $fillable = [
        'title'
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
