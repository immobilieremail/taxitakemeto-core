<?php

namespace App\Http\Controllers;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\QueueList,
    App\JoinListSound,
    Illuminate\Http\Request;

require_once __DIR__ . "/myfunctions/rand_nbr.php";
require_once __DIR__ . "/myfunctions/get_sound.php";


class IndexController extends Controller
{
    public function index()
    {
        $edits = Edit::all();
        $queues = QueueList::all();

        return view('index', ['lists' => $edits, 'queues' => $queues]);
    }

    public function store()
    {
        $view_nbr = rand_large_nbr();
        $edit_nbr = rand_large_nbr();

        $list = SoundList::create();
        $view = View::create([
            'id_view' => $view_nbr,
            'id_list' => $list->id
        ]);
        $edit = Edit::create([
            'id_edit' => $edit_nbr,
            'id_view' => $view_nbr
        ]);
        return redirect('upload-audio/' . $edit_nbr);
    }

    public function update($queue_id)
    {
        $queue = QueueList::findOrFail($queue_id);

        $return_value = Sound::addToDB($queue->id_sound, $queue->path);
        if ($return_value !== true)
            return back();
        $return_value = JoinListSound::addToDB($queue->id_sound, $queue->id_list);
        if ($return_value !== true)
            return back();
        QueueList::findOrFail($queue_id)->delete();
        return back();
    }

    public function destroy($queue_id)
    {
        QueueList::findOrFail($queue_id)->delete();
        return back();
    }
}
