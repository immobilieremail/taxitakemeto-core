<?php

namespace App;

use Illuminate\Support\Facades\DB;

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

    private function formatAudioListFacets($audiolist, $type)
    {
        $basic_url = "/api/audiolist/$audiolist->swiss_number";
        $complete_url = ($audiolist instanceof AudioListEditFacet) ?
            $basic_url . '/edit' : $basic_url;

        return [
            'type' => 'ocap',
            'ocapType' => $type,
            'url' => $complete_url
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

    public function getJsonShell()
    {
        return [
            'type' => 'Shell',
            'id' => $this->swiss_number,
            'update' => "/api/shell/" . $this->swiss_number,
            'contents' => [
                'audiolists_view' => $this->getAudioListViews(),
                'audiolists_edit' => $this->getAudioListEdits()
            ]
        ];
    }

    private function updateShellSetJoinPos($new_audiolist, $pos)
    {
        $join = JoinAudioList::all()
            ->where('shell_swiss_number', $this->swiss_number)
            ->where('join_audio_list_id', $new_audiolist->swiss_number)
            ->first();
        $join->pos = $pos;
        $join->save();
    }

    public function updateShell($new_audiolists)
    {
        $pos_edit = 0;
        $pos_view = 0;

        DB::beginTransaction();

        $this->audioListViews()->detach();
        $this->audioListEdits()->detach();
        foreach ($new_audiolists as $new_audiolist) {
            if ($new_audiolist instanceof AudioListEditFacet) {
                $this->audioListEdits()->save($new_audiolist);
                $this->updateShellSetJoinPos($new_audiolist, $pos_edit++);
            } else {
                $this->audioListViews()->save($new_audiolist);
                $this->updateShellSetJoinPos($new_audiolist, $pos_view++);
            }
        }

        DB::commit();
    }
}
