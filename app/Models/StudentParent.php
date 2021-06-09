<?php

namespace App\Models;
//データセット
use App\User;
use App\Models\Student;
use App\Models\Trial;
use App\Models\UserTag;
use App\Models\StudentParent;
use App\Models\StudentRelation;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\StudentParent
 *
 * @property int $id
 * @property int $user_id ユーザーID
 * @property string $status ステータス/　trial=体験 / regular=入会 / recess=休会 / unsubscribe=退会
 * @property string $name_first 姓
 * @property string $name_last 名
 * @property string $kana_first 姓カナ
 * @property string $kana_last 名カナ
 * @property string|null $birth_day 生年月日
 * @property string|null $phone_no 生年月日
 * @property string|null $post_no
 * @property string|null $address 住所
 * @property int $create_user_id 作成者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChargeStudent[] $chargeStudents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $create_task
 * @property-read mixed $created_date
 * @property-read mixed $kana
 * @property-read mixed $name
 * @property-read mixed $updated_date
 * @property-read \Illuminate\Database\Eloquent\Collection|StudentRelation[] $relations
 * @property-read \Illuminate\Database\Eloquent\Collection|UserTag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Milestone[] $target_milestone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $target_task
 * @property-read \Illuminate\Database\Eloquent\Collection|Trial[] $trials
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
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParent query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchSubjects($subjects)
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchWord($word)
 * @mixin \Eloquent
 */
class StudentParent extends Teacher
{
  protected $table = 'common.student_parents';
  protected $guarded = array('id');
  protected $status_key_name = 'attribute.student_status';

  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
  );
  public function user(){
    return $this->belongsTo('App\User');
  }
  public function relations(){
    return $this->hasMany('App\Models\StudentRelation', 'student_parent_id');
  }
  public function agreements(){
    return $this->hasMany('App\Models\Agreement','student_parent_id');
  }
  public function get_agreements_by_status($status){
    return $this->hasMany('App\Models\Agreement','student_parent_id')->where('status',$status);
  }
  public function name()
  {
    $name = $this->name_last . ' ' .$this->name_first;
    if(empty(trim($name))){
      $child = $this->relation()->first();
      if(isset($child)){
        $name = $child->student->name();
      }
    }
    return $name;
  }
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  static public function entry($form){
    $ret = [];

    $parent_user = User::where('email', $form['email'])->first();
    $parent = null;
    if(isset($parent_user)){
      $parent = StudentParent::where('user_id', $parent_user->id)->first();
      if(!isset($parent)) return null;
    }
    else {
      $parent_user = User::create([
          'name' => $form['name_last'].' '.$form['name_first'],
          'email' => $form['email'],
          'image_id' => 4,
          'status' => 1,
          'access_key' => $form['access_key'],
          'password' => '-',
      ]);
      $parent = StudentParent::create([
        'name_last' => $form['name_last'],
        'name_first' => $form['name_first'],
        'phone_no' => $form['phone_no'],
        'post_no' => $form['post_no'],
        'address' => $form['address'],
        'kana_last' => '',
        'kana_first' => '',
        'user_id' => $parent_user->id,
        'create_user_id' => 1,
        'status' => 'trial',
      ]);
    }
    return $parent;
  }

  public function brother_add($form, $status=0){
    $ret = [];
    $student = $this->get_child($form['name_last'], $form['name_first']);
    if(isset($student)){
      //すでに同姓同名の子供を登録済み
      return $student;
    }
    $form['create_user_id'] = $this->user_id;
    $form['status'] = $status;
    $student = Student::entry($form);
    StudentRelation::create([
      'student_id' => $student->id,
      'student_parent_id' => $this->id,
      'create_user_id' => $this->user_id,
    ]);
    $student->profile_update($form);
    return $student;
  }
  public function get_child($name_last, $name_first){
    foreach($this->relation() as $relation){
      if($relation->student->name_last ==$name_last &&
        $relation->student->name_first == $name_first ){
          return $relation->student;
      }
    }
    return null;
  }
  public function profile_update($form){
    $update_fields = [
      'name_last' => "",
      'name_first' => "",
      'kana_last' => "",
      'kana_first' => "",
      'phone_no' => "",
      'post_no' => "",
      'address' => "",
      'status' => "",
      'entry_date' => null,
      'unsubscribe_date' => null,
    ];
    $update_form = [];
    foreach($update_fields as $key => $val){
      if(isset($form[$key])){
        $update_form[$key] = $form[$key];
      }
    }
    if(!empty($form['parent_name_last']) && empty($form['name_last'])){
      $update_form['name_last'] = $form['parent_name_last'];
    }
    if(!empty($form['parent_name_first']) && empty($form['name_first'])){
      $update_form['name_first'] = $form['parent_name_first'];
    }
    if(!empty($form['parent_kana_last']) && empty($form['kana_last'])){
      $update_form['kana_last'] = $form['parent_kana_last'];
    }
    if(!empty($form['parent_kana_first']) && empty($form['kana_first'])){
      $update_form['kana_first'] = $form['parent_kana_first'];
    }

    $this->update($update_form);
    return $this;
  }
  public function relation(){
    $items = StudentRelation::where('student_parent_id', $this->id)->get();
    return $items;
  }
  public function get_enable_students(){
    $relations = $this->relation();
    $students = [];
    foreach($relations as $relation){
      if($relation->student->status=='regular' || $relation->student->status=='recess'){
        $students[] = $relation->student;
      }
    }
    return $students;
  }
  public function details(){
    $item = $this;
    $students = [];
    //$item['relations'] = $this->relations;
    foreach($this->relation() as $relation){
      $students[] = $relation->student->details();
    }
    unset($item->user);
    $item['students'] = $students;
    $item['email'] = $this->user->email;

    return $item;
  }
  
  public function is_hachiojisakura(){
    foreach($this->relation() as $relation){
      if ($relation->student->is_hachiojisakura()){
          return true;
      }
    }
    return false;
  }
}
