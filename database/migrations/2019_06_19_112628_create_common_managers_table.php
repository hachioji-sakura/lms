<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::connection('mysql_common')->create('managers', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->index('index_user_id')->comment('ユーザーID')->unique();
        $table->string('name_first')->nullable(false)->comment('姓');
        $table->string('name_last')->nullable(false)->comment('名');
        $table->string('kana_first')->nullable(false)->comment('姓カナ');
        $table->string('kana_last')->nullable(false)->comment('名カナ');
        $table->integer('gender')->nullable(false)->default(0)->comment('性別：1=男性 , 2=女性, 0=未設定');
        $table->date('birth_day')->nullable(true)->default(null)->comment('生年月日');
        $table->string('phone_no')->nullable(true)->comment('生年月日');
        $table->string('address')->nullable(true)->comment('住所');
        $table->integer('create_user_id')->comment('作成者');
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
      Schema::connection('mysql_common')->dropIfExists('managers');
    }
}
