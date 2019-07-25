<?php

namespace App\Http\Controllers;

use App\Edit;
use App\View;
use App\SoundList;
use Illuminate\Http\Request;

require_once __DIR__ . "/myfunctions/rand_nbr.php";
require_once __DIR__ . "/myfunctions/get_sound.php";


class IndexController extends Controller
{
    public function index($lang)
    {
        $edits = Edit::all();

        return view('index', ['lists' => $edits, 'lang' => $lang]);
    }

    public function store($lang)
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
        return redirect("$lang/upload-audio/$edit_nbr", 303);
    }
}
