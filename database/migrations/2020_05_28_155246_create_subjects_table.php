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
          $table->string('name')->nullable(false);
          $table->integer('create_user_id');
          $table->timestamps();
        });
        $subjects = GeneralAttribute::where('attribute_key','charge_subject')->get();
        foreach($subjects as $subject){
          $form = [
            'name' => $subject->attribute_name,
            'create_user_id' => 1,
           ];
           Subject::create($form);
        }
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
