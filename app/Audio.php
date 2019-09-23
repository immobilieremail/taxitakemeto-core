<?php

namespace App;

use App\SwissObject;

class Audio extends SwissObject
{
    protected $fillable = ['path'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        if (isset($attributes['extension']))
            $this->path = "$this->swiss_number." . $attributes['extension'];
    }

    public static function create(array $attributes = array())
    {
        $audio = new Audio($attributes);

        $audio->save();
        return $audio;
    }
}
