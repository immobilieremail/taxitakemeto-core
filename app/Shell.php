<?php

namespace App;

use App\SwissObject;

class Shell extends SwissObject
{
    public function audioListEdits()
    {
        return $this->morphedByMany('App\AudioListEditFacet', 'join_audio_list')->withPivot('pos');
    }

    public function audioListViews()
    {
        return $this->morphedByMany('App\AudioListViewFacet', 'join_audio_list')->withPivot('pos');
    }
}
