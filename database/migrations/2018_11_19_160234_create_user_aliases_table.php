<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_aliases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('alias_key');
            $table->string('alias_value');
            $table->string('create_user_id');
            $table->timestamps();
            $table->index('user_id');
            $table->index('alias_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_aliases');
    }
}
