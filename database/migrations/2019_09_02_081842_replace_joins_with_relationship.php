<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReplaceJoinsWithRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audio', function (Blueprint $table) {
            $table->bigInteger('audio_list_id')->nullable();
        });
        Schema::dropIfExists('join_shell_to_msgs');
        Schema::dropIfExists('join_dropbox_to_msgs');
        Schema::dropIfExists('join_shell_shell_dropboxes');
        Schema::dropIfExists('shell_dropbox_messages');
        Schema::dropIfExists('shell_dropboxes');
        Schema::dropIfExists('join_shell_view_facets');
        Schema::dropIfExists('join_shell_edit_facets');
        Schema::dropIfExists('join_list_audio');
        Schema::dropIfExists('shells');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audio', function (Blueprint $table) {
            $table->dropColumn('audio_list_id');
        });
        Schema::create('shells', function (Blueprint $table) {
            $table->string('swiss_number', 24)->primary();
            $table->timestamps();
        });
        Schema::create('join_list_audio', function (Blueprint $table) {
            $table->bigInteger('id_list')->unsigned();
            $table->string('id_audio', 24);
            $table->timestamps();

            $table->foreign('id_list')
                ->references('id')
                ->on('audio_lists')
                ->onDelete('cascade');

            $table->foreign('id_audio')
                ->references('swiss_number')
                ->on('audio')
                ->onDelete('cascade');
        });
        Schema::create('join_shell_edit_facets', function (Blueprint $table) {
            $table->string('id_shell', 24);
            $table->string('id_facet', 24);
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('swiss_number')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_facet')
                ->references('swiss_number')
                ->on('audio_list_edit_facets')
                ->onDelete('cascade');
        });
        Schema::create('join_shell_view_facets', function (Blueprint $table) {
            $table->string('id_shell', 24);
            $table->string('id_facet', 24);
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('swiss_number')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_facet')
                ->references('swiss_number')
                ->on('audio_list_view_facets')
                ->onDelete('cascade');
        });
        Schema::create('shell_dropboxes', function (Blueprint $table) {
            $table->string('swiss_number', 24)->primary();
            $table->timestamps();
        });
        Schema::create('shell_dropbox_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_receiver', 24);
            $table->string('capability', 24);
            $table->enum('type', ['ROFAL', 'RWFAL']);
            $table->timestamps();
        });
        Schema::create('join_shell_shell_dropboxes', function (Blueprint $table) {
            $table->string('id_shell', 24);
            $table->string('id_dropbox', 24);
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('swiss_number')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_dropbox')
                ->references('swiss_number')
                ->on('shell_dropboxes')
                ->onDelete('cascade');
        });
        Schema::create('join_dropbox_to_msgs', function (Blueprint $table) {
            $table->string('id_dropbox', 24);
            $table->string('id_msg', 24);
            $table->timestamps();

            $table->foreign('id_dropbox')
                ->references('swiss_number')
                ->on('shell_dropboxes')
                ->onDelete('cascade');

            $table->foreign('id_msg')
                ->references('swiss_number')
                ->on('shell_dropbox_messages')
                ->onDelete('cascade');
        });
        Schema::create('join_shell_to_msgs', function (Blueprint $table) {
            $table->string('id_shell', 24);
            $table->string('id_msg', 24);
            $table->timestamps();

            $table->foreign('id_shell')
                ->references('swiss_number')
                ->on('shells')
                ->onDelete('cascade');

            $table->foreign('id_msg')
                ->references('swiss_number')
                ->on('shell_dropbox_messages')
                ->onDelete('cascade');
        });
    }
}
