<?php

namespace App;

use App\SwissObject;

class Shell extends SwissObject
{
    public function audioListViewFacets() {
        return JoinShellViewFacet::where('id_shell', $this->swiss_number)->get()->toArray();
    }

    public function audioListEditFacets() {
        return JoinShellEditFacet::where('id_shell', $this->swiss_number)->get()->toArray();
    }

    public function getDropbox() {
        $join = JoinShellShellDropbox::where('id_shell', $this->swiss_number)->first();
        return $join->id_dropbox;
    }

    public function shellDropboxMessages() {
        $msg_array = array();
        $joins = JoinDropboxToMsg::all()->where('id_dropbox', $this->getDropbox());

        foreach ($joins as $join) {
            array_push($msg_array, ShellDropboxMessage::find($join->id_msg));
        }
        return $msg_array;
    }
}
