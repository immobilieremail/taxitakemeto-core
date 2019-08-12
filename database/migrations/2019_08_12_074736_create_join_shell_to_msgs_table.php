<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoinShellToMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('join_shell_to_msgs');
    }
}
