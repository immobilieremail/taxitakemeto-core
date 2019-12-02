<?php

namespace App\Models;

use Illuminate\Http\Request;

class PIEditFacet extends Facet
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->type = 'App\Models\PIEditFacet';
    }

    /**
     * Inverse relation of EditFacet for specific PI
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(PI::class);
    }

    public function has_show()
    {
        return true;
    }

    public function description()
    {
        return [
            'type' => 'PIViewFacet',
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'data' => [
                "title" => $this->target->title,
                "description" => $this->target->description,
                "medias" => []
            ]
        ];
    }
}
