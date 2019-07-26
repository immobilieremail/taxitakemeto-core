<?php

namespace App\Http\Controllers;

use App\Shell,
    App\AudioList,
    App\JoinShellView,
    App\JoinShellEdit,
    App\AudioListEditFacet,
    App\AudioListViewFacet;

use Illuminate\Http\Request;

require_once __DIR__ . "/myfunctions/rand_nbr.php";
require_once __DIR__ . "/myfunctions/get_sound.php";


class IndexController extends Controller
{
    public function index($lang)
    {
        $shells = Shell::all();
        $array_edit = array();
        $array_view = array();

        foreach ($shells as $shell) {
            $edits = JoinShellEdit::all()->where('id_shell', $shell->id);
            foreach ($edits as $edit)
                array_push($array_edit, $edit->id_edit);
            $views = JoinShellView::all()->where('id_shell', $shell->id);
            foreach ($views as $view)
                array_push($array_view, $view->id_view);
        }
        return view('index', [
            'edits' => $array_edit,
            'views' => $array_view,
            'lang' => $lang]);
    }

    public function store($lang)
    {
        $list = new AudioList;
        $view = new AudioListViewFacet;
        $edit = new AudioListEditFacet;
        $shell = new Shell;
        $joinshllview = new JoinShellView;
        $joinshlledit = new JoinShellEdit;

        $list->save();
        $view_nbr = rand_large_nbr();
        $view->id = $view_nbr;
        $view->id_list = $list->id;
        $view->save();
        $edit_nbr = rand_large_nbr();
        $edit->id = $edit_nbr;
        $edit->id_list = $list->id;
        $edit->save();
        $shell_nbr = rand_large_nbr();
        $shell->id = $shell_nbr;
        $shell->save();
        $joinshllview->id_view = $view_nbr;
        $joinshllview->id_shell = $shell_nbr;
        $joinshllview->save();
        $joinshlledit->id_edit = $edit_nbr;
        $joinshlledit->id_shell = $shell_nbr;
        $joinshlledit->save();
        return redirect("$lang/upload-audio/$edit_nbr", 303);
    }
}
