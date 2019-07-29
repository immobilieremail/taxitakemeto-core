<?php

namespace App\Http\Controllers;

use App\Shell,
    App\AudioList,
    App\AudioListEditFacet,
    App\AudioListViewFacet;

use Illuminate\Http\Request;

require_once __DIR__ . "/myfunctions/rand_nbr.php";

class ShellController extends Controller
{
    private function linkAudioListToShell($list_id, $shell_id)
    {
        $view = new AudioListViewFacet;
        $edit = new AudioListEditFacet;

        $view_nbr = rand_large_nbr();
        $view->id = $view_nbr;
        $view->id_list = $list_id;
        $view->id_shell = $shell_id;
        $view->save();
        $edit_nbr = rand_large_nbr();
        $edit->id = $edit_nbr;
        $edit->id_list = $list_id;
        $edit->id_shell = $shell_id;
        $edit->save();
        return $edit_nbr;
    }

    public function index($lang, $shell_id)
    {
        $edits = NULL;
        $edits_array = array();
        $views_array = array();

        if (Shell::find($shell_id) == NULL)
            return response(view('404'), 404);
        $edits = AudioListEditFacet::all()->where('id_shell', $shell_id);
        foreach ($edits as $edit) {
            array_push($edits_array, $edit);
            $view = AudioListViewFacet::where('id_list', $edit->id_list)->first();
            array_push($views_array, $view);
        }
        return view('shell', [
            'lang' => $lang,
            'shell_id' => $shell_id,
            'edits' => $edits_array,
            'views' => $views_array
        ]);
    }

    public function store(Request $request, $lang, $shell_id)
    {
        $list = new AudioList;

        $list->save();
        $edit_nbr = $this->linkAudioListToShell($list->id, $shell_id);
        return redirect("$lang/upload-audio/$edit_nbr", 303);
    }
}
