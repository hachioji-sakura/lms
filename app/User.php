<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRelation;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\UserTag;
use App\Models\Comment;
use App\Models\Image;
use App\Models\StudentParent;
use App\Notifications\CustomPasswordReset;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use App\Models\UserCalendarSetting;
use App\Models\Traits\Common;
use Hash;
class User extends Authenticatable
{
    use Common;
    use Notifiable;
    protected $connection = 'mysql_common';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'image_id', 'email', 'password', 'status', 'access_key','locale'
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
    public function student_parent(){
      return $this->hasOne('App\Models\StudentParent');
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
    public function calendars(){
      return $this->hasMany('App\Models\UserCalendar');
    }
    public function calendar_settings(){
      return $this->hasMany('App\Models\UserCalendarSetting');
    }
    public function calendar_members(){
      return $this->hasMany('App\Models\UserCalendarMember');
    }
    public function calendar_member_settings(){
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
      $image = $this->image;
      $s3_url = '';
      if(isset($image)){
        $s3_url = $image->s3_url;
      }
      //id=1はroot
      if($this->id==1 || session('login_role') == 'manager' || $domain=="managers"){
        //事務でない権限で事務の情報を取得できない（例えば、事務からの通達など）
        if(empty($domain) || $domain=="managers"){
          $item = Manager::where('user_id', $this->id)->first();
          if(isset($item)){
            $item['manager_id'] = $item['id'];
            if($this->id==1 || (session('login_role') == 'manager' && $item->is_admin()==true)){
              $item['role'] = 'manager';
            }
            else {
              $item['role'] = 'staff';
            }
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
      if(!isset($item)){
        if(empty($domain) ){
          $item = Manager::where('user_id', $this->id)->first();
          if(isset($item)){
            $item['manager_id'] = $item['id'];
            $item['role'] = 'staff';
          }
        }
      }

      if(isset($item)){
        $item['name'] = $item->name();
        $item['kana'] = $item->kana();
        $item['icon'] = $s3_url;
        $item['email'] = $this->email;
        switch($item['role']){
          case 'manager':
          case 'staff':
            $item['domain'] = 'managers';
            $item['role_name'] = '事務員';
            break;
          case 'teacher':
            $item['domain'] = 'teachers';
            $item['role_name'] = '講師';
            break;
          case 'parent':
            $item['domain'] = 'parents';
            $item['role_name'] = '契約者';
            break;
          case 'student':
            $item['domain'] = 'students';
            $item['role_name'] = '生徒';
            break;
        }
        $item['email'] = $this->email;
        $item['locale'] = $this->locale;
        if(isset($item->birth_day) && $item->birth_day == '9999-12-31') $item->birth_day = '';
        return $item;
      }
      return $this;
    }
    public function get_name(){
      $item = $this->details();
      if(!empty($item["name"])) return $item["name"];
      return "";
    }
    public function scopeTag($query, $tagkey, $tagvalue)
    {
      $where_raw = <<<EOT
        id in (select user_id from user_tags where tag_key=? and tag_value=?)
EOT;

      return $query->whereRaw($where_raw,[$tagkey, $tagvalue]);
    }
    public function get_enable_calendar_settings(){
      $items = UserCalendarSetting::findUser($this->id)
      ->whereNotIn('status', ['cancel'])
      ->orderByWeek('lesson_week', 'asc')
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
    public function get_enable_lesson_calendar_settings(){
      $items = UserCalendarSetting::findUser($this->id)
      ->whereNotIn('status', ['cancel'])
      ->enable()
      ->orderByWeek('lesson_week', 'asc')
      ->orderBy('from_time_slot', 'asc')
      ->get();
      $ret = [];
      foreach(config('attribute.lesson') as $key => $name){
        if(isset($ret[$key])) $ret[$key] = [];
        foreach($items as $item){
          $lesson = $item->get_tag_value('lesson');
          if($lesson!=$key) continue;
          if(!isset($ret[$lesson][$item->schedule_method])) $ret[$item->schedule_method] = [];
          if(!isset($ret[$lesson][$item->schedule_method][$item->lesson_week])) $ret[$item->schedule_method][$item->lesson_week] = [];
          $ret[$lesson][$item->schedule_method][$item->lesson_week][] = $item;
        }
      }
      return $ret;
    }
    public function get_enable_calendar_setting_count($lesson){
      $items = UserCalendarSetting::findUser($this->id)
      ->where('schedule_method', 'week')
      ->whereNotIn('status', ['cancel'])
      ->enable()
      ->orderBy('from_time_slot', 'asc')
      ->get();
      $c = 0;
      foreach($items as $item){
        if($item->has_tag('lesson', $lesson)) $c++;
      }
      return $c;
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
            $hour = intval(substr($tag_value,0,2));
            $c = 0;
            while($c < 60){
              $ret[$week_day][sprintf('%02d', $hour).sprintf('%02d', $c)] = true;
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
    public function get_trial_enable_times($minute=30)
    {
      return $this->get_work_times('trial', $minute);
    }
    public function get_lesson_times($minute=30)
    {
      return $this->get_work_times('lesson', $minute);
    }

    public function send_mail($title, $param, $type, $template){
      $param['user'] = $this->details();
      if(!isset($param['login_user'])){
        $u = Auth::user();
        if(isset($u)){
          $param['login_user'] = $u->details();
        }
      }
      $res = $this->_send_mail($this->get_mail_address(), $title, $param, $type, $template, $this->locale);
      return $res;
    }
    public function remind_mail($title, $param, $type, $template, $send_schedule){
      $param['user'] = $this->details();
      if(!isset($param['login_user'])){
        $u = Auth::user();
        if(isset($u)){
          $param['login_user'] = $u->details();
        }
      }
      $res = $this->_remind_mail($this->get_mail_address(), $title, $param, $type, $template, $this->locale, $send_schedule);
      return $res;
    }

    public function get_mail_address(){
      \Log::info("-----------------get_mail_address------------------");
      $u = $this->details();
      $email = '';
      \Log::info($u->role);
      if($u->role==='student'){
        $student_id = $this->student->id;
        $relations = StudentRelation::where('student_id', $student_id)->get();
        foreach($relations as $relation){
          //TODO 先にとれたユーザーを操作する親にする（修正したい）
          $user_id = $relation->parent->user->id;
          $email = $relation->parent->user->email;
          \Log::info("relation=".$user_id.":".$email);
          break;
        }
      }
      else {
        $email = $u->email;
      }
      \Log::info("-----------------get_mail_address[$email]------------------");
      return $email;
    }
    public function get_comments($form){
      $u = $this->details();
      $form['_sort'] ='created_at';
      $comment_types = [];
      $is_desc = false;
      $sort = 'desc';
      if(isset($form['is_asc']) && $form['is_asc']==1){
        $sort = 'asc';
      }
      $is_star = false;
      if(isset($form['is_star']) && $form['is_star']==1){
        $is_star = true;
      }
      $form_date = "";
      $to_date = "";
      if(isset($form['search_from_date'])){
        $from_date = $form['search_from_date'];
      }
      if(isset($form['search_to_date'])){
        $to_date = $form['search_to_date'];
      }
      if(isset($form['search_comment_type'])){
        $comment_types = $form['search_comment_type'];
      }
      $comments = Comment::findTargetUser($this->id);
      $comments = $comments->findTypes($comment_types);
      $comments = $comments->findDefaultTypes($u->domain);

      if($is_star==true){
        $comments = $comments->where('importance', '>', 0);
      }
      if(!empty($from_date) || !empty($to_date)){
        $comments = $comments->rangeDate($from_date, $to_date);
      }

      if(isset($form['is_checked_only']) && $form['is_checked_only']==1){
        if(!(isset($form['is_unchecked_only']) && $form['is_unchecked_only']==1)){
          $comments = $comments->checked($form['check_user_id']);
        }
      }
      else if(isset($form['is_unchecked_only']) && $form['is_unchecked_only']==1){
        $comments = $comments->unChecked($form['check_user_id']);
      }
      if(isset($form['search_keyword']) && !empty($form['search_keyword'])){
        $comments = $comments->searchWord($form['search_keyword']);
      }

      $count = $comments->count();
      if($is_star==true){
        $comments = $comments->orderBy('importance', 'desc')
                             ->orderBy('created_at', 'desc');
      }
      else {
        $comments = $comments->sortCreatedAt($sort);
      }

      $comments = $comments->get();
      return ["data" => $comments, 'count' => $count];
    }
}
