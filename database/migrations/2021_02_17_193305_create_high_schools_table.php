<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateHighSchoolsTable
 */
class CreateHighSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('high_schools', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned()->comment('学校ID');
            $table->string('post_number')->comment('郵便番号');
            $table->string('address')->comment('住所');
            $table->string('phone_number')->comment('電話番号');
            $table->string('fax_number')->nullable()->comment('FAX番号');
            $table->text('access')->comment('使用路線');
            $table->boolean('full_day_grade')->comment('全日学年制');
            $table->boolean('full_day_credit')->comment('全日単位制');
            $table->boolean('part_time_grade_night_only')->comment('定時制学年制夜間');
            $table->boolean('part_time_credit')->comment('定時単位制');
            $table->boolean('part_time_credit_night_only')->comment('定時単位制夜間');
            $table->boolean('online_school')->comment('通信制');
            $table->timestamps();

            // インデックス
            $table->index('school_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('high_schools');
    }
}
