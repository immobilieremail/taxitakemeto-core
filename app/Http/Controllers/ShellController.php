<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Shell;
use App\Models\ShellUserFacet;

class ShellController extends Controller
{
    public function store()
    {
        $shell = Shell::create();
        $shell->userFacet()->save(new ShellUserFacet);

        return response()->json([
            'type' => 'ocap',
            'ocapType' => 'ShellUserFacet',
            'url' => route('obj.show', ['obj' => $shell->userFacet->id])
        ]);
    }
}
