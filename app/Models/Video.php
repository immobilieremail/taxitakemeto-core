<?php

namespace App\Models;

use App\Models\Media;

class Video extends Media
{
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->media_type = 'video';
    }

    public static function create(array $attributes = array())
    {
        $video = new Video($attributes);

        $video->save();
        return $video;
    }
}
