<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Trial;

class AddStudentIdTrials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trials', function (Blueprint $table) {
          $table->integer('student_id')->index('index_student_id')->comment('生徒ID')->after('student_parent_id');
        });
        $trials = Trial::all();
        //TODO いずれtrial_studentsテーブルを削除する
        foreach($trials as $trial){
          foreach($trial->trial_students as $s){
            $trial->update(['student_id' => $s->student_id]);
          }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trials', function (Blueprint $table) {
          $table->dropColumn('student_id');
        });
    }
}
