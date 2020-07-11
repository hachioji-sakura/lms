<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      //@TODO
      //title, typeはほぼ意味をなさないが、必要になったら使う
        Schema::create('task_comments', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('task_id')->nullable(false);
          $table->string('title')->nullable(true);
          $table->string('body',10000)->nullable(false);
          $table->string('type')->nullable(false);
          $table->string('s3_alias')->nullable(true);
          $table->string('s3_url')->nullable(true);
          $table->integer('create_user_id')->index('index_create_user_id');
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
        Schema::dropIfExists('task_comments');
    }
}
