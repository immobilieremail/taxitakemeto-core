<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MannikJ\Laravel\SingleTableInheritance\Traits\SingleTableInheritance;

class Facet extends SwissObject
{
    use SingleTableInheritance;

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
        'id', 'target_id', 'type'
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

    /**
     * Inverse of relationship
     *
     * @return [type] [description]
     */
    public function target()
    {
        return null;
    }

    public function has_index()
    {
        return false;
    }

    public function has_store()
    {
        return false;
    }

    public function has_create()
    {
        return false;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function has_show()
    {
        return false;
    }

    public function has_update()
    {
        return false;
    }

    public function has_destroy()
    {
        return false;
    }

    public function has_edit()
    {
        return false;
    }
}
