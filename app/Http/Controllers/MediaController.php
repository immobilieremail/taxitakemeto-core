<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Models\MediaEditFacet;
use App\Models\MediaViewFacet;
use App\Jobs\ConvertUploadedAudio;
use App\Jobs\ConvertUploadedImage;
use App\Jobs\ConvertUploadedVideo;

class MediaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $media_name = swiss_number() . '.' . $request->file('media')->extension();
        $path = $request->file('media')->storeAs('', $media_name, 'uploads');
        $mime_type = $request->file('media')->getMimeType();

        $media_type = [];
        preg_match('#(^[a-z])\w+#', $mime_type, $media_type);

        $media = Media::create(['path'=>$path, 'media_type'=>$media_type[0]]);
        $media->editFacet()->save(new MediaEditFacet);
        $media->viewFacet()->save(new MediaViewFacet);

        if ($media_type[0] == 'audio'){
            $this->dispatch(new ConvertUploadedAudio($media));
        }
        elseif ($media_type[0] == 'video') {
            $this->dispatch(new ConvertUploadedVideo($media));
        }
        elseif ($media_type[0] == 'image') {
            $this->dispatch(new ConvertUploadedImage($media));
        }
        return response('Success !', 200);
    }
}
