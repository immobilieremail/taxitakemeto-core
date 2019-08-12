<?php

namespace App\Http\Controllers;

use App\Shell,
    App\AudioList,
    App\ShellDropbox,
    App\JoinDropboxToMsg,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    App\JoinShellEditFacet,
    App\JoinShellViewFacet,
    App\ShellDropboxMessage,
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

    private function accept_facet($class_facet, $class_join, $shell_dropbox_message, $shell)
    {
        $audiolist_facet = $class_facet::find($shell_dropbox_message->capability);
        $join_shell_facet = $class_join::create([
            'id_shell' => $shell->swiss_number,
            'id_facet' => $audiolist_facet->swiss_number
        ]);
        JoinDropboxToMsg::where('id_msg', $shell_dropbox_message->swiss_number)->delete();
    }

    public function accept($lang, $shell_id, $msg_id)
    {
        $param = array();
        $shell = Shell::find($shell_id);
        $shell_dropbox_message = ShellDropboxMessage::find($msg_id);

        if ($shell != NULL) {
            if ($shell_dropbox_message != NULL) {
                if ($shell_dropbox_message->type == "ROFAL") {
                    $this->accept_facet(AudioListViewFacet::class,
                        JoinShellViewFacet::class, $shell_dropbox_message, $shell);
                } else if ($shell_dropbox_message_type == "RWFAL") {
                    $this->accept_facet(AudioListEditFacet::class,
                        JoinShellEditFacet::class, $shell_dropbox_message, $shell);
                } else
                    return response(view('404'), 404);
                return back();
            }
        }
        return response(view('404'), 404);
    }
}
