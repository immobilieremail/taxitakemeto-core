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
}
