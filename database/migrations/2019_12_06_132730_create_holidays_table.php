<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::connection('mysql_common')->create('holidays', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable(false)->comment('日付');
            $table->string('remark')->nullable(false)->comment('説明');
            $table->integer('is_public_holiday')->default(0)->comment('国民の休日の場合=1');
            $table->integer('is_private_holiday')->default(0)->comment('塾の休日の場合=1');
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
        Schema::connection('mysql_common')->dropIfExists('holidays');
    }
}
