<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('announcement_id')->nullable(false)->index('index_comment_id')->comment('事務コメントID');
            $table->integer('check_user_id')->index('index_check_user_id')->comment('チェックしたユーザー');
            $table->boolean('is_checked')->default(0)->comment('チェックした=1');
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
        Schema::dropIfExists('announcement_checks');
    }
}
