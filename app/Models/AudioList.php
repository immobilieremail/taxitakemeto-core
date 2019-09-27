<?php

namespace App;

use App\Audio;
use App\AudioListEditFacet;

use App\AudioListViewFacet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AudioList extends Model
{
    protected $fillable = ['id'];

    public function audios()
    {
        return $this->hasMany(Audio::class);
    }

    public function viewFacet()
    {
        return $this->hasOne(AudioListViewFacet::class, 'id_list', 'id');
    }

    public function editFacet()
    {
        return $this->hasOne(AudioListEditFacet::class, 'id_list', 'id');
    }

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
            'url' => route(($type == "AudioEdit") ?
                'audio.edit' : 'audio.show', ['audio' => $audio->swiss_number])
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

        return $this->getAudioFacets($audios, "AudioView");
    }

    public function getAudioEdits()
    {
        $audios = $this->audios;

        $audio_array = collect($audios)->map(function ($audio) {
            return [
                'audio' => [
                    'type' => 'Audio',
                    'audio_id' => $audio->swiss_number,
                    'path_to_file' => $audio->path
                ]
            ];
        });
        return $audio_array;
    }
}
