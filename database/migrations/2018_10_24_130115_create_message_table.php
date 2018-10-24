<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('message_id');
            $table->string('message', 3000);
            $table->unsignedInteger('type');
            $table->string('account', 20);
            $table->unsignedInteger('chat_id');
            $table->unsignedInteger('cm_id');
            $table->dateTime('created_at');

            $table->foreign('account')->references('account')->on('user');
            $table->foreign('chat_id')->references('chat_id')->on('chat');
            $table->foreign('cm_id')->references('cm_id')->on('chatmember');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message');
    }
}
