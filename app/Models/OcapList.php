<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcapList extends Model
{
    /**
     * Table where ocaplist are stored
     * @var String
     */
    protected $table = 'ocap_lists';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public function contents()
    {
        return $this->belongsToMany(Facet::class);
    }
}
