<?php

namespace App\Models;

use App\Models\MediaView;
use App\Models\MediaEdit;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'path', 'media_type'
    ];

    public function viewFacet()
    {
        return $this->morphOne(MediaView::class, 'target');
    }

    public function editFacet()
    {
        return $this->morphOne(MediaEdit::class, 'target');
    }
}
