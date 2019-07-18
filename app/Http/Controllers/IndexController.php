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
        $edit = new Edit;
        $view = new View;
        $list = new SoundList;

        $list->save();
        $view_nbr = rand_large_nbr();
        $view->id_view = $view_nbr;
        $view->id_list = $list->id;
        $view->save();
        $edit_nbr = rand_large_nbr();
        $edit->id_edit = $edit_nbr;
        $edit->id_view = $view->id_view;
        $edit->save();
        return redirect('upload-audio/' . $edit_nbr);
    }

    public function update($queue_id)
    {
        $queue = QueueList::findOrFail($queue_id);

        echo $queue;
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
