<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UserCalendar;
class AddTeachingTypeUserCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->string('teaching_type')->after('lecture_id')->default('')->comment('授業予定タイプ：trial=体験、exchange=振替、regular=通常、add=追加、season=期間講習, trainng=演習');
        });
        $items = UserCalendar::all();
        foreach($items as $item){
          $code = $this->teaching_type($item);
          if(empty($code)) continue;
          $item->update(['teaching_type' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_calendars', function (Blueprint $table) {
          $table->dropColumn('teaching_type');
        });
    }

    public function teaching_type($item){
      if($item->work==10) return 'season';
      if($item->work==5) return 'training';

      if($item->is_teaching()){
        if($item->trial_id > 0){
          return 'trial';
        }
        if(intval($item->user_calendar_setting_id) > 0){
          return 'regular';
        }
        if($item->exchanged_calendar_id > 0){
          return 'exchange';
        }
        return 'add';
      }
      return "";
    }
}
