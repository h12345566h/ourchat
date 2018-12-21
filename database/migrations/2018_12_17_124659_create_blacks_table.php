<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacks', function (Blueprint $table) {
            $table->increments('black_id');
            $table->string('black_account', 20);
            $table->string('blacked_account', 20);
            $table->dateTime('created_at');

            $table->unique(['black_account', 'blacked_account']);
            $table->foreign('black_account')->references('account')->on('users');
            $table->foreign('blacked_account')->references('account')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blacks');
    }
}
