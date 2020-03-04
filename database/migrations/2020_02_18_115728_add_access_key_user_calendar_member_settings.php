<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UserCalendarSetting;

class AddAccessKeyUserCalendarMemberSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        UserCalendarSetting::where('status', 'enabled')->update(['status'=>'fix']);
        $settings = UserCalendarSetting::all();
        foreach($settings as $setting){
          if($setting->is_enable()==false){
            $setting->update(['status'=>'closed']);
          }
        }
        Schema::table('user_calendar_member_settings', function (Blueprint $table) {
          $table->string('access_key')->default('')->after('remark')->comment('アクセスキー');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendar_member_settings', function (Blueprint $table) {
          $table->dropColumn('access_key');
        });
    }
}
