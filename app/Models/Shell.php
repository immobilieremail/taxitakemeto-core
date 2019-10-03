<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Shell extends Model
{
    public function userFacet()
    {
        return $this->HasOne(ShellUserFacet::class, 'id_shell', 'id');
    }

    public function dropboxFacet()
    {
        return $this->HasOne(ShellDropboxFacet::class, 'id_shell', 'id');
    }

    public function audioListEdits()
    {
        return $this->morphedByMany('App\AudioListEditFacet', 'join_audio_list')->withPivot('pos');
    }

    public function audioListViews()
    {
        return $this->morphedByMany('App\AudioListViewFacet', 'join_audio_list')->withPivot('pos');
    }

    private function formatAudioListFacets($audiolist, $type)
    {
        $url = ($audiolist instanceof AudioListEditFacet) ?
            'audiolist.edit' : 'audiolist.show';

        return [
            'type' => 'ocap',
            'ocapType' => $type,
            'url' => route($url, ['audiolist' => $audiolist->swiss_number])
        ];
    }

    private function getAudioListFacets($audios, String $facet_type)
    {
        return array_map(function ($audio, $type) {
            return $this->formatAudioListFacets($audio, $type);
        }, $audios, array_fill(0, count($audios), $facet_type));
    }

    public function getAudioListViews()
    {
        $audios = $this->audioListViews->sortBy(function ($audio, $key) {
            return $audio->pivot->pos;
        })->values()->all();

        return $this->getAudioListFacets($audios, "AudioListView");
    }

    public function getAudioListEdits()
    {
        $audios = $this->audioListEdits->sortBy(function ($audio, $key) {
            return $audio->pivot->pos;
        })->values()->all();

        return $this->getAudioListFacets($audios, "AudioListEdit");
    }
}
