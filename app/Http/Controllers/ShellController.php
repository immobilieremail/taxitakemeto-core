<?php

namespace App\Http\Controllers;

use App\Shell,
    App\AudioList,
    App\ShellDropbox,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    App\JoinShellEditFacet,
    App\JoinShellViewFacet,
    App\JoinShellShellDropbox;

use Illuminate\Http\Request;

class ShellController extends Controller
{
    public function index($lang)
    {
        $shells = Shell::all();
        $shell_array = array();

        foreach ($shells as $shell) {
            array_push($shell_array, [
                "swiss_number" => $shell->swiss_number,
                "dropbox" => $shell->getDropbox()
            ]);
        }
        return view('index', [
            'shells' => $shell_array,
            'lang' => $lang]);
    }

    public function show($lang, $shell_id)
    {
        $shell = Shell::find($shell_id);
        if ($shell == NULL) {
            return response(view('404'), 404);
        } else {
            return view('shell', [
                'lang' => $lang,
                'shell_id' => $shell_id,
                'views' => $shell->audioListViewFacets(),
                'edits' => $shell->audioListEditFacets(),
                'dropbox' => $shell->shellDropboxMessages(),
            ]);
        }
    }

    public function store(Request $request, $lang)
    {
        $shell = Shell::create();
        $dropbox = ShellDropbox::create();
        $join_shell_shell_dropbox = JoinShellShellDropbox::create([
            'id_shell' => $shell->swiss_number,
            'id_dropbox' => $dropbox->swiss_number
        ]);

        return redirect("$lang/shell/$shell->swiss_number", 303);
    }

    public function new_audio_list(Request $request, $lang, $shell_id)
    {
        $audio_list = AudioList::create();
        $audio_list_edit_facet = AudioListEditFacet::create(['id_list' => $audio_list->id]);
        $join_shell_edit_facet = JoinShellEditFacet::create(['id_shell' => $shell_id, 'id_facet' => $audio_list_edit_facet->swiss_number]);
        return redirect()->route('audiolist.edit', [$lang, $audio_list_edit_facet->swiss_number]);
    }

    public function accept($lang, $shell_id, $msg_id)
    {

    }
}
