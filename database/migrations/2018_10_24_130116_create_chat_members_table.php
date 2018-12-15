<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_members', function (Blueprint $table) {
            $table->increments('cm_id');
            $table->string('account', 20);
            $table->unsignedInteger('chat_id');
            $table->unsignedInteger('message_id')->nullable();
            $table->integer('status');
            $table->dateTime('created_at');

            $table->foreign('message_id')->references('message_id')->on('messages');
            $table->unique(['account', 'chat_id']);
            $table->foreign('account')->references('account')->on('user');
            $table->foreign('chat_id')->references('chat_id')->on('chats');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_members');
    }
}