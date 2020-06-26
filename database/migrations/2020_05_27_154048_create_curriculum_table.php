<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Curriculum;

class CreateCurriculumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false)->comment('単元名');
            $table->string('remarks')->nullable(true)->comment('備考');
            $table->integer('sort_no')->nullable(true)->comment('ソートナンバー');
            $table->integer('create_user_id')->comment('登録ユーザー');
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
        Schema::dropIfExists('curriculums');
    }
}
