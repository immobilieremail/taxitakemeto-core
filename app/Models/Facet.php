<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MannikJ\Laravel\SingleTableInheritance\Traits\SingleTableInheritance;

class Facet extends Model
{
    use SingleTableInheritance;

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
     * Facet method permissions
     * @var array
     */
    protected $permissions      = [];

    /**
     * Constructor for eloquent model hierarchy
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->ensureTypeCharacteristics();
        $this->setSwissNumber();
    }

    public function setSwissNumber()
    {
        $this->id = swissNumber();
    }

    /**
     * Check if Facet has permissions for specific request method
     *
     * @return bool permission
     */
    public function has_access(String $method): bool
    {
        return in_array($method, $this->permissions, true);
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
}
