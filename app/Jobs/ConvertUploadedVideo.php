<?php

namespace App\Jobs;

use FFMpeg;

use App\Models\Media;

use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Filters\Audio\SimpleFilter;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertUploadedVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->video = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video_name = swissNumber();
        $ffprobe = \FFMpeg\FFProbe::create();
        $video_path = Storage::disk('uploads')->getAdapter()->getPathPrefix() . $this->video->path;
        $bit_rate = $ffprobe->format($video_path)->get('bit_rate') / 1024; // get bit rate in Kb
        $bitRateFormat = (new X264('aac', 'libx264'))->setKiloBitrate(($bit_rate > 500) ? 500 : $bit_rate); // create a file format

        FFMpeg::fromDisk('uploads') // open the uploaded audio from the right disk
            ->open($this->video->path)
            ->export()
            ->toDisk('converts') // tell the Exporter to which disk we want to export
            ->inFormat($bitRateFormat)
            ->save("$video_name.mp4");

        Storage::disk('uploads')
            ->delete($this->video->path);

        $this->video->path = "$video_name.mp4";
        $this->video->save();
    }
}
