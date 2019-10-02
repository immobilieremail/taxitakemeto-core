<?php

namespace App;

use App\AudioList;
use App\SwissObject;

class AudioListEditFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public function audioList()
    {
        return $this->belongsTo(AudioList::class, 'id_list');
    }

    public function shells()
    {
        return $this->morphToMany('App\Shell', 'join_audio_list');
    }

    public function getAudios()
    {
        return $this->audioList->getAudioViews();
    }

    public function getEditableAudios()
    {
        $audios = $this->getAudios();

        $audio_array = collect($audios)->map(function ($audio) {
            return $audio += [
                "update_audio" => "/api/audiolist/$this->swiss_number/audio/" . $audio["audio"]["audio_id"],
                "delete_audio" => "/api/audiolist/$this->swiss_number/audio/" . $audio["audio"]["audio_id"]
            ];
        });
        return $audio_array;
    }

    public function getJsonEditFacet()
    {
        return [
            'type' => 'AudioListEdit',
            'update' => route('audiolist.update', ['audiolist' => $this->swiss_number]),
            'view_facet' => route('audiolist.show', ['audiolist' => $this->audioList->viewFacet->swiss_number]),
            'contents' => $this->getAudios()
        ];
    }

    public function updateAudioList($new_audios)
    {
        $pos = 0;

        DB::beginTransaction();

        $this->audioList->audioViews()->detach();
        foreach ($new_audios as $new_audio) {
            $this->audioList->audioViews()->save($new_audio);
            $join = JoinAudio::where('audio_list_id', $this->id_list)
                ->where('join_audio_id', $new_audio->swiss_number)
                ->first();
            $join->pos = $pos++;
            $join->save();
        }

        DB::commit();
    }
}
