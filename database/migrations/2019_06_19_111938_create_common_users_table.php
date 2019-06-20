<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('users', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name')->comment('ユーザー名');
          $table->string('email')->unique()->comment('メールアドレス（ログインキー）');
          $table->timestamp('email_verified_at')->nullable();
          $table->integer('status')->default(0)->comment('0:新規　, 1:仮 , 9:削除');
          $table->integer('image_id')->default(0)->index('index_image_id')->comment('アイコン');
          $table->string('password')->comment('パスワード');
          $table->string('access_key')->default('')->comment('アクセスキー');
          $table->rememberToken();
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
        Schema::connection('mysql_common')->dropIfExists('users');
    }
}
