<?php

namespace App\Http\Controllers;

use App\Shell,
    App\AudioList,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    App\JoinShellEditFacet,
    App\JoinShellViewFacet;

use Illuminate\Http\Request;

class ShellController extends Controller
{
    public function index($lang)
    {
        $shells = Shell::all();

        return view('index', [
            'shells' => $shells,
            'lang' => $lang]);
    }

    public function show($lang, $shell_id)
    {
        $shell = Shell::find($shell_id);
        if ($shell == NULL)
        {
            return response(view('404'), 404);
        } else {
            return view('shell', [
                'lang' => $lang,
                'shell_id' => $shell_id,
                'views' => $shell->audioListViewFacets(),
                'edits' => $shell->audioListEditFacets()
            ]);
        }
    }

    public function store(Request $request, $lang)
    {
        $shell = Shell::create();

        return redirect("$lang/shell/$shell->swiss_number", 303);
    }

    public function new_audio_list(Request $request, $lang, $shell_id)
    {
        $audio_list = AudioList::create();
        $audio_list_edit_facet = AudioListEditFacet::create(['id_list' => $audio_list->id]);
        $join_shell_edit_facet = JoinShellEditFacet::create(['id_shell' => $shell_id, 'id_facet' => $audio_list_edit_facet->swiss_number]);
        return redirect()->route('audiolist.edit', [$lang, $audio_list_edit_facet->swiss_number]);
    }
}
