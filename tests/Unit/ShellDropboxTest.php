<?php

namespace Tests\Unit;

use App\Shell,
    App\AudioList,
    App\ShellDropbox,
    App\JoinDropboxToMsg,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    App\ShellDropboxMessage,
    App\JoinShellShellDropbox;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShellDropboxTest extends TestCase
{
    /** @test */
    public function modelGetDropbox()
    {
        $shell = Shell::create();
        $dropbox = ShellDropbox::create();
        $join_shell_dropbox = JoinShellShellDropbox::create([
            'id_shell' => $shell->swiss_number,
            'id_dropbox' => $dropbox->swiss_number
        ]);

        $this->assertEquals($shell->getDropbox(), $dropbox->swiss_number);
    }

    /** @test */
    public function modelShellDropboxMessages()
    {
        $shell = Shell::create();
        $audiolist = AudioList::create();
        $dropbox = ShellDropbox::create();
        $audiolist_view_facet = AudioListViewFacet::create([
            'id_list' => $audiolist->id
        ]);
        $join_shell_dropbox = JoinShellShellDropbox::create([
            'id_shell' => $shell->swiss_number,
            'id_dropbox' => $dropbox->swiss_number
        ]);
        $shell_dropbox_msg = ShellDropboxMessage::create([
            'capability' => $audiolist_view_facet->swiss_number,
            'type' => "ROFAL"
        ]);
        $join_dropbox_msg = JoinDropboxToMsg::create([
            'id_dropbox' => $dropbox->swiss_number,
            'id_msg' => $shell_dropbox_msg->swiss_number
        ]);
        $msg_array = $shell->shellDropboxMessages();
        $this->assertEquals($shell_dropbox_msg->toArray(), $msg_array[0]);
    }

    /** @test */
    public function modelShellDropboxMessagesWhenNoMessage()
    {
        $shell = Shell::create();
        $audiolist = AudioList::create();
        $dropbox = ShellDropbox::create();
        $join_shell_dropbox = JoinShellShellDropbox::create([
            'id_shell' => $shell->swiss_number,
            'id_dropbox' => $dropbox->swiss_number
        ]);

        $empty_array = array();
        $msg_array = $shell->shellDropboxMessages();
        $this->assertEquals($empty_array, $msg_array);
    }

    /** @test */
    public function routeCreateShellDropbox()
    {
        $count_before = ShellDropbox::all()->count();
        $this->post("/en/shell");
        $count_after = ShellDropbox::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
    }

    /** @test */
    public function routeAcceptDropboxMessageWithROFALType()
    {
        $shell = Shell::create();
        $audiolist = AudioList::create();
        $dropbox = ShellDropbox::create();
        $audiolist_view_facet = AudioListViewFacet::create([
            'id_list' => $audiolist->id
        ]);
        $join_shell_dropbox = JoinShellShellDropbox::create([
            'id_shell' => $shell->swiss_number,
            'id_dropbox' => $dropbox->swiss_number
        ]);
        $shell_dropbox_msg = ShellDropboxMessage::create([
            'capability' => $audiolist_view_facet->swiss_number,
            'type' => "ROFAL"
        ]);
        $join_dropbox_msg = JoinDropboxToMsg::create([
            'id_dropbox' => $dropbox->swiss_number,
            'id_msg' => $shell_dropbox_msg->swiss_number
        ]);

        $count_view_before = count($shell->audioListViewFacets());
        $this->post("/en/shell/$shell->swiss_number/$shell_dropbox_msg->swiss_number/accept");
        $count_view_after = count($shell->audioListViewFacets());
        $this->assertEquals($count_view_before + 1, $count_view_after);
    }

    /** @test */
    public function routeAcceptDropboxMessageWithRWFALType()
    {
        $shell = Shell::create();
        $audiolist = AudioList::create();
        $dropbox = ShellDropbox::create();
        $audiolist_edit_facet = AudioListEditFacet::create([
            'id_list' => $audiolist->id
        ]);
        $join_shell_dropbox = JoinShellShellDropbox::create([
            'id_shell' => $shell->swiss_number,
            'id_dropbox' => $dropbox->swiss_number
        ]);
        $shell_dropbox_msg = ShellDropboxMessage::create([
            'capability' => $audiolist_edit_facet->swiss_number,
            'type' => "RWFAL"
        ]);
        $join_dropbox_msg = JoinDropboxToMsg::create([
            'id_dropbox' => $dropbox->swiss_number,
            'id_msg' => $shell_dropbox_msg->swiss_number
        ]);

        $count_edit_before = count($shell->audioListEditFacets());
        $this->post("/en/shell/$shell->swiss_number/$shell_dropbox_msg->swiss_number/accept");
        $count_edit_after = count($shell->audioListEditFacets());
        $this->assertEquals($count_edit_before + 1, $count_edit_after);
    }
}