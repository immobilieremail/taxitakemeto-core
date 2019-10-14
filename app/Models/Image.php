<?php

namespace App\Models;

use App\Models\Media;

class Image extends Media
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->media_type = 'image';
    }

    public static function create(array $attributes = array())
    {
        $image = new Image($attributes);

        $image->save();
        return $image;
    }
}
