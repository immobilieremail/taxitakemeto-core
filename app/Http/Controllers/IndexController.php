<?php

namespace App\Http\Controllers;

use App\Shell,
    App\AudioList,
    App\JoinShellView,
    App\JoinShellEdit,
    App\AudioListEditFacet,
    App\AudioListViewFacet;

use Illuminate\Http\Request;

require_once __DIR__ . "/myfunctions/rand_nbr.php";

class IndexController extends Controller
{
    public function index($lang)
    {
        $shells = Shell::all();

        return view('index', [
            'shells' => $shells,
            'lang' => $lang]);
    }

    public function store($lang)
    {
        $shell = new Shell;

        $shell_nbr = rand_large_nbr();
        $shell->id = $shell_nbr;
        $shell->save();
        return redirect("$lang/shell/$shell_nbr", 303);
    }
}
