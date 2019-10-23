<?php

namespace App;

use App\SwissObject;

use Illuminate\Support\Facades\DB;

class ShellUserFacet extends SwissObject
{
    protected $fillable = ['id_shell'];

    public function shell()
    {
        return $this->belongsTo(Shell::class, 'id_shell');
    }

    public function getJsonShell()
    {
        return [
            'type' => 'Shell',
            'dropbox' => route('shell.send', ['shell' => $this->shell->dropboxFacet->swiss_number]),
            'update' =>  route('shell.update', ['shell' => $this->swiss_number]),
            'contents' => [
                'audiolists_view' => $this->shell->getAudioListViews(),
                'audiolists_edit' => $this->shell->getAudioListEdits()
            ]
        ];
    }

    private function updateShellSetJoinPos($new_audiolist, $pos)
    {
        $join = JoinAudioList::where('shell_id', $this->id_shell)
            ->where('join_audio_list_id', $new_audiolist->swiss_number)
            ->first();
        $join->pos = $pos;
        $join->save();
    }

    public function updateShell($new_audiolists)
    {
        $pos_edit = 0;
        $pos_view = 0;

        DB::beginTransaction();

        $this->shell->audioListViews()->detach();
        $this->shell->audioListEdits()->detach();
        foreach ($new_audiolists as $new_audiolist) {
            if ($new_audiolist instanceof AudioListEditFacet) {
                $this->shell->audioListEdits()->save($new_audiolist);
                $this->updateShellSetJoinPos($new_audiolist, $pos_edit++);
            } else if ($new_audiolist instanceof AudioListViewFacet) {
                $this->shell->audioListViews()->save($new_audiolist);
                $this->updateShellSetJoinPos($new_audiolist, $pos_view++);
            }
        }

        DB::commit();
    }
}
