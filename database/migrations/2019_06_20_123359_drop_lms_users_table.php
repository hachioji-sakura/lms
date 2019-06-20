<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLmsUsersTable extends Migration
{
  private $tables = ['users','user_tags',
            'students', 'teachers', 'managers',
            'student_parents', 'student_relations',
            'student_groups', 'student_group_members',
            'general_attributes'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      foreach($this->tables as $table){
        //lms → commonに移行
        $sql        = 'SHOW COLUMNS FROM common.'.$table;
        $columns    = DB::select($sql);
        $field = "";
        foreach($columns as $column){
          $field .= $column->Field.',';
        }
        $field = trim($field,',').'';
        DB::statement('insert into common.'.$table.'('.$field.') select '.$field.' from lms.'.$table);
        DB::statement('drop table lms.'.$table);
      }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      foreach($this->tables as $table){
        //common → lmsに戻す
        DB::statement('CREATE TABLE lms.'.$table.' LIKE common.'.$table);
        $sql        = 'SHOW COLUMNS FROM common.'.$table;
        $columns    = DB::select($sql);
        $field = "";
        foreach($columns as $column){
          $field .= $column->Field.',';
        }
        $field = trim($field,',').'';
        DB::statement('insert into lms.'.$table.'('.$field.') select '.$field.' from common.'.$table);
      }
    }
}
