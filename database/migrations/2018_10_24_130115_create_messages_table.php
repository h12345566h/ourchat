<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('message_id');
            $table->unsignedInteger('chat_id');
            $table->string('account', 20);
            $table->string('content', 3000);
            $table->unsignedInteger('type');
            $table->boolean('revoke')->default(false);
            $table->dateTime('created_at');

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
        Schema::dropIfExists('messages');
    }
}
