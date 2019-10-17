<?php

namespace App\Jobs;

use FFMpeg;

use App\Models\Media;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Bus\Queueable;

use FFMpeg\Coordinate\Dimension;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertUploadedAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $audio;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->audio = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $audio_path = swiss_number();
        $bitRateFormat = (new Mp3)->setAudioKiloBitrate(256); // create a file format

        FFMpeg::fromDisk('uploads') // open the uploaded audio from the right disk
            ->open($this->audio->path)
            ->export()
            ->toDisk('converts') // tell the Exporter to which disk we want to export
            ->inFormat($bitRateFormat)
            ->save("$audio_path.mp3");

        Storage::disk('uploads')
            ->delete($this->audio->path);

        $this->audio->path = "$audio_path.mp3";
        $this->audio->save();
    }
}
