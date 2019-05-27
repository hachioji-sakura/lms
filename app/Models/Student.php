<?php

namespace App\Models;
use App\Models\Image;
use App\Models\StudentRelation;
use App\Models\StudentParent;
use App\Models\Student;
use App\Models\UserCalendarSetting;
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
  public function tag_value($key)
  {
    $tag = $this->get_tag($key);
    if(isset($tag)){
      return $tag['value'];
    }
    return "";
  }
  public function tag_name($key)
  {
    $tag = $this->get_tag($key);
    if(isset($tag)){
      return $tag['name'];
    }
    return "";
  }
  public function tags_value($key)
  {
    $tags = $this->get_tags($key);
    $ret = "";
    if(isset($tags)){
      foreach($tags as $tag){
        $ret .= $tag['tags_value'].',';
      }
      return trim($ret, ',');
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
    return $this->hasMany('App\Models\StudentRelation', 'student_id');
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
   *　スコープ：タグを持っているか
   */
  public function scopeHasTag($query, $tag_key, $tag_value)
  {
    $where_raw = <<<EOT
      $this->table.user_id in (select user_id from user_tags where tag_key=? and tag_value=?)
EOT;
    return $query->whereRaw($where_raw,[$tag_key, $tag_value]);
  }
  /**
  *　スコープ：タグ複数値いずれかを持っているか
   */
  public function scopeHasTags($query, $tag_key, $tag_values)
  {
    $where_raw = <<<EOT
      $this->table.user_id in (select user_id from user_tags where tag_key=? and tag_value in ($tag_values))
EOT;
    return $query->whereRaw($where_raw,[$tag_key]);
  }
  /**
   *　スコープ：担当生徒
   * @param  Integer $id  講師ID
   */
  public function scopeFindChargeStudent($query, $id)
  {
    $where_raw = <<<EOT
      students.user_id in (
        select user_id from user_calendar_member_settings where user_calendar_setting_id in (
         select id from user_calendar_settings where user_id=(select user_id from teachers where id = ?)
        )
)
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
    $tag_names = ['lesson', 'lesson_place', 'kids_lesson', 'english_talk_lesson', 'student_character'];
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
    $tag_names = ['piano_level', 'english_teacher', 'lesson_week_count', 'english_talk_course_type', 'kids_lesson_course_type', 'course_minutes'
      ,'school_name', 'grade'];
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
    return $this;
  }
  public function get_brother(){
    $relations =StudentRelation::where('student_id', $this->id)->get();
    $parent_ids = [];
    foreach($relations as $relation){
      $parent_ids[] = $relation->student_parent_id;
    }
    $relations = StudentRelation::findParents($parent_ids)->get();
    $ret = [];

    foreach($relations as $relation){
      if($relation->student_id != $this->id){
        $ret[$relation->student_id] = $relation->student;
      }
    }
    return $ret;
  }
  public function get_charge_subject(){
    //担当科目を取得
    $subjects = [];
    $tags = $this->user->tags;
    foreach($this->user->tags as $tag){
      $tag_data = $tag->details();
      if(isset($tag_data['charge_subject_level_item'])){
        //補習以上可能なものを取得
        if(intval($tag->tag_value) > 1){
          $subjects[$tag->tag_key] = intval($tag->tag_value);
        }
      }
    }
    return $subjects;
  }
  public function get_subject($lesson=0){
    $ret = [];
    $lesson = intval($lesson);
    if($lesson===1 || $lesson==0){
      $tags = $this->user->tags;
      foreach($this->user->tags as $tag){
        $tag_data = $tag->details();
        if(isset($tag_data['charge_subject_level_item'])){
          if(intval($tag->tag_value) > 1){
            $subject_key = str_replace('_level', '', $tag->tag_key);
            $grade = $tag_data['charge_subject_level_item']->parent();
            if(isset($grade)) $grade = $grade->parent_attribute_value;
            else $grade = "";
            $ret[$subject_key] = [
              "subject_key" => $subject_key,
              "subject_name" => $tag->keyname(),  //科目名
              "level_name" => $tag->name(), //補習可能、受験可能など
              "style" => "secondary",
              "grade" => $grade,
            ];
          }
        }
      }
    }
    else if($lesson===3 || $lesson==0){
      //ピアノの場合特に判断基準なし
      $ret['piano'] = [
        "subject_key" => 'piano',
        "subject_name" => 'ピアノ',  //科目名
        "level_name" => '',
        "style" => "primary",
      ];
    }
    else if($lesson==4 || $lesson==2 || $lesson==0){
      $key_name = 'kids_lesson';
      if($lesson==2){
        $key_name = 'english_talk_lesson';
      }
      foreach($this->user->tags as $tag){
        if($tag->tag_key !== $key_name) continue;
        //対応可能
        $ret[$tag->tag_value] = [
          "subject_key" => $tag->tag_value,
          "subject_name" => $tag->name(),
          "style" => "secondary",
        ];
      }
    }
    return $ret;
  }
  public function is_family($student_id){
    $relations = StudentRelation::where('student_id', $this->id)->get();
    $relations2 = StudentRelation::where('student_id', $student_id)->get();
    foreach($relations as $relation){
      foreach($relations2 as $relation2){
        if($relation2->student_parent_id == $relation->student_parent_id) return true;
      }
    }
    return false;
  }
  public function get_calendar_settings($filter){
    $items = UserCalendarSetting::findUser($this->user_id);
    $items = $items->enable();

    if(isset($filter["search_place"])){
      $_param = "";
      if(gettype($filter["search_place"]) == "array") $_param  = $filter["search_place"];
      else $_param = explode(',', $filter["search_place"].',');
      $items = $items->findPlaces($_param);
    }
    if(isset($filter["search_work"])){
      $_param = "";
      if(gettype($filter["search_work"]) == "array") $_param  = $filter["search_work"];
      else $_param = explode(',', $filter["search_work"].',');
      $items = $items->findWorks($_param);
    }
    if(isset($filter["search_week"])){
      $_param = "";
      if(gettype($filter["search_week"]) == "array") $_param  = $filter["search_week"];
      else $_param = explode(',', $filter["search_week"].',');
      $items = $items->findWeeks($_param);
    }
    $items = $items->orderByWeek()
    ->get();
    foreach($items as $key=>$item){
      $items[$key] = $item->details();
    }
    return $items;
  }
  public function details(){
    $item = $this;
    $subject = [];
    $lessons = [];
    foreach($this->get_tags('lesson') as $tag){
      $lesson = intval($tag["value"]);
      $lessons[] = $lesson;
      $subject[$lesson] = $this->get_subject($lesson);
    }
    $item["lesson"] = $lessons;
    $item["subject"] = $subject;
    return $item;
  }
}
