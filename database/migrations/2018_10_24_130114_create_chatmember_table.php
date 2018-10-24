<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatmemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatmember', function (Blueprint $table) {
            $table->increments('cm_id');
            $table->string('account', 20);
            $table->unsignedInteger('chat_id');
            $table->integer('status');
            $table->dateTime('created_at');

            $table->unique(['account', 'chat_id']);
            $table->foreign('account')->references('account')->on('user');
            $table->foreign('chat_id')->references('chat_id')->on('chat');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatmember');
    }
}
