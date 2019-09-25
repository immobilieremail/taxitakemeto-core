<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Audio,
    App\AudioListEditFacet,
    App\AudioListViewFacet;

use App\Jobs\ConvertUploadedAudio;

use App\Http\Requests\NewAudioRequest;

class AudioController extends Controller
{
    // need dispatch method from Controller
    private function convert(Audio $audio)
    {
        $this->dispatch(new ConvertUploadedAudio($audio));
        $audio->path = "$audio->swiss_number.mp3";
        $audio->save();
    }

    public function store(NewAudioRequest $request, $audiolist)
    {
        $edit_facet = AudioListEditFacet::find($audiolist);

        if ($edit_facet != NULL) {
            if ($request->has('audio')) {
                $extension = $request->file('audio')->extension();
                $audio = $edit_facet->addAudio($extension);
                $request->file('audio')->storeAs('storage/uploads',
                                                 "$audio->swiss_number.$extension", 'public');
                $this->convert($audio);

                return response()->json(
                    [
                        "type" => "Audio",
                        "audio_id" => $audio->swiss_number,
                        "path_to_file" => $audio->path
                    ]
                );
            } else {
                abort(400);
            }
        } else
            abort(404);
    }

    public function update(NewAudioRequest $request, $facet_id, $audio_id)
    {
        $audio = Audio::find($audio_id);
        $edit_facet = AudioListEditFacet::find($facet_id);
        $condition = $edit_facet != NULL && $audio != NULL;

        if ($condition) {
            $extension = $request->file('audio')->extension();
            $request->file('audio')->storeAs('storage/uploads',
                "$audio->swiss_number.$extension", 'public');
            $audio->path = "$audio->swiss_number.$extension";
            $audio->save();
            $this->convert($audio);
            return response()->json(
                [
                    "type" => "Audio",
                    "audio_id" => $audio->swiss_number,
                    "path_to_file" => $audio->path
                ]
            );
        } else
            abort(404);
    }

    public function destroy($facet_id, $audio_id)
    {
        $audio = Audio::find($audio_id);
        $condition = $audio != NULL
            && AudioListEditFacet::find($facet_id) != NULL
            && Storage::disk('converts')->exists($audio->path);

        if ($condition) {
            Storage::disk('converts')->delete($audio->path);
            $audio->delete();
            return response()->json(
                [
                    'status' => 200
                ]
            );
        } else
            abort(404);
    }
}
