<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventUserMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_user_mails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_user_id')->index('index_event_user_id')->comment('イベントユーザーID');
            $table->integer('mail_id')->index('index_mail_id')->comment('メールID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_user_mails');
    }
}
