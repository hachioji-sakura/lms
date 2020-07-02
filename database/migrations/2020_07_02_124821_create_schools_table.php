<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false)->comment('学校名');
            $table->integer('sort_no')->nullable(true)->comment('ソートナンバー');
            $table->string('hp_url')->nullable(true)->comment('HPのURL');
            $table->string('remarks')->nullable(true)->comment('備考');
            $table->integer('create_user_id')->nullable(false)->comment('起票者');
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
        Schema::dropIfExists('schools');
    }
}
