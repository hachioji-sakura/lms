<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCalendarMemberSettingAgreementStatementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('user_calendar_member_setting_agreement_statement', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_calendar_member_setting_id')->comment('user_calendar_member_settingID');
            $table->integer('agreement_statement_id')->comment('agreement_statementID');
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
        Schema::dropIfExists('user_calendar_member_setting_agreement_statement');
    }
}
