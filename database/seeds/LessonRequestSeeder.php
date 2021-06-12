<?php

use Illuminate\Database\Seeder;
use App\Models\Trial;
use App\Models\LessonRequest;
use App\Models\LessonRequestTag;
use App\Models\LessonRequestDate;
use App\Models\Ask;
use App\Models\UserCalendar;
use App\Models\UserCalendarSetting;

class LessonRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //体験申し込みをLessonRequestに変えるときに使う
      set_time_limit(3600);
      $trials = Trial::all();
      foreach($trials as $trial){
        $r = LessonRequest::where('type', 'trial')
                          ->where('user_id', $trial->student->user_id)
                          ->where('created_at', $trial->created_at)
                          ->first();

        if(!isset($r)){
          $r = LessonRequest::create([
            'type' => 'trial',
            'user_id' => $trial->student->user_id,
            'create_user_id' => $trial->parent->user_id,
            'status' => $trial->status,
            'remark' => $trial->remark,
            'created_at' => $trial->created_at,
            'updated_at' => $trial->updated_at,
          ]);
          $d = date('Y-m-d', strtotime($trial->trial_start_time1));
          $from = date('h:i', strtotime($trial->trial_start_time1));
          $to = date('h:i', strtotime($trial->trial_end_time1));
          $s = 1;
          LessonRequestDate::create([
            'lesson_request_id' => $r->id,
            'day' => $d,
            'sort_no' => $s,
            'from_time_slot' => $from,
            'to_time_slot' => $to,
            'created_at' => $trial->created_at,
            'updated_at' => $trial->updated_at,
          ]);
          $d = date('Y-m-d', strtotime($trial->trial_start_time2));
          $from = date('h:i', strtotime($trial->trial_start_time2));
          $to = date('h:i', strtotime($trial->trial_end_time2));
          $s++;
          LessonRequestDate::create([
            'lesson_request_id' => $r->id,
            'day' => $d,
            'sort_no' => $s,
            'from_time_slot' => $from,
            'to_time_slot' => $to,
            'created_at' => $trial->created_at,
            'updated_at' => $trial->updated_at,
          ]);
          $d = date('Y-m-d', strtotime($trial->trial_start_time3));
          $from = date('h:i', strtotime($trial->trial_start_time3));
          $to = date('h:i', strtotime($trial->trial_end_time3));
          $s++;
          LessonRequestDate::create([
            'lesson_request_id' => $r->id,
            'day' => $d,
            'sort_no' => $s,
            'from_time_slot' => $from,
            'to_time_slot' => $to,
            'created_at' => $trial->created_at,
            'updated_at' => $trial->updated_at,
          ]);
          foreach($trial->tags as $tag){
            LessonRequestTag::create([
              'lesson_request_id' => $r->id,
              'tag_key' => $tag->tag_key,
              'tag_value' => $tag->tag_value,
              'create_user_id' => $tag->create_user_id,
              'created_at' => $tag->created_at,
              'updated_at' => $tag->updated_at,
            ]);
          }
          if(!empty($trial->schedule_start_hope_date)){
            LessonRequestTag::create([
              'lesson_request_id' => $r->id,
              'tag_key' => 'schedule_start_hope_date',
              'tag_value' => $trial->schedule_start_hope_date,
              'create_user_id' => $trial->parent->user_id,
              'created_at' => $tag->updated_at,
              'updated_at' => $tag->updated_at,
            ]);
          }
        }
        Ask::where('target_model', 'trials')->where('target_model_id', $trial->id)
                  ->update(['target_model'=>'lesson_requests',
                            'target_model_id' => $r->id]);
        UserCalendarSetting::where('trial_id', $trial->id)->update(['lesson_request_id' => $r->id]);
        UserCalendar::where('trial_id', $trial->id)->update(['lesson_request_id' => $r->id]);
      }
    }
}
