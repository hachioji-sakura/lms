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
      $items = UserCalendarSetting::findUser($this->id)
      ->orderBy('lesson_week', 'asc')
      ->orderBy('from_time_slot', 'asc')
      ->get();
      $ret = [];
      foreach($items as $item){
        if(!isset($ret[$item->schedule_method])) $ret[$item->schedule_method] = [];
        if(!isset($ret[$item->schedule_method][$item->lesson_week])) $ret[$item->schedule_method][$item->lesson_week] = [];
        $ret[$item->schedule_method][$item->lesson_week][] = $item;
      }
      return $ret;
    }
    public function get_week_calendar_setting($minute=30)
    {
      $settings = $this->calendar_setting();
      if(!isset($settings['week'])) return [];
      $settings = $settings['week'];
      $week_setting = [];
      $seconds = $minute*60;
      foreach($settings as $week_day => $settings){
        if(!isset($week_setting[$week_day])){
          $week_setting[$week_day] = [];
        }
        foreach($settings as $setting){
          //カレンダー設定を取得し、30分単位のコマ設定として取得する
          $base_date = '2000-01-01 '.$setting['from_time_slot'];
          $time_minutes = strtotime('2000-01-01 '.$setting['to_time_slot'])-strtotime($base_date);
          //echo"[".$setting['from_time_slot']."][".$setting['to_time_slot']."][".$time_minutes."][".$setting['lesson_week']."]<br>";
          while($time_minutes > 0){
            $time = date('Hi', strtotime($base_date));
            $week_setting[$week_day][$time] = $setting; //ex.$week_setting['fri'][1630] = setting
            //echo "→[".$time."][".$setting['lesson_week']."]<br>";
            $base_date = date("Y-m-d H:i:s", strtotime("+".$minute." minute ".$base_date));
            $time_minutes -= $seconds;
          }
        }
      }
      //var_dump($week_setting);
      //array("mon" : [ calendar_setting...])
      return $week_setting;
    }
    /**
     * user_tagsから、work_mon_time、work_the_timeなどを取得し、
     * 30分単位のtime_slotにしてtrue/falseを返す
     * @return array
     * ex. ["mon" => ["1000"=>true, "1030"=>true,...]
     */
    public function get_work_times($prefix='work', $minute=30)
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
            $c = 0;
            while($c < 60){
              $ret[$week_day]['10'.sprintf('%02d', $c)] = true;
              $c+=$minute;
            }
            $c = 0;
            while($c < 60){
              $ret[$week_day]['11'.sprintf('%02d', $c)] = true;
              $c+=$minute;
            }
          }
          else if(strlen($tag_value)==5){
            //nn_nn形式
            $time = intval(substr($tag_value,0,2));
            $c = 0;
            while($c < 60){
              $ret[$week_day][$time.sprintf('%02d', $c)] = true;
              $c+=$minute;
            }
          }
        }
      }
      return $ret;
    }
    /**
     * user_tagsから、lesson_mon_time、lesson_the_timeなどを取得し、
     * 30分単位のtime_slotにしてtrue/falseを返す
     * @return array
     * ex. ["mon" => ["1000"=>true, "1030"=>true,...]
     */
    public function get_lesson_times($minute=30)
    {
      return $this->get_work_times('lesson', $minute);
    }
}
