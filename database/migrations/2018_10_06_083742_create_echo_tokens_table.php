<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEchoTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('echo_tokens', function (Blueprint $table) {
            $table->increments('et_id');
            $table->string('account', 20);
            //1 fcm /2 APNs
            $table->unsignedTinyInteger('type');
            $table->string('token', 300);
            $table->dateTime('created_at');

            $table->foreign('account')->references('account')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('echo_tokens');
    }
}
