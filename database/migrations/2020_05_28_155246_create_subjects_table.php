<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\GeneralAttribute;
use App\Models\Subject;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('subjects', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name')->nullable(false)->comment('科目名');
          $table->string('remarks')->nullable(true)->comment('備考');
          $table->integer('sort_no')->nullable(true)->comment('ソートナンバー');
          $table->integer('create_user_id');
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
        Schema::dropIfExists('subjects');
    }
}
