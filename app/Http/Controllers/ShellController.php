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
        $view_id = rand_large_nbr();
        $edit_id = rand_large_nbr();
        $view = AudioListViewFacet::addToDb($view_id, $list_id, $shell_id);
        $edit = AudioListEditFacet::addToDb($edit_id, $list_id, $shell_id);
        return $edit_id;
    }

    private function sortViews($views, $edits)
    {
        for ($indx_e = 0; isset($edits[$indx_e]); $indx_e++) {
            for ($indx_v = 0; isset($views[$indx_v]); $indx_v++) {
                if ($views[$indx_v]->id_list == $edits[$indx_e]->id_list) {
                    array_splice($views, $indx_v, 1);
                    break;
                }
            }
        }
        return $views;
    }

    public function index($lang, $shell_id)
    {
        $edits_array = array();
        $views_array = array();

        if (Shell::find($shell_id) == NULL)
            return response(view('404'), 404);
        $edits = AudioListEditFacet::all()->where('id_shell', $shell_id);
        foreach ($edits as $edit)
            array_push($edits_array, $edit);
        $views = AudioListViewFacet::all()->where('id_shell', $shell_id);
        foreach ($views as $view)
            array_push($views_array, $view);
        $views_array = $this->sortViews($views_array, $edits_array);
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
