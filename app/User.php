<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\UserTag;
use App\Models\Image;
use App\Models\StudentParent;
use App\Notifications\CustomPasswordReset;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;

use Hash;
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'image_id', 'email', 'password', 'status', 'access_key'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function tags(){
      return $this->hasMany('App\Models\UserTag');
    }
    public function has_tag($key, $val=""){
      $tags = $this->tags;
      foreach($tags as $tag){
        if(empty($val) && $tag->tag_key==$key) return true;
        if($tag->tag_key==$key && $tag->tag_value==$val) return true;
      }
      return false;
    }
    public function get_tag($key){
      $item = $this->tags->where('tag_key', $key)->first();
      if(isset($item)){
        return $item;
      }
      return null;
    }
    public function get_tags($key){
      $item = $this->tags->where('tag_key', $key);
      if(isset($item)){
        return $item;
      }
      return null;
    }

    public function student(){
      return $this->hasOne('App\Models\Student');
    }
    public function teacher(){
      return $this->hasOne('App\Models\Teacher');
    }
    public function manager(){
      return $this->hasOne('App\Models\Manager');
    }
    public function image(){
      return $this->belongsTo('App\Models\Image');
    }
    public function icon(){
      return $this->image->s3_url;
    }
    public function create_comments(){
      return $this->hasMany('App\Models\Comment', 'create_user_id');
    }
    public function target_comments(){
      return $this->hasMany('App\Models\Comment', 'target_user_id');
    }
    public function target_milestones(){
      return $this->hasMany('App\Models\Milestone', 'target_user_id');
    }
    public function calendar_member(){
      return $this->hasMany('App\Models\UserCalendarMember');
    }
    public function calendar_member_setting(){
      return $this->hasMany('App\Models\UserCalendarMemberSetting');
    }

    /**
     * パスワードリセット通知の送信
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordReset($token));
    }
    public function set_password($password){
      $this->update([
        'access_key' => '',
        'password' => Hash::make($password)
      ]);
    }
    public function details($domain = ""){
      //Manager | Teacher | Studentのいずれかで認証し情報を取り出す
      $image = Image::where('id', $this->image_id)->first();
      $s3_url = '';
      if(isset($image)){
        $s3_url = $image->s3_url;
      }
      if(session('login_role') == 'manager' || $domain=="managers"){
        //TODO 事務でない権限で事務の情報を取得できない（例えば、事務からの通達など）
        if(empty($domain) || $domain=="managers"){
          $item = Manager::where('user_id', $this->id)->first();
          if(isset($item)){
            $item['manager_id'] = $item['id'];
            $item['role'] = 'manager';
          }
        }
      }
      if(!isset($item)){
        if(empty($domain) || $domain=="teachers"){
        $item = Teacher::where('user_id', $this->id)->first();
          if(isset($item)){
            $item['teacher_id'] = $item['id'];
            $item['role'] = 'teacher';
          }
        }
      }
      if(!isset($item)){
        if(empty($domain) || $domain=="parents"){
          $item = StudentParent::where('user_id', $this->id)->first();
          if(isset($item)){
            $item['role'] = 'parent';
            $item['student_parent_id'] = $item['id'];
          }
        }
      }
      if(!isset($item)){
        if(empty($domain) || $domain=="students"){
          $item = Student::where('user_id', $this->id)->first();
          if(isset($item)){
            $item['role'] = 'student';
            $item['student_id'] = $item['id'];
          }
        }
      }
      if(isset($item)){
        $item['name'] = $item->name();
        $item['kana'] = $item->kana();
        $item['icon'] = $s3_url;
        $item['email'] = $this->email;
        if(isset($item->birth_day) && $item->birth_day == '9999-12-31') $item->birth_day = '';
        return $item;
      }
      return $this;
    }
    public function scopeTag($query, $tagkey, $tagvalue)
    {
      $where_raw = <<<EOT
        id in (select user_id from user_tags where tag_key=? and tag_value=?)
EOT;

      return $query->whereRaw($where_raw,[$tagkey, $tagvalue]);
    }
    public function calendar_setting(){
      $items = UserCalendarSetting::findUser($this->id)->get();
      return $items;
    }
    public function get_week_calendar_setting()
    {
      $settings = $this->calendar_setting();
      $week_setting = [];
      foreach($settings as $index => $setting){
        $base_date = '2000-01-01 '.$setting['from_time_slot'];
        $diff = strtotime('2000-01-01 '.$setting['to_time_slot'])-strtotime($base_date);
        $week_setting[$setting['lesson_week']] = [];
        while($diff > 0){
          $time = date('H:i', strtotime($base_date));
          $week_setting[$setting['lesson_week']][$time] = true;
          $base_date = date("Y-m-d H:i:s", strtotime("+30 minute ".$base_date));
          $diff -= 1800;
        }
      }
      return $week_setting;
    }
    public function get_work_times($prefix='work')
    {
      $weeks = config('attribute.lesson_week');
      $ret = [];
      foreach($weeks as $week_day => $val){
        $key = $prefix.'_'.$week_day.'_time';
        $tags = $this->get_tags($key);
        $ret[$week_day]=[];
        foreach($tags as $index => $tag){
          $tag_value = $tag->tag_value;
          if($tag_value=='am'){
            $ret[$week_day][1000] = true;
            $ret[$week_day][1030] = true;
            $ret[$week_day][1100] = true;
            $ret[$week_day][1130] = true;
          }
          else if(strlen($tag_value)==5){
            //nn_nn形式
            $time = intval(substr($tag_value,0,2));
            $ret[$week_day][$time.'00'] = true;
            $ret[$week_day][$time.'30'] = true;
          }
        }
      }
      return $ret;
    }
    public function get_lesson_times()
    {
      return $this->get_work_times('lesson');
    }
}
