<?php

namespace App\Models;
use App\Models\Image;
use App\Models\StudentRelation;
use App\Models\StudentParent;
use App\Models\Student;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
  protected $table = 'students';
  protected $guarded = array('id');
  /**
   * 入力ルール
   */
  public static $rules = array(
      'name_last' => 'required',
      'name_first' => 'required',
      'kana_last' => 'required',
      'kana_first' => 'required',
      'gender' => 'required',
  );
  /**
   *　プロパティ：年齢
   */
  public function age(){
    return floor((date("Ymd") - str_replace("-", "", $this->birth_day))/10000);
  }
  /**
   *　プロパティ：氏名
   */
  public function name()
  {
      return $this->name_last . ' ' .$this->name_first;
  }
  /**
   *　プロパティ：氏名カナ
   */
  public function kana()
  {
      return $this->kana_last . ' ' .$this->kana_first;
  }
  /**
   *　プロパティ：アイコン
   */
  public function icon()
  {
      return $this->user->icon();
  }
  /**
   *　プロパティ：性別名称
   */
  public function gender()
  {
    if(isset($this->gender)){
      if($this->gender===1) return "男性";
      if($this->gender===2) return "女性";
      return "その他";
    }
    return "-";
  }
  /**
   *　プロパティ：birth_day
   */
  public function birth_day($format='Y年m月d日')
  {
    if(isset($this->birth_day)){
      return date($format, strtotime($this->birth_day));
    }
    return "-";
  }
  /**
   *　プロパティ：学年
   */
  public function grade()
  {
    return $this->tag_name('grade');
  }
  /**
   *　プロパティ：学校名
   */
  public function school_name()
  {
    return $this->tag_name('school_name');
  }
  /**
   *　プロパティ：希望レッスン場所
   */
  public function lesson_place()
  {
    return $this->tags_name('lesson_place');
  }
  /**
   *　プロパティ：希望レッスン
   */
  public function lesson()
  {
    return $this->tags_name('lesson');
  }
  public function tag_name($key)
  {
    $tag = $this->get_tag($key);
    if(isset($tag)){
      return $tag['name'];
    }
    return "";
  }
  public function tags_name($key)
  {
    $tags = $this->get_tags($key);
    $ret = "";
    if(isset($tags)){
      foreach($tags as $tag){
        $ret .= $tag['name'].',';
      }
      return trim($ret, ',');
    }
    return "";
  }
  /**
   *　プロパティ：親（複数）
   */
  public function parents(){
    $items = StudentRelation::where('student_id', $this->id)->get();
    $parents = [];
    foreach($items as $item){
      $parent = StudentParent::where('id', $item->student_parent_id)->first();
      $parents[] =User::where('id', $this->user_id)->first();
    }
    return $parents;
  }
  /**
   *　プロパティ：タグ取得
   */
  public function get_tag($key)
  {
    $tag = $this->user->get_tag($key);
    if(isset($tag)){
      return ["name" => $tag->name(), "key" => $tag->keyname(), "value" => $tag->tag_value];
    }
    return ["name" => '-', "key" => $key, "value" => ''];
  }
  /**
   *　プロパティ：タグ取得
   */
  public function get_tags($key)
  {
    $tags = $this->user->get_tags($key);
    $ret = null;
    if(isset($tags)){
      $ret = [];
      foreach($tags as $tag){
        $ret[] = ["name" => $tag->name(), "key" => $tag->keyname(), "value" => $tag->tag_value];
      }
      return $ret;
    }
    return null;
  }

  /**
   *　リレーション：ユーザー（認証アカウント）
   */
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  /**
   *　リレーション：担当生徒（担当講師）
   */
  public function chargeStudents(){
    return $this->hasMany('App\Models\ChargeStudent', 'student_id');
  }
  /**
   *　リレーション：家族関係
   */
  public function relations(){
    return $this->hasMany('App\Models\StudentRelation');
  }
  /**
   *　スコープ：ユーザーステータス
   */
  public function scopeFindStatuses($query, $statuses)
  {
    $where_raw = <<<EOT
      $this->table.user_id in (select id from users where status in ($statuses))
EOT;
    return $query->whereRaw($where_raw,[]);
  }
  /**
   *　スコープ：担当生徒
   * @param  Integer $id  講師ID
   */
  public function scopeFindChargeStudent($query, $id)
  {
    $where_raw = <<<EOT
      students.id in (select student_id from charge_students where teacher_id=?)
EOT;
    $query = $query->whereRaw($where_raw,[$id]);
    return $this->scopeFindStatuses($query, "0,1");
  }
  /**
   *　スコープ：自身の子供
   * @param  Integer $id  保護者ID
   */
  public function scopeFindChild($query, $id)
  {
    $where_raw = <<<EOT
    students.id in (select student_id from student_relations where student_parent_id=?)
EOT;
    $query = $query->whereRaw($where_raw,[$id]);
    return $this->scopeFindStatuses($query, "0,1");
  }
  /**
   *　スコープ：メールアドレス　（生徒の場合は、生徒Noが入る）
   * @param  String $word  キーワード
   */
  public function scopeFindEmail($query, $word)
  {
    $where_raw = <<<EOT
      $this->table.user_id in (select id from users where email like ?)
EOT;
    return $query->whereRaw($where_raw,['%'.$word.'%']);
  }
  /**
   *　スコープ：キーワード検索
   * @param  String $word  キーワード
   */
  public function scopeSearchWord($query, $word)
  {
    $search_words = explode(' ', $word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('name_last','like', $_like)
          ->orWhere('name_first','like', $_like)
          ->orWhere('kana_last','like', $_like)
          ->orWhere('kana_first','like', $_like);
      }
    });
    return $query;
  }

  /**
   *　メソッド：登録
   * @param  Collection $form
   */
  static public function entry($form){
    $ret = [];
    $_nos = UserTag::where('tag_key', 'student_no')->get();
    $_no = 0;
    foreach($_nos as $__no){
      $__no = $__no['tag_value'];
      $__no = intval(ltrim($__no, '0'));
      if($_no < $__no) $_no = $__no;
    }
    $student_no = $_no+1;
    //TODO : student_no (id?)は数値
    /*
    $student_no = sprintf('%06d', $student_no);
    */
    $user = User::create([
      'name' => $form['name_last'].' '.$form['name_first'],
      'password' => '-',
      'email' => $student_no,
      'image_id' => $form['gender'],
      'status' => $form['status'],
    ]);
    if(!isset($form['birth_day']) || empty($form['birth_day'])){
      $form['birth_day'] = '9999-12-31';
    }
    $student = Student::create([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => $form['kana_last'],
      'kana_first' => $form['kana_first'],
      'birth_day' => $form['birth_day'],
      'gender' => $form['gender'],
      'user_id' => $user->id,
      'create_user_id' => $user->id,
    ]);
    UserTag::setTag($user->id,'student_no',$student_no,$user->id);

    return $student;
  }
  /**
   *　メソッド：情報編集
   * @param  Collection $form
   */
  public function profile_update($form){
    $update_field = [
      'name_last' => "",
      'name_first' => "",
      'kana_last' => "",
      'kana_first' => "",
      'birth_day' => "9999-12-31",
      'gender' => "",
    ];
    $update_form = [];
    foreach($update_field as $key => $val){
      if(isset($form[$key])){
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);
    //1:nタグ
    $tag_names = ['lesson', 'lesson_place', 'kids_lesson', 'student_character'];
    //通塾可能曜日・時間帯タグ
    $lesson_weeks = GeneralAttribute::findKey('lesson_week')->get();
    foreach($lesson_weeks as $lesson_week){
      $tag_names[] = 'lesson_'.$lesson_week['attribute_value'].'_time';
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    //1:1タグ
    $tag_names = ['piano_level', 'english_teacher', 'school_name', 'grade', 'lesson_week_count', 'course_type', 'course_minutes'];
    //科目タグ
    $charge_subject_level_items = GeneralAttribute::findKey('charge_subject_level_item')->get();
    foreach($charge_subject_level_items as $charge_subject_level_item){
      $tag_names[] = $charge_subject_level_item['attribute_value'];
    }
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
  }
}
