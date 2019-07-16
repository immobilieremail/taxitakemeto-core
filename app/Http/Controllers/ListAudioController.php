<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListAudioController extends Controller
{
    public function index()
    {
        $path = NULL;
        $audios = array();

        foreach (Storage::disk('local')->allFiles() as $file) {
            if (strpos($file, 'public/uploads/') !== false) {
                $path = str_replace('public/uploads/', '/storage/uploads/', $file);
                if ($audios !== NULL)
                    array_push($audios, $path);
                else
                    $audios = $path;
            }
        }
        return view('list-audio', ['audios' => $audios]);
    }
}
