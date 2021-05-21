<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//データセット
use App\User;
use App\Models\UserTag;
use App\Models\ChargeStudent;
//他
use App\Models\GeneralAttribute;

/**
 * App\Models\Teacher
 *
 * @property int $id
 * @property int $user_id ユーザーID
 * @property string $status ステータス/　trial=体験 / regular=入会 / recess=休会 / unsubscribe=退会
 * @property string $name_first 姓
 * @property string $name_last 名
 * @property string $kana_first 姓カナ
 * @property string $kana_last 名カナ
 * @property int $gender 性別：1=男性 , 2=女性, 0=未設定
 * @property string|null $birth_day 生年月日
 * @property string|null $entry_date 入社日
 * @property string|null $recess_start_date 休会開始日
 * @property string|null $recess_end_date 休会終了日
 * @property string|null $unsubscribe_date 退会日
 * @property string|null $phone_no 生年月日
 * @property string|null $post_no
 * @property string|null $address 住所
 * @property string|null $bank_no 銀行番号
 * @property string|null $bank_branch_no 銀行支店番号
 * @property string|null $bank_account_type 口座種別
 * @property string|null $bank_account_no 銀行口座番号
 * @property string|null $bank_account_name 銀行口座名義
 * @property int $create_user_id 作成者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|ChargeStudent[] $chargeStudents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $create_task
 * @property-read mixed $created_date
 * @property-read mixed $kana
 * @property-read mixed $name
 * @property-read mixed $updated_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StudentRelation[] $relations
 * @property-read \Illuminate\Database\Eloquent\Collection|UserTag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Milestone[] $target_milestone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $target_task
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trial[] $trials
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tuition[] $tuitions
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher chargeSubject($subjects)
 * @method static \Illuminate\Database\Eloquent\Builder|Student fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher findChargeStudent($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher findChargeTeachers($student_ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Student findChild($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Student findEmail($word, $or = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher findParent($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Student findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Student hasTag($tag_key, $tag_value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student hasTags($tag_key, $tag_values)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchSubjects($subjects)
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchWord($word)
 * @mixin \Eloquent
 */
class Teacher extends Student
{
  protected $table = 'common.teachers';
  protected $guarded = array('id');

  public static $rules = array(
    'name_last' => 'required',
    'name_first' => 'required',
    'kana_last' => 'required',
    'kana_first' => 'required',
  );
  /**
   *　リレーション：担当生徒（担当講師）
   */
  public function chargeStudents(){
    return $this->hasMany('App\Models\ChargeStudent', 'teacher_id');
  }
  public function status_name(){
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.teacher_status')[$this->status])){
      $status_name = config('attribute.teacher_status')[$this->status];
    }
    return $status_name;
  }

  public function scopeFindChargeStudent($query, $id)
  {
    $where_raw = <<<EOT
      $this->table.id in (select teacher_id from charge_students where student_id=?)
EOT;
    return $query->whereRaw($where_raw,[$id]);
  }
  public function scopeFindParent($query, $id)
  {
    return $query;
  }
  public function scopeChargeSubject($query, $subjects)
  {
    if(!isset($subjects)) return $query;
    if(count($subjects)<1) return $query;
    $where_raw = "";
    foreach($subjects as $subject){
      $key = $subject->tag_key;
      $value = intval($subject->tag_value);
      $_where_raw = <<<EOT
        $this->table.user_id in (select user_id from common.user_tags where tag_key='$key' and tag_value >= $value)
EOT;
      $where_raw .= 'OR ('.$_where_raw.')';
    }
    $where_raw = '('.trim($where_raw,'OR').')';
    return $query->whereRaw($where_raw,[]);
  }

  static public function entry($form){
    $ret = [];
    $_nos = UserTag::where('tag_key', 'teacher_no')->get();
    $_no = 0;
    foreach($_nos as $__no){
      $__no = $__no['tag_value'];
      $__no = intval(ltrim($__no, '0'));
      if($_no < $__no) $_no = $__no;
    }
    $teacher_no = $_no+1;

    $user = User::create([
        'name' => $form['name_last'].' '.$form['name_first'],
        'email' => $form['email'],
        'image_id' => 3,
        'status' => 1,
        'access_key' => $form['access_key'],
        'locale' => $form['locale'],
        'password' => '-',
    ]);
    $teacher = Teacher::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => '',
      'kana_first' => '',
      'status' => 'trial',
      'user_id' => $user->id,
      'create_user_id' => 1,
    ]);
    UserTag::setTag($user->id,'teacher_no',$teacher_no,$user->id);

    return $teacher;
  }
  public function profile_update($form){
    $update_field = [
      'name_last' => "",
      'name_first' => "",
      'kana_last' => "",
      'kana_first' => "",
      'birth_day' => "",
      'gender' => "",
      'phone_no' => "",
      'post_no' => "",
      'address' => "",
      'bank_no' => "",
      'bank_branch_no' => "",
      'bank_account_type' => "",
      'bank_account_no' => "",
      'bank_account_name' => "",
      'entry_date' => null,
      'unsubscribe_date' => null,
      'status' => '',
    ];
    $update_form = [];
    foreach($update_field as $key => $val){
      if(array_key_exists($key, $form)){
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);

    $charge_subject_level_items = GeneralAttribute::get_items('charge_subject_level_item');
    foreach($charge_subject_level_items as $charge_subject_level_item){
      $tag_names[] = $charge_subject_level_item['attribute_value'];
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }

    //希望シフト
    $lesson_weeks = config('attribute.lesson_week');
    $fields = ["lesson","work", "trial", "season_lesson"];
    foreach($fields as $field){
      $is_setting_find = false;
      $tag_names = [];
      foreach($lesson_weeks as $lesson_week=>$name){
        $tag_name = $field.'_'.$lesson_week.'_time';
        if(isset($form[$tag_name]) && count($form[$tag_name])>0){
          $is_setting_find = true;
        }
        $tag_names[] = $tag_name;
      }
      if($is_setting_find==true){
        //更新する場合
        foreach($tag_names as $tag_name){
          if(isset($form[$tag_name]) && count($form[$tag_name])>0){
            UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
          }
          else {
            //曜日の設定がない場合に削除する必要がある
            UserTag::clearTags($this->user_id, $tag_name);
          }
        }
      }
    }
    $tag_names = ['lesson', "lesson_place", 'kids_lesson', 'english_talk_lesson', 'teacher_character', 'manager_type'];
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name])){
        //設定があれば差し替え
        UserTag::clearTags($this->user_id, $tag_name);
      }
    }
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name]) && count($form[$tag_name])>0){
        //設定があれば差し替え
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
    $tag_names = ['piano_level', 'english_teacher', 'schedule_remark', "skype_name"];
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name])){
        //設定があれば差し替え
        UserTag::clearTags($this->user_id, $tag_name);
      }
    }
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name]) && !empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
    }
    $tag_names = ['schedule_remark'];
    foreach($tag_names as $tag_name){
      if(empty($form[$tag_name])) $form[$tag_name] = '';
      UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
    }
    if(!empty($form['locale'])){
      $this->user->update(['locale' => $form['locale']]);
    }
  }
  public function is_manager(){
    $manager = Manager::where('user_id', $this->user_id)->first();
    if(isset($manager)) return true;
    return false;
  }
  public function to_manager($access_key, $already_manager_id=0, $create_user_id){
    $_create_form =[
      'name_last' => $this->name_last,
      'name_first' => $this->name_first,
      'kana_last' => $this->kana_last,
      'kana_first' => $this->kana_first,
      'birth_day' => $this->birth_day,
      'gender' => $this->gender,
      'phone_no' => $this->phone_no,
      'post_no' => $this->post_no,
      'address' => $this->address,
      'create_user_id' => $create_user_id,
    ];
    $manager = null;
    if(isset($already_manager_id) && $already_manager_id > 0){
      $manager = Manager::where('id', $already_manager_id)->first();
      if(isset($manager)){
        //既存マネージャーのuserを削除ステータス
        if(isset($manager->user)) $manager->user->user_replacement($this->user_id);
        //既存マネージャーのuser_idを差し替え
        $manager->update(['user_id'=>$this->user_id]);
        $manager->profile_update($_create_form);
      }
    }
    else {
      $_create_form['user_id'] = $this->user_id;
      $manager = Manager::entry($_create_form);
      if(isset($manager)) $manager->profile_update($_create_form);
    }
    $this->user->update(['status' => 1,
                          'access_key' => $access_key
                        ]);
    return $manager;
  }
  public function get_charge_students(){
    $items = [];
    $charge_students = (new Student)->findChargeStudent($this->id)->get();

    foreach($charge_students as $charge_student){
      $detail = $charge_student->user->details("students");
      $detail['grade'] = $detail->get_tag_value('grade');
      $items[$detail->id] = $detail;
    }
    return $items;
  }
  public function regular(){
    $this->user->update(['status' => 0]);
    $this->update(['status' => 'regular']);
    return $this;
  }
  public function add_charge_student($student_id, $create_user_id){
    $item = ChargeStudent::where('student_id' , $student_id)->where('teacher_id', $this->id)->first();
    if(!isset($item)){
      $item =  ChargeStudent::create(['student_id' => $student_id,
                     'teacher_id' => $this->id,
                     'create_user_id' => $create_user_id,
                   ]);
      return $this->api_response(200, __('messages.info_add'), "");
    }
    return $this->error_response(__('messages.error_already_reagisted'), '');
  }

  public function scopeFindChargeTeachers($query, $student_ids){
    return $query->whereIn('user_id', function($query) use($student_ids)
          {
            $query->select('user_id')->from('lms.user_calendar_settings')
                ->whereIn('id', function($query) use($student_ids)
                {
                  $query->select('user_calendar_setting_id')
                      ->from('lms.user_calendar_member_settings')
                      ->whereIn('user_id', function($query) use ($student_ids)
                      {
                        $query->select('user_id')
                            ->from('common.students')
                            ->whereIn('id', $student_ids);
                      });
                });
          });
  }

}
