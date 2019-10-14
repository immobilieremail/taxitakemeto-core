<?php

namespace App\Models;

use App\Models\Media;

class Audio extends Media
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->media_type = 'audio';
    }

    public static function create(array $attributes = array())
    {
        $audio = new Audio($attributes);

        $audio->save();
        return $audio;
    }
}
