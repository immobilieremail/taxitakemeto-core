<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Audio,
    App\AudioEditFacet,
    App\AudioViewFacet;

use App\Jobs\ConvertUploadedAudio;

use App\Http\Requests\NewAudioRequest;

class AudioController extends Controller
{
    // need dispatch method from Controller
    private function convert(Audio $audio)
    {
        $this->dispatch(new ConvertUploadedAudio($audio));
        $audio->path = "$audio->id.mp3";
        $audio->save();
    }

    public function store(NewAudioRequest $request)
    {
        if ($request->has('audio')) {
            $extension = $request->file('audio')->extension();
            $audio = Audio::create(['extension' => $extension]);
            $audio_edit = AudioEditFacet::create(["id_audio" => $audio->id]);
            $audio_view = AudioViewFacet::create(["id_audio" => $audio->id]);

            $request->file('audio')->storeAs('storage/uploads',
                "$audio->id.$extension", 'public');
            $this->convert($audio);
            return response()->json(
                [
                    "type" => "ocap",
                    "ocapType" => "Audio",
                    "url" => "http://localhost:8000/api/audio/$audio_edit->swiss_number"
                ]
            );
        } else
            abort(400);
    }

    public function destroy($audio_id)
    {
        $audio_edit = AudioEditFacet::find($audio_id);
        $audio = ($audio_edit != NULL) ?
            Audio::find($audio_edit->id_audio) : NULL;
        $condition = $audio != NULL
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
