<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudioList extends Model
{
    protected $fillable = ['id'];

    public function audioEdits()
    {
        return $this->morphedByMany('App\AudioEditFacet', 'join_audio');
    }

    public function audioViews()
    {
        return $this->morphedByMany('App\AudioViewFacet', 'join_audio')->withPivot('pos');
    }

    private function formatAudioFacets($audio, $type)
    {
        return [
            'type' => 'ocap',
            'ocapType' => $type,
            'url' => "/api/audio/$audio->swiss_number"
        ];
    }

    private function getAudioFacets($audios, String $facet_type)
    {
        return array_map(function ($audio, $type) {
            return $this->formatAudioFacets($audio, $type);
        }, $audios, array_fill(0, count($audios), $facet_type));
    }

    public function getAudioViews()
    {
        $audios = $this->audioViews->sortBy(function ($audio, $key) {
            return $audio->pivot->pos;
        })->values()->all();

        return $this->getAudioFacets($audios, "AudioViewFacet");
    }

    public function getAudioEdits()
    {
        $audios = $this->audioEdits->sortBy(function ($audio, $key) {
            return $audio->pivot->pos;
        })->values()->all();

        return $this->getAudioFacets($audios, "AudioEditFacet");
    }
}
