<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id')->nullable(false)->index('index_comment_id')->comment('コメントID');
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
        Schema::dropIfExists('comment_checks');
    }
}
