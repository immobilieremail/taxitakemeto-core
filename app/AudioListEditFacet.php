<?php

namespace App;

use Illuminate\Support\Facades\DB;

use App\SwissObject;

class AudioListEditFacet extends SwissObject
{
    protected $fillable = ['id_list'];

    public static function create(Array $param)
    {
        $obj = new AudioListEditFacet;

        $obj->id_list = $param["id_list"];
        $obj->save();
        return $obj;
    }

    public function getAudios()
    {
        $audiolist = AudioList::find($this->id_list);

        return $audiolist->getAudioViews();
    }

    public function getViewFacet()
    {
        return AudioListViewFacet::where('id_list', $this->id_list)->first();
    }

    public function getJsonViewFacet()
    {
        return [
            "type" => 'AudioListEdit',
            "contents" => $this->getAudios()
        ];
    }

    public function getJsonEditFacet()
    {
        return [
            'type' => 'AudioListEdit',
            'update' => "/api/audiolist/" . $this->swiss_number,
            'view_facet' => "/api/audiolist/" . $this->getViewFacet()->swiss_number,
            'contents' => $this->getAudios()
        ];
    }

    public function updateAudioList($new_audios)
    {
        $pos = 0;
        $audiolist = AudioList::find($this->id_list);

        DB::beginTransaction();

        $audiolist->audioViews()->detach();
        foreach ($new_audios as $new_audio) {
            $audiolist->audioViews()->save($new_audio);
            $join = JoinAudio::all()
                ->where('audio_list_id', $audiolist->id)
                ->where('join_audio_id', $new_audio->swiss_number)
                ->first();
            $join->pos = $pos++;
            $join->save();
        }

        DB::commit();
    }
}
