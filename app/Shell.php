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
        $join = JoinShellShellDropbox::where('id_shell', $this->swiss_number)->first();
        return ShellDropboxMessage::where('id_receiver', $join->id_dropbox)->get()->toArray();
    }
}
