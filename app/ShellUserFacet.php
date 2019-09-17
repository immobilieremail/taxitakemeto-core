<?php

namespace App;

use App\SwissObject;

use Illuminate\Support\Facades\DB;

class ShellUserFacet extends SwissObject
{
    protected $fillable = ['id_shell'];

    public static function create(Array $param)
    {
        $obj = new ShellUserFacet;

        $obj->id_shell = $param["id_shell"];
        $obj->save();
        return $obj;
    }

    public function getJsonShell()
    {
        $shell = Shell::find($this->id_shell);

        return [
            'type' => 'Shell',
            'update' => "/api/shell/" . $this->swiss_number,
            'contents' => [
                'audiolists_view' => $shell->getAudioListViews(),
                'audiolists_edit' => $shell->getAudioListEdits()
            ]
        ];
    }

    private function updateShellSetJoinPos($new_audiolist, $pos)
    {
        $join = JoinAudioList::all()
            ->where('shell_id', $this->id_shell)
            ->where('join_audio_list_id', $new_audiolist->swiss_number)
            ->first();
        $join->pos = $pos;
        $join->save();
    }

    public function updateShell($new_audiolists)
    {
        $pos_edit = 0;
        $pos_view = 0;
        $shell = Shell::find($this->id_shell);

        DB::beginTransaction();

        $shell->audioListViews()->detach();
        $shell->audioListEdits()->detach();
        foreach ($new_audiolists as $new_audiolist) {
            if ($new_audiolist instanceof AudioListEditFacet) {
                $shell->audioListEdits()->save($new_audiolist);
                $this->updateShellSetJoinPos($new_audiolist, $pos_edit++);
            } else {
                $shell->audioListViews()->save($new_audiolist);
                $this->updateShellSetJoinPos($new_audiolist, $pos_view++);
            }
        }

        DB::commit();
    }
}
