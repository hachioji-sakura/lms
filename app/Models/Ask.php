<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Trial;
use App\Models\Managers;
use App\Models\Teachers;
use App\Models\Students;
use App\Models\UserCalendar;
use App\Models\UserCalendarMember;
use Illuminate\Support\Facades\Auth;
use View;
use App\Models\Traits\WebCache;

/**
 * App\Models\Ask
 *
 * @property int $id
 * @property string $title 件名
 * @property string $body 内容
 * @property string $type 種別
 * @property int $parent_ask_id 親依頼ID
 * @property string $status ステータス
 * @property string|null $from_time_slot 開始時分
 * @property string|null $to_time_slot 終了時分
 * @property string|null $start_date 開始日
 * @property string|null $end_date 終了日
 * @property string $target_model 更新対象model
 * @property int $target_model_id 更新対象model.id
 * @property int $charge_user_id 担当ユーザーID
 * @property int $target_user_id 対象ユーザーID
 * @property int $create_user_id 作成ユーザーID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $charge_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AskComment[] $comments
 * @property-read User $create_user
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $importance_label
 * @property-read mixed $target_user_name
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read Ask $parent_ask
 * @property-read User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|Ask chargeUser($user_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Ask findTargetModel($target_model, $target_model_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Ask findUser($user_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Ask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|Ask query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ask rangeDate($from_date, $to_date = null, $field = 'start_time')
 * @method static \Illuminate\Database\Eloquent\Builder|Ask rangeEndDate($from_date, $to_date = null, $field = 'start_time')
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Ask sortEnddate($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Ask user($user_id)
 * @mixin \Eloquent
 */
class Ask extends Milestone
{
  use WebCache;
  protected $table = 'lms.asks';
  protected $guarded = array('id');

  public function charge_user(){
    return $this->belongsTo('App\User', 'charge_user_id');
  }
  public function parent_ask(){
    return $this->belongsTo('App\Models\Ask', 'parent_ask_id');
  }
  public function comments(){
    return $this->hasMany('App\Models\AskComment');
  }

  public function getStatusNameAttribute(){
    return config('attribute.ask_status')[$this->status];
  }

  public function scopeRangeDate($query, $from_date, $to_date=null, $field='start_time')
  {
    //日付検索
    if(!empty($from_date)){
      $query = $query->where($field, '>=', $from_date);
    }
    if(!empty($to_date)){
      $query = $query->where($field, '<', $to_date);
    }
    return $query;
  }
  public function scopeFindUser($query, $user_id)
  {
    $where_raw = <<<EOT
      (target_user_id = $user_id
      or charge_user_id = $user_id
      )
EOT;
    return $query->whereRaw($where_raw,[1]);
  }
  public function scopeUser($query, $user_id)
  {
    return $query->where('charge_user_id', $user_id);
  }
  public function scopeChargeUser($query, $user_id)
  {
    return $query->where('charge_user_id', $user_id);
  }
  public function scopeRangeEndDate($query, $from_date, $to_date=null, $field='start_time')
  {
    return $this->scopeRangeDate($query, $from_date, $to_date, 'end_time');
  }
  public function scopeSortEnddate($query, $sort){
    if(empty($sort)) $sort = 'asc';
    return $query->orderBy('end_date', $sort);
  }
  static protected function already_data($form){
    $type = $form['type'];
    //重複チェック
    $ask = Ask::where('type', $type)
      ->whereNotIn('status', ['cancel', 'closed', 'complete'])
      ->where('target_model', $form['target_model'])
      ->where('target_model_id', $form['target_model_id']);

    if(isset($form['status'])){
      $ask = $ask->whereIn('status', $form['status']);
    }
    if(isset($form['start_date'])){
      $ask = $ask->where('start_date', $form['start_date']);
    }
    if(isset($form['target_user_id'])){
      $ask = $ask->where('target_user_id', $form['target_user_id']);
    }
    if(isset($form['charge_user_id'])){
      $ask = $ask->where('charge_user_id', $form['charge_user_id']);
    }
    $ask = $ask->first();
    if(isset($ask)) {
      //重複登録あり
      return $ask->details();
    }
    return null;
  }
  static protected function add($form, $file=null){
    $parent_ask_id = 0;
    if(isset($form['parent_ask_id'])){
      $parent_ask_id = $form['parent_ask_id'];
      if($form['type']=="teacher_change"){
        if(!isset($form['target_user_id'])){
          //担当者・対象者は一緒
          $form['target_user_id'] = $form['charge_user_id'];
        }
        //代講：target_modelには、休講のデータを設定する
        $parent_ask = Ask::where('id', $parent_ask_id)->first();
        if(isset($parent_ask)){
          $form["target_model"] = $parent_ask->target_model;
          $form["target_model_id"] = $parent_ask->target_model_id;
          $form["end_date"] = $parent_ask->end_date;
          $form["body"] = $parent_ask->body;
        }
      }
    }
    if(!isset($form['title'])){
      $form['title'] = '';
    }
    if(!isset($form['start_date'])){
      $form['start_date'] = date('Y-m-d');
      if(!isset($form['start_date'])){
        $form['start_date'] = date('Y-m-d');
      }
    }
    if(!isset($form['end_date'])){
      $form['end_date'] = '9999-12-31';
    }
    if(!isset($form['body'])){
      $form['body'] = '';
    }
    if(!isset($form['access_key'])){
      $form['access_key'] = '';
    }
    if( $form['type'] == "teacher_change"){
      $emp_ask = new Ask;
      $emp_ask->target_user_id = $form['target_user_id'];
      $emp_ask->charge_user_id = $form['charge_user_id'];
      $form['title'] = __('labels.ask_teacher_change'). ' : ' . $emp_ask->target_user->details()->name() .' → '. $emp_ask->charge_user->details()->name();
      $form['body'] = View::make('emails.forms.calendar')->with([
        'item' => UserCalendar::where('id' ,$form['target_model_id'])->first(),
        'login_user' => Auth::user()->details(),
        'send_to' => 'teacher',
        ])->render();
    }
    if(!isset($form['target_model'])){
      $form['target_model'] = '';
    }

    if(!isset($form['target_model_id'])){
      $form['target_model_id'] = 0;
    }

    if(!isset($form['from_time_slot'])){
      $form['from_time_slot'] = "";
    }
    if(!isset($form['to_time_slot'])){
      $form['to_time_slot'] = "";
    }
    if(!isset($form['status'])){
      $form['status'] = "new";
    }

    $ask = Ask::create([
      'start_date' => $form['start_date'],
      'end_date' => $form['end_date'],
      'type' => $form['type'],
      'parent_ask_id' => $parent_ask_id,
      'status' => $form['status'],
      'title' => $form['title'],
      'body' => $form['body'],
      'access_key' => $form['access_key'],
      'from_time_slot' => $form['from_time_slot'],
      'to_time_slot' => $form['to_time_slot'],
      'target_model' => $form['target_model'],
      'target_model_id' => $form['target_model_id'],
      'charge_user_id' => $form['charge_user_id'],
      'target_user_id' => $form['target_user_id'],
      'create_user_id' => $form['create_user_id'],
    ]);
    $ask->cache_delete();
    if($ask->is_auto_commit()==true){
      //自動承認対象
      \Log::warning("自動承実行");
      $ask = $ask->change(['status'=>'commit', 'login_user_id'=>$ask->create_user_id]);
    }
    $ask->remind_mail($form['create_user_id']);
    return $ask;
  }
  /*
  public function start_date($format = "Y年n月j日"){
    return $this->dateweek_format($this->start_date, $format);
  }
  public function end_date($format = "Y年n月j日"){
    return $this->dateweek_format($this->end_date, $format);
  }
  */
  public function change($form, $file=null, $is_file_delete = false){
    if(isset($form["status"]) && isset($form["login_user_id"])){
      $this->_change($form);
      $this->update(['status'=>$form['status']]);
      $this->cache_delete();
    }
    return $this;
  }
  public function dispose(){
    $target_model = null;
    AskComment::where('ask_id', $this->id)->delete();
    $this->delete();
    $this->cache_delete();
  }
  public function end_dateweek(){
    $d = date('n月j日',  strtotime($this->end_date));
    $d .= '('.config('week')[date('w',  strtotime($this->end_date))].')';
    return $d;
  }
  public function getTypeNameAttribute(){
    return $this->type_name();
  }
  public function type_name()
  {
    return $this->config_attribute_name('ask_type', $this->type);
  }
  public function status_name()
  {
    return $this->config_attribute_name('ask_status', $this->status);
  }
  public function details(){
    $item = $this;
    $item["type_name"] = $this->type_name();
    $item["status_name"] = $this->status_name();
    $item["label_start_date"] = $this->dateweek_format($this->start_date, "Y年n月j日");
    $item["label_end_date"] = $this->dateweek_format($this->end_date, "Y年n月j日");
    $item["duration"] = $item["label_start_date"].'～'.$this->dateweek_format($this->end_date, "n月j日");
    if($this->charge_user_id==1) $item["charge_user_name"] = "事務";
    else $item["charge_user_name"] = $this->charge_user->get_name();

    $item["target_user_name"] = $this->target_user->get_name();
    $item["create_user_name"] = $this->create_user->get_name();
    $item["end_dateweek"] = $this->end_dateweek();
    return $item;
  }
  public function _change($form){
    $ret = false;
    $is_commit = false;
    $is_complete = false;
    if(!isset($form['status']) || !isset($form['login_user_id'])) return false;
    $status = $form['status'];
    if($status == 'commit'){
      $is_commit = true;
    }
    $login_user_id = $form['login_user_id'];
    $target_model_data = $this->get_target_model_data();
    if($target_model_data==null) return false;
    switch($this->type){
      case "recess":
      case "unsubscribe":
        $start_date = null;
        $end_date = null;
        if($is_commit==true){
          $start_date = $this->start_date;
          $end_date = $this->end_date;
        }
        if($this->type=="recess"){
          $target_model_data->recess_commit($is_commit, $start_date, $end_date);
        }
        else if($this->type=="unsubscribe"){
          $target_model_data->unsubscribe_commit($is_commit, $start_date);
        }
        break;
      case "hope_to_join":
        $ret = true;
        $is_complete = true;
        //Trial->hope_to_join()を実行
        $form['create_user_id'] = $login_user_id;
        $target_model_data->hope_to_join($is_commit, $form);
        break;
      case "agreement":
        $is_complete = true;
        $this->get_target_model_data()->student->regular();
      case "agreement_confirm":
        $ret = true;
        //agreementを更新
        $agreement = $this->get_target_model_data();
        $old_agreements = $agreement->student->enable_agreements_by_type('normal');
        if($old_agreements->count() > 0){
          $old_agreements->update(['end_date' => date('Y/m/d H:i:s')]);
        }
        $agreement->start_date =  date('Y/m/d H:i:s');
        $agreement->status = 'commit';
        $agreement->save();
        break;
      case "rest_cancel":
        $ret = true;
        $is_complete = true;
        $target_model_data->rest_cancel($is_commit);
        break;
      case "lecture_cancel":
        $ret = true;
        $is_complete = true;
        $target_model_data->lecture_cancel($is_commit);
        break;
      case "teacher_change":
        $ret = true;
        //代講承認された
        $target_model_data->teacher_change($is_commit, $this->charge_user_id,$this->target_user_id);
        break;
    }
    if($is_complete==true){
      $this->complete();
    }
    return $ret;
  }
  public function complete(){
    $this->update(['status' => 'complete']);
  }
  //依頼に関する通知
  public function remind_mail($login_user_id, $is_remind=false){
    $res = false;
    $param = [];
    $param['ask'] = $this->details();
    $param['send_to'] = 'teacher';
    $param['login_user'] = User::where('id', $login_user_id)->first()->details();
    $target_model_data = $this->get_target_model_data();
    if($target_model_data==null) return false;
    switch($this->type){
      case "hope_to_join":
      case "agreement":
      case "agreement_confirm":
      case "recess":
      case "unsubscribe":
        $param['target_model'] = $target_model_data;
        break;
      case "teacher_change":
        $param["item"] = $target_model_data->details($this->target_user_id);
        break;
      case "rest_cancel":
      case "lecture_cancel":
      case "emergency_lecture_cancel":
      case "late_arrival":
        $param["item"] = $target_model_data->calendar->details($this->target_user_id);
        break;
    }
    //依頼対象者にメール通知
    $res = $this->charge_user_mail($param);

    if($this->type=='lecture_cancel' && $this->status=='commit'){
      $res = $this->target_user_mail($param);
      //生徒あてに休講連絡
      \Log::warning("休講更新[".$this->type."][".$this->status."]");
      $param['send_to'] = 'student';
      $param["item"]->student_mail('休講のお知らせ', $param, 'text', 'calendar_lecture_cancel');
    }
    else if($this->type=='teacher_change' && $this->status=='commit'){
      //生徒あてに先生が変わったあとの連絡
      \Log::warning("代講更新[".$this->type."][".$this->status."]");
      $param['send_to'] = 'student';
      $param['prev_teacher_name'] = $this->target_user->details()["name"];
      $param['next_teacher_name'] = $param["item"]->user->details()->name();

      $param["item"]->student_mail('講師変更のお知らせ', $param, 'text', 'calendar_teacher_change');
      $res = $this->target_user_mail($param);
    }
    else {
      $res = $this->target_user_mail($param);
    }
    return $res;
  }
  public function get_mail_title(){
    if($this->type == "agreement" && $this->status == "commit"){
      $title = " 入会手続き完了のお知らせ及びユーザー登録のお願い";
    }
    else if($this->type == "hope_to_join" && $this->status == "commit"){
      $title = " 入会希望、ありがとうございます";
    }
    else{
      $title = $this->type_name();//.':'.$this->status_name();
    }
    return $title;
  }
  public function target_user_mail($param){
    $template = 'ask_'.$this->type.'_'.$this->status;
    if (!View::exists('emails.'.$template.'_text')) {
      return false;
    }
    if($this->target_user_id==1) return false;
    $title = $this->get_mail_title();
    $param['send_to'] = 'teacher';
    $param['ask'] = $this;
    if($this->target_user->details('students')->role=='student'){
      $param['send_to'] = 'student';
    }
    $param["user_name"] = $this->target_user->details()["name"];
    $param["access_key"] = $this->target_user->access_key;
    $param['is_send_to_target_user'] = true;
    return $this->send_mail($this->target_user_id, $title, $param, 'text', $template);
  }
  public function charge_user_mail($param){
    $template = 'ask_'.$this->type.'_'.$this->status;
	if (!View::exists('emails.'.$template.'_text')) {
      return false;
    }    if($this->charge_user_id==1) return false;
    if($this->charge_user_id==$this->target_user_id) return false;

    $title = $this->get_mail_title();
    \Log::info("charge_user_mail=".$title);
    $param['send_to'] = 'teacher';
    $param['ask'] = $this;
    if($this->charge_user->details('students')->role=='student'){
      $param['send_to'] = 'student';
    }
    $param["user_name"] = $this->charge_user->details()["name"];
    $param["access_key"] = $this->charge_user->access_key;
    $param['is_send_to_target_user'] = false;

    return $this->send_mail($this->charge_user_id, $title, $param, 'text', $template);
  }
  public function is_auto_commit(){
    \Log::warning("自動承認：ask[".$this->id."][".$this->type."]");
    $ret = false;
    $target_model_data = $this->get_target_model_data();
    if($target_model_data==null) return false;
    switch($this->type){
      case "recess":
      case "unsubscribe":
        if($this->target_model=="students"){
          $p = StudentParent::where('user_id', $this->target_user_id)->first();
          if(!isset($p)){
            \Log::error("保護者が存在しない：ask[".$this->id."][".$this->type."]");
            return false;
          }
          if($target_model_data->is_parent($p->id)!=true) return false;
        }
        else {
          if($target_model_data->user_id != $this->target_user_id) return false;
        }
        $ret = true;
        break;
      case "rest_cancel":
        //事務作業の休み取り消しは可能
        if($target_model_data->calendar->work==9){
          $ret = true;
        }
        break;
      case "lecture_cancel":
        $check = [];
        if($target_model_data->calendar->is_group()==true && $target_model_data->calendar->is_english_talk_lesson()==true) return false;
        \Log::warning("休講自動承認：calendar[".$target_model_data->calendar->id."]");
        $d = date('Ymd',  strtotime($target_model_data->calendar->start_time));
        $check[$d] = true;
        \Log::warning("依頼は2日まで");
        //今月承認された休講を取得
        $asks = Ask::where('type', 'lecture_cancel')
                  ->where('target_user_id', $this->target_user_id)
                  ->where('status', '=', 'commit')
                  ->where('end_date', '>=', date('Y-m-1'))
                  ->where('end_date', '<=', date('Y-m-t'))
                  ->get();
        $c = 0;
        //今月承認された休講が3日以上ある場合、自動承認できない
        foreach($asks as $ask){
          $m = UserCalendarMember::where('id', $ask->target_model_id)->first();
          $d = date('Ymd',  strtotime($m->calendar->start_time));
          $check[$d] = true;
        }
        \Log::warning("休講自動承認：ask.count[".count($check)."]");
        if(count($check) > 2) return false;
        $ret = true;
        break;
    }
    \Log::warning("自動承認：[".$ret."]");
    return $ret;
  }
  public function get_target_model_data(){
    //TODO Laravelのリレーションでいずれ対処する
    $ret = null;
    switch($this->target_model){
      case 'trials':
        $ret = Trial::where('id', $this->target_model_id)->first();
        break;
      case 'students':
        $ret = Student::where('id', $this->target_model_id)->first();
        break;
      case 'teachers':
        $ret = Teacher::where('id', $this->target_model_id)->first();
        break;
      case 'managers':
        $ret = Manager::where('id', $this->target_model_id)->first();
        break;
      case 'user_calendar_members':
        $ret = UserCalendarMember::where('id', $this->target_model_id)->first();
        break;
      case 'user_calendars':
        $ret = UserCalendar::where('id', $this->target_model_id)->first();
        break;
      case 'agreements':
      case 'agreement_confirm':
        $ret = Agreement::where('id', $this->target_model_id)->first();
        break;    }
    return $ret;
  }
  public function is_access($user_id){
    $u = User::where('id', $user_id)->first();
    if(!isset($u)) return false;
    $u = $u->details();
    if($u->role=="manager") return true;
    if($this->charge_user_id == $user_id ) return true;
    if($this->target_user_id == $user_id ) return true;
    if($u->role=="parent"){
      $s = Student::where('user_id', $this->target_user_id)->first();
      if(isset($s) && $s->is_parent($u->id)==true) return true;
      $s = Student::where('user_id', $this->charge_user_id)->first();
      if(isset($s) && $s->is_parent($u->id)==true) return true;
    }
    return false;
  }

  public function scopeFindTargetModel($query, $target_model, $target_model_id){
    return $query->where('target_model', $target_model)->where('target_model_id', $target_model_id);
  }


  public function cache_delete(){
    $this->delete_user_cache($this->charge_user_id);
    $this->delete_user_cache($this->target_user_id);
  }

}
