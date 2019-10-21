<?php

namespace App\Jobs;

use App\Models\Media;
use Spatie\Glide\GlideImage;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertUploadedImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $image;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->image = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $image_name = swiss_number();
        $upload_path = Storage::disk('uploads')->getAdapter()->getPathPrefix();
        $convert_path = Storage::disk('converts')->getAdapter()->getPathPrefix();

        GlideImage::create($upload_path . $this->image->path)
            ->modify(['fm'=>'jpg'])
            ->save($convert_path . $image_name . '.jpg');

        Storage::disk('uploads')
            ->delete($this->image->path);

        $this->image->path = "$image_name.jpg";
        $this->image->save();
    }
}
