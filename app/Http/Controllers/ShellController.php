<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Shell;

class ShellController extends Controller
{
    public function store()
    {
        $shell = Shell::create();

        return response()->json(
            [
                'type' => 'ocap',
                'ocapType' => 'Shell',
                'url' => "/api/shell/$shell->swiss_number"
            ]
        );
    }

    public function show($shell_id)
    {
        $shell = Shell::findOrFail($shell_id);

        return response()->json(
            [
                'type' => 'Shell',
                'id' => $shell->swiss_number,
                'contents' => [
                    'audiolists_view' => "",
                    'audiolists_edit' => ""
                ]
            ]
        );
    }
}
