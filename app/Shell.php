<?php

namespace App;

use App\SwissObject;

class Shell extends SwissObject
{
    public function audioListViewFacets() {
        return AudioListViewFacet::where('id_shell', $this->shell_id)->get()->toArray();
    }

    public function audioListEditFacets() {
        return AudioListEditFacet::where('id_shell', $this->shell_id)->get()->toArray();
    }
}
