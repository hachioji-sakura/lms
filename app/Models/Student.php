<?php

namespace App\Models;
use App;
use App\Models\Image;
use App\Models\StudentRelation;
use App\Models\StudentParent;
use App\Models\UserCalendarSetting;
use App\Models\Ask;
use App\Models\Tuition;
use App\User;
use App\Models\UserTag;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;
use DB;

class Student extends Model
{
  use Common;
  protected $table = 'common.students';
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
  public function tags(){
    return $this->hasMany('App\Models\UserTag', 'user_id', 'user_id');
  }

  /**
   *　プロパティ：年齢
   */
  public function age(){
    if($this->birth_day=='9999-12-31') return '';
    return floor((date("Ymd") - str_replace("-", "", $this->birth_day))/10000);
  }

  /**
   *　プロパティ：氏名
   */
  public function name()
  {
    if(preg_match('/^[^ -~｡-ﾟ\x00-\x1f\t]+$/u', $this->name_last)){
      if (App::isLocale('en') && $this->user->locale!='en') {
        return $this->romaji();
      }
    }
    return $this->name_last . ' ' .$this->name_first;
  }
  /**
   *　プロパティ：氏名カナ
   */
  public function kana()
  {
    if (App::isLocale('en')) {
      return "";
    }
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
      if($this->gender===1) return __('labels.man');
      if($this->gender===2) return __('labels.woman');
      return __('labels.other');
    }
    return "-";
  }
  /**
   *　プロパティ：birth_day
   */
  public function birth_day($format='Y年m月d日')
  {
    if (App::isLocale('en')) {
      $format = 'Y-m-d';
    }
    if(!isset($this->birth_day)) return '-';
    if(empty($this->birth_day)) return '-';
    if($this->birth_day=='9999-12-31') return '-';
    if($this->birth_day=='9999/12/31') return '-';
    $d = date($format, strtotime($this->birth_day));
    return $d;
  }
  /**
   *　プロパティ：学年
   */
  public function grade()
  {
    $grade_name = $this->get_tag_name('grade');
    if(empty($grade_name)) return "-";
    return $grade_name;
  }
  /**
   *　プロパティ：学校名
   */
  public function school_name()
  {
    return $this->get_tag_name('school_name');
  }
  /**
   *　プロパティ：希望レッスン場所
   */
  public function lesson_place()
  {
    return $this->get_tags_name('lesson_place');
  }
  /**
   *　プロパティ：希望レッスン
   */
  public function lesson()
  {
    return $this->get_tags_name('lesson');
  }
  /**
   *　プロパティ：ステータス名
   */
  public function status_name(){
    $status_name = "";
    if(app()->getLocale()=='en') return $this->status;

    if(isset(config('attribute.student_status')[$this->status])){
      $status_name = config('attribute.student_status')[$this->status];
    }
    return $status_name;
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
   *　リレーション：受講料データ
   */
  public function tuitions(){
    return $this->hasMany('App\Models\Tuition', 'student_id');
  }
  /**
   *　リレーション：家族関係
   */
  public function relations(){
    return $this->hasMany('App\Models\StudentRelation', 'student_id');
  }
  /**
   * 体験申し込み
  */
  public function trials()
  {
    return $this->hasMany('App\Models\Trial', 'student_id')->where('status', '!=', 'cancel');
  }

  /**
   *　スコープ：ユーザーステータス
   */
   /*
  public function scopeFindStatuses($query, $statuses)
  {
    $where_raw = <<<EOT
      $this->table.user_id in (select id from users where status in ($statuses))
EOT;
    return $query->whereRaw($where_raw,[]);
  }
  */
  public function scopeFindStatuses($query, $vals, $is_not=false)
  {
    return $this->scopeFieldWhereIn($query, 'status', $vals, $is_not);
  }

  /**
   *　スコープ：タグを持っているか
   */
  public function scopeHasTag($query, $tag_key, $tag_value)
  {
    return $this->scopeHasTags($query, $tag_key, $tag_value);
  }
  /**
  *　スコープ：タグ複数値いずれかを持っているか
   */
  public function scopeHasTags($query, $tag_key, $tag_values)
  {
    if(gettype($tag_values) == "string" || gettype($tag_values) == "integer") $tag_values = explode(',', $tag_values.',');
    return $query->whereIn('user_id' , function ($query) use($tag_key, $tag_values){
          $query->select('user_id')
                  ->from('common.user_tags')
                  ->where('tag_key', $tag_key)
                  ->whereIn('tag_value', $tag_values);
    });
  }
  /**
   *　スコープ：担当生徒
   * @param  Integer $id  講師ID
   */
  public function scopeFindChargeStudent($query, $id)
  {
    //TODO:charge_studentsの追加が必要の場合(charge_studentsにあれば担当生徒とする場合は以下のフィルタを変更する）
    //担当生徒＝講師にその生徒との通常授業の設定があること(charge_studentsテーブルはいったん依存させない）
    $where_raw = <<<EOT
      students.user_id in (
        select user_id from lms.user_calendar_member_settings where user_calendar_setting_id in (
         select id from lms.user_calendar_settings where user_id=(select user_id from common.teachers where id = ?)
        )
)
EOT;
    $query = $query->whereRaw($where_raw,[$id]);
    return $this->scopeFindStatuses($query, ["unsubscribe"], true);
  }
  /**
   *　スコープ：自身の子供
   * @param  Integer $id  保護者ID
   */
  public function scopeFindChild($query, $id)
  {
    $where_raw = <<<EOT
    students.id in (select student_id from common.student_relations where student_parent_id=?)
    AND students.status != 'unsubscribe'
EOT;
    $query = $query->whereRaw($where_raw,[$id]);
    return $this->scopeFindStatuses($query, ["unsubscribe"], true);
  }
  /**
   *　スコープ：メールアドレス　（生徒の場合は、生徒Noが入る）
   * @param  String $word  キーワード
   */
  public function scopeFindEmail($query, $word, $or=false)
  {
    $where_raw = <<<EOT
      $this->table.user_id in (select id from common.users where email like ?)
EOT;
    if($or == true){
      return $query->orWhereRaw($where_raw,['%'.$word.'%']);
    }
    return $query->whereRaw($where_raw,['%'.$word.'%']);
  }
  /**
   *　スコープ：キーワード検索
   * @param  String $word  キーワード
   */
  public function scopeSearchWord($query, $word)
  {
    $search_words = $this->get_search_word_array($word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('name_last','like', $_like)
          ->orWhere('name_first','like', $_like)
          ->orWhere('kana_last','like', $_like)
          ->orWhere('kana_first','like', $_like);
        $query = $this->scopeFindEmail($query, $_search_word, true);
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
      'status' => 'trial',
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
      'entry_date' => null,

    ];
    $update_form = [];
    foreach($update_field as $key => $val){
      if(isset($form[$key])){
        $update_form[$key] = $form[$key];
      }
    }
    $this->update($update_form);
    //1:nタグ
    $tag_names = ['lesson', 'lesson_place', 'kids_lesson', 'english_talk_lesson', 'student_character', 'student_type'];
    //通塾可能曜日・時間帯タグ
    $lesson_weeks = config('attribute.lesson_week');

    foreach($lesson_weeks as $lesson_week=>$name){
      $tag_names[] = 'lesson_'.$lesson_week.'_time';
    }
    foreach($tag_names as $tag_name){
      if(isset($form[$tag_name]) && count($form[$tag_name])>0){
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
      }
      else {
        UserTag::clearTags($this->user_id, $tag_name);
      }
    }
    //1:1タグ
    $tag_names = ['piano_level', 'english_teacher', 'lesson_week_count', 'english_talk_course_type', 'kids_lesson_course_type', 'course_minutes'
      ,'school_name', 'grade'];
    //科目タグ
    $charge_subject_level_items = GeneralAttribute::get_items('charge_subject_level_item');
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
  public function is_juken(){
    $grade = $this->get_tag_value('grade');
    //中３、高３の場合＝受験
    if($grade == 'j3' || $grade == 'h3') return true;

    //小4,5,6の場合かつ、受験希望＝受験
    if($grade == 'e4' || $grade == 'e5' || $grade == 'e6'){
      //事務システムから渡された受験生かどうか
      if($this->user->has_tag('student_type', 'j_juken')) return true;
      //TODO　中２、高２でも受験の場合があるかもしれない
      $subjects = $this->get_charge_subject();
      foreach($subjects as $key => $subject){
        //受験希望科目がある
        if($subject > 1) return true;
      }
    }
    return false;
  }
  public function is_arrowre(){
    if($this->user->has_tag('student_type', 'arrowre')) return true;
    return false;
  }
  public function is_fee_free(){
    //受講料無料の場合
    if($this->user->has_tag('student_type', 'fee_free')) return true;
    return false;
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
    $tags = UserTag::where('user_id', $this->user_id)->where('tag_key', 'like', '%_level')->get();
    foreach($tags as $tag){
      //補習以上可能なものを取得
      if(intval($tag->tag_value) > 1){
        $subjects[$tag->tag_key] = intval($tag->tag_value);
      }
    }
    return $subjects;
  }
  public function getNameAttribute(){
    return $this->name();
  }
  public function getGradeAttribute(){
    return $this->grade();
  }
  public function getSchoolNameAttribute(){
    return $this->school_name();
  }
  public function get_subject($lesson=0){
    $ret = [];
    $lesson = intval($lesson);
    if($lesson===1 || $lesson==0){
      $tags = UserTag::where('user_id', $this->user_id)->where('tag_key', 'like', '%_level')->get();
      foreach($tags as $tag){
        $tag_data = $tag->details();
        if(isset($tag_data['charge_subject_level_item'])){
          if(intval($tag->tag_value) > 1){
            $subject_key = str_replace('_level', '', $tag->tag_key);
            $grade = $tag_data['charge_subject_level_item']->get_parent();
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
  public function recess_duration(){
    if(strtotime($this->recess_end_date) < strtotime('now')) return "";
    if(empty($this->recess_start_date)) return "";
    $ret = date('Y年m月d日',  strtotime($this->recess_start_date));
    $ret .=  '～'.date('Y年m月d日',  strtotime($this->recess_end_date));
    return $ret;
  }
  public function unsubscribe_date_label(){
    if(empty($this->unsubscribe_date)) return "";
    $ret = date('Y年m月d日',  strtotime($this->unsubscribe_date));
    return $ret;
  }

  public function is_first_brother(){
    $relations = StudentRelation::where('student_id', $this->id)->get();
    $parent_id = [];
    foreach($relations as $relation){
      $parent_id[] = $relation->student_parent_id;
    }
    //自分と同じ親を持つ家族関係
    $relations = StudentRelation::whereIn('student_parent_id', $parent_id)->get();
    $c = 0;
    foreach($relations as $relation){
      if($relation->student_id < $this->id){
        //自分より先に入った兄弟がいる場合
        $c++;
      }
    }
    if($c==0) return true;
    return false;
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
  public function is_parent($parent_id){
    $relations = StudentRelation::where('student_id', $this->id)->get();
    foreach($relations as $relation){
      if($relation->student_parent_id == $parent_id) return true;
    }
    return false;
  }
  public function get_calendar_settings($filter){
    $items = UserCalendarSetting::hiddenFilter();
    $items = $items->findUser($this->user_id);
    if(isset($filter["search_status"])){
      $_param = "";
      if(gettype($filter["search_status"]) == "array") $_param  = $filter["search_status"];
      else $_param = explode(',', $filter["search_status"].',');
      $items = $items->findStatuses($_param);
    }
    else {
      $items = $items->enable();
    }
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
    $item = $this->user->details();
    /*
    $subject = [];
    $lessons = [];
    foreach($this->get_tags('lesson') as $tag){
      $lesson = intval($tag["value"]);
      $lessons[] = $lesson;
      $subject[$lesson] = $this->get_subject($lesson);
    }
    $item["lesson"] = $lessons;
    $item["subject"] = $subject;
    */
    return $item;
  }
  public function romaji(){
    return ucfirst($this->kana_to_romaji($this->kana_first)).' '.ucfirst($this->kana_to_romaji($this->kana_last));
  }
  private function kana_to_romaji($str){
    $str = mb_convert_kana($str, 'cHV', 'utf-8');

    $kana = array(
        'きゃ', 'きぃ', 'きゅ', 'きぇ', 'きょ',
        'ぎゃ', 'ぎぃ', 'ぎゅ', 'ぎぇ', 'ぎょ',
        'くぁ', 'くぃ', 'くぅ', 'くぇ', 'くぉ',
        'ぐぁ', 'ぐぃ', 'ぐぅ', 'ぐぇ', 'ぐぉ',
        'しゃ', 'しぃ', 'しゅ', 'しぇ', 'しょ',
        'じゃ', 'じぃ', 'じゅ', 'じぇ', 'じょ',
        'ちゃ', 'ちぃ', 'ちゅ', 'ちぇ', 'ちょ',
        'ぢゃ', 'ぢぃ', 'ぢゅ', 'ぢぇ', 'ぢょ',
        'つぁ', 'つぃ', 'つぇ', 'つぉ',
        'てゃ', 'てぃ', 'てゅ', 'てぇ', 'てょ',
        'でゃ', 'でぃ', 'でぅ', 'でぇ', 'でょ',
        'とぁ', 'とぃ', 'とぅ', 'とぇ', 'とぉ',
        'にゃ', 'にぃ', 'にゅ', 'にぇ', 'にょ',
        'ヴぁ', 'ヴぃ', 'ヴぇ', 'ヴぉ',
        'ひゃ', 'ひぃ', 'ひゅ', 'ひぇ', 'ひょ',
        'ふぁ', 'ふぃ', 'ふぇ', 'ふぉ',
        'ふゃ', 'ふゅ', 'ふょ',
        'びゃ', 'びぃ', 'びゅ', 'びぇ', 'びょ',
        'ヴゃ', 'ヴぃ', 'ヴゅ', 'ヴぇ', 'ヴょ',
        'ぴゃ', 'ぴぃ', 'ぴゅ', 'ぴぇ', 'ぴょ',
        'みゃ', 'みぃ', 'みゅ', 'みぇ', 'みょ',
        'りゃ', 'りぃ', 'りゅ', 'りぇ', 'りょ',
        'うぃ', 'うぇ', 'いぇ'
    );

    $romaji  = array(
        'kya', 'kyi', 'kyu', 'kye', 'kyo',
        'gya', 'gyi', 'gyu', 'gye', 'gyo',
        'qwa', 'qwi', 'qwu', 'qwe', 'qwo',
        'gwa', 'gwi', 'gwu', 'gwe', 'gwo',
        'sya', 'syi', 'syu', 'sye', 'syo',
        'ja', 'jyi', 'ju', 'je', 'jo',
        'cha', 'cyi', 'chu', 'che', 'cho',
        'dya', 'dyi', 'dyu', 'dye', 'dyo',
        'tsa', 'tsi', 'tse', 'tso',
        'tha', 'ti', 'thu', 'the', 'tho',
        'dha', 'di', 'dhu', 'dhe', 'dho',
        'twa', 'twi', 'twu', 'twe', 'two',
        'nya', 'nyi', 'nyu', 'nye', 'nyo',
        'va', 'vi', 've', 'vo',
        'hya', 'hyi', 'hyu', 'hye', 'hyo',
        'fa', 'fi', 'fe', 'fo',
        'fya', 'fyu', 'fyo',
        'bya', 'byi', 'byu', 'bye', 'byo',
        'vya', 'vyi', 'vyu', 'vye', 'vyo',
        'pya', 'pyi', 'pyu', 'pye', 'pyo',
        'mya', 'myi', 'myu', 'mye', 'myo',
        'rya', 'ryi', 'ryu', 'rye', 'ryo',
        'wi', 'we', 'ye'
    );

    $str = $this->kana_replace($str, $kana, $romaji);

    $kana = array(
        'あ', 'い', 'う', 'え', 'お',
        'か', 'き', 'く', 'け', 'こ',
        'さ', 'し', 'す', 'せ', 'そ',
        'た', 'ち', 'つ', 'て', 'と',
        'な', 'に', 'ぬ', 'ね', 'の',
        'は', 'ひ', 'ふ', 'へ', 'ほ',
        'ま', 'み', 'む', 'め', 'も',
        'や', 'ゆ', 'よ',
        'ら', 'り', 'る', 'れ', 'ろ',
        'わ', 'ゐ', 'ゑ', 'を', 'ん',
        'が', 'ぎ', 'ぐ', 'げ', 'ご',
        'ざ', 'じ', 'ず', 'ぜ', 'ぞ',
        'だ', 'ぢ', 'づ', 'で', 'ど',
        'ば', 'び', 'ぶ', 'べ', 'ぼ',
        'ぱ', 'ぴ', 'ぷ', 'ぺ', 'ぽ'
    );

    $romaji = array(
        'a', 'i', 'u', 'e', 'o',
        'ka', 'ki', 'ku', 'ke', 'ko',
        'sa', 'shi', 'su', 'se', 'so',
        'ta', 'chi', 'tsu', 'te', 'to',
        'na', 'ni', 'nu', 'ne', 'no',
        'ha', 'hi', 'fu', 'he', 'ho',
        'ma', 'mi', 'mu', 'me', 'mo',
        'ya', 'yu', 'yo',
        'ra', 'ri', 'ru', 're', 'ro',
        'wa', 'wyi', 'wye', 'wo', 'n',
        'ga', 'gi', 'gu', 'ge', 'go',
        'za', 'ji', 'zu', 'ze', 'zo',
        'da', 'ji', 'du', 'de', 'do',
        'ba', 'bi', 'bu', 'be', 'bo',
        'pa', 'pi', 'pu', 'pe', 'po'
    );
    $str = $this->kana_replace($str, $kana, $romaji);
    $str = preg_replace('/(っ$|っ[^a-z])/u', "xtu", $str);
    $res = preg_match_all('/(っ)(.)/u', $str, $matches);
    if(!empty($res)){
        for($i=0;isset($matches[0][$i]);$i++){
            if($matches[0][$i] == 'っc') $matches[2][$i] = 't';
            $str = preg_replace('/' . $matches[1][$i] . '/u', $matches[2][$i], $str, 1);
        }
    }

    $kana = array(
        'ぁ', 'ぃ', 'ぅ', 'ぇ', 'ぉ',
        'ヵ', 'ヶ', 'っ', 'ゃ', 'ゅ', 'ょ', 'ゎ', '、', '。', '　'
    );

    $romaji = array(
        'a', 'i', 'u', 'e', 'o',
        'ka', 'ke', 'xtu', 'xya', 'xyu', 'xyo', 'xwa', ', ', '.', ' '
    );
    $str = $this->kana_replace($str, $kana, $romaji);

    $str = preg_replace('/^ー|[^a-z]ー/u', '', $str);
    $res = preg_match_all('/(.)(ー)/u', $str, $matches);

    if($res){
        for($i=0;isset($matches[0][$i]);$i++){
            if( $matches[1][$i] == "a" ){ $replace = 'â'; }
            else if( $matches[1][$i] == "i" ){ $replace = 'î'; }
            else if( $matches[1][$i] == "u" ){ $replace = 'û'; }
            else if( $matches[1][$i] == "e" ){ $replace = 'ê'; }
            else if( $matches[1][$i] == "o" ){ $replace = 'ô'; }
            else { $replace = ""; }

            $str = preg_replace('/' . $matches[0][$i] . '/u', $replace, $str, 1);
        }
    }

    return $str;
  }

  private function kana_replace($str, $kana, $romaji)
  {
      $patterns = array();
      foreach($kana as $value){
          $patterns[] = '/' . $value . '/';
      }
      $str = preg_replace($patterns, $romaji, $str);
      return $str;
  }
  public function already_ask_data($type, $login_user_id, $status=['new', 'commit']){
    $form = [
      'type' => $type,
      'status' => $status,
      'target_model' => str_replace('common.', '',$this->table),
      'target_model_id' => $this->id,
      'target_user_id' => $this->user_id,
    ];
    if($type=="emergency_lecture_cancel"){
      $form["start_date"] = date('Y-m-d');
    }
    $already_data = Ask::already_data($form);
    return $already_data;
  }
  public function unsubscribe_commit($is_commit, $start_date=''){
    if($is_commit==true){
      $this->update([
        'unsubscribe_date' => $start_date,
      ]);
      $this->unsubscribe();
    }
  }
  public function unsubscribe(){
    if($this->status!='regular') return null;
    $user_calendar_members = [];

    //退会以降の授業予定をキャンセルにする
    $calendars = UserCalendar::rangeDate($this->unsubscribe_date)
                  ->findUser($this->user_id)
                  ->where('status', 'fix')
                  ->get();

    foreach($calendars as $calendar){
      foreach($calendar->members as $member){
        if($member->user_id != $this->user_id) continue;
        $member->status_update('cancel', '退会のため', 1, false, true);
        $user_calendar_members[$member->id] = $member;
      }
    }
    if(strtotime($this->unsubscribe_date) <= strtotime(date('Y-m-d'))){
      //退会開始日経過＝ステータスを退会
      $this->update(['status' => 'unsubscribe']);
      $this->user->update(['status' => 9]);
    }
    return ['user_calendar_members' => $user_calendar_members,];
  }
  public function recess_commit($is_commit, $start_date='', $end_date=''){
    if($is_commit==true){
      $this->update([
        'recess_start_date' => $start_date,
        'recess_end_date' => $end_date,
      ]);
      $this->recess();
    }
  }
  public function recess(){

    if(strtotime($this->recess_end_date) < strtotime(date('Y-m-d'))){
      //休会終了の場合
      return $this->recess_end();
    }

    if($this->status!='regular') return null;

    $user_calendar_members = [];
    //休会範囲の授業予定をキャンセルにする
    $calendars = UserCalendar::rangeDate($this->recess_start_date, $this->recess_end_date)
                  ->findUser($this->user_id)
                  ->where('status', 'fix')
                  ->get();
    foreach($calendars as $calendar){
      foreach($calendar->members as $member){
        if($member->user_id != $this->user_id) continue;
        $member->status_update('cancel', '休会のため', 1, false, true);
        $user_calendar_members[$member->id] = $member;
      }
    }
    if(strtotime($this->recess_start_date) <= strtotime(date('Y-m-d'))){
      //休会開始日経過＝ステータスを休会
      $this->update(['status' => 'recess']);
    }
    return ['user_calendar_members' => $user_calendar_members,];
  }
  public function recess_cancel(){
    return $this->_recess_cancel();
  }
  public function unsubscribe_cancel(){
    return $this->_recess_cancel('unsubscribe');
  }
  public function _recess_cancel($type='recess'){
    $type_name = '休会';
    if($type=='unsubscribe') $type_name = '退会';
    $param = [];
    $user_calendar_members = [];
    $conflict_calendar_members = [];

    if($type=='unsubscribe'){
      $calendars = UserCalendar::rangeDate($this->unsubscribe_date);
    }
    else {
      $calendars = UserCalendar::rangeDate($this->recess_start_date, $this->recess_end_date);
    }

    $calendars = $calendars->findUser($this->user_id)
                  ->findStatuses(['fix','cancel'])
                  ->get();

    foreach($calendars as $calendar){
      $is_update = false;
      foreach($calendar->members as $member){
        if($member->user_id != $this->user_id) continue;
        if($member->remark != $type_name.'のため') continue;
        $conflicts = null;

        if($calendar->is_group()==false){
          $conflicts = UserCalendar::searchDate($calendar->start_time, $calendar->end_time)
                      ->where('id', '!=', $calendar->id)
                      ->where('status', 'fix')
                      ->where('user_id', $calendar->user_id)
                      ->first();
        }


        if(isset($conflicts)){
          //このカレンダーの予定と競合した予定がある場合は、再開できない
          $conflict_calendar_members[$member->id] = $member;
        }
        else {
          $member->status_update('fix', '', 1, false, true);
          $user_calendar_members[$member->id] = $member;
          $is_update = true;
        }
      }
    }
    $param['user_calendar_members'] = $user_calendar_members;
    $param['conflict_calendar_members'] = $conflict_calendar_members;
    $param['send_to'] = 'student';
    $param['type'] = $type;

    if($type=='unsubscribe'){
      $this->update([
        'status' => 'regular',
        'unsubscribe_date' => null,
      ]);
    }
    else {
      $this->update([
        'status' => 'regular',
        'recess_start_date' => null,
        'recess_end_date' => null,
      ]);
    }
    $this->user->send_mail($type_name.'依頼取り消し', $param, 'text', 'recess_cancel');
    return ['user_calendar_members' => $user_calendar_members, 'conflict_calendar_members' => $conflict_calendar_members];
  }
  public function recess_end(){
    if($this->status!='recess') return null;
    $param = [];
    $user_calendar_members = [];
    $conflict_calendar_members = [];

    $this->update(['status' => 'regular']);
    $calendars = UserCalendar::rangeDate($this->recess_end_date)
                  ->findUser($this->user_id)
                  ->findStatuses(['fix','cancel'])
                  ->get();
    foreach($calendars as $calendar){
      $is_update = false;
      foreach($calendar->members as $member){
        if($member->user_id != $this->user_id) continue;
        $conflicts = UserCalendar::searchDate($calendar->start_time, $calendar->end_time)
                    ->where('id', '!=', $calendar->id)
                    ->where('status', 'fix')
                    ->where('user_id', $calendar->user_id)
                    ->first();

        //このカレンダーの予定と競合した予定がある場合は、再開できない
        if(isset($conflicts)){
          $conflict_calendar_members[$member->id] = $member;
        }
        else {
          $member->status_update('fix', '休会から再開するため', 1, false, true);
          $user_calendar_members[$member->id] = $member;
          $is_update = true;
        }
      }
    }
    $param['user_calendar_members'] = $user_calendar_members;
    $param['conflict_calendar_members'] = $conflict_calendar_members;
    $param['send_to'] = 'student';
    $this->user->send_mail('休会終了のお知らせ', $param, 'text', 'recess_end');
    return ['user_calendar_members' => $user_calendar_members, 'conflict_calendar_members' => $conflict_calendar_members];
  }
  public function regular(){
    $this->user->update(['status' => 0]);
    $update_form = ['status' => 'regular'];
    if(isset($this->recess_start_date)){
      $update_form['recess_start_date'] = null;
      $update_form['recess_end_date'] = null;
      $update_form['unsubscribe_date'] = null;
    }
    $this->update($update_form);
    //カレンダー設定を有効にする
    /*
    UserCalendarSetting::findUser($this->user_id)
                  ->where('status', 'new')
                  ->update(['status' => 'fix']);
    */
    //受講料を有効にする
    /*
    Tuition::where('student_id', $this->id)->update(['start_date'=>date('Y-m-d')]);
    */
    /*
    $settings = $this->get_calendar_settings([]);
    $res = [];
    foreach($settings as $setting){
      $res = array_merge($res, $setting->setting_to_calendar(date('Y-m-d')));
    }
    */
  }
  /**
   *　プロパティ：標準学年（年齢から算出）
   * TODO:学年自動設定で使う予定(今は自動設定自体を導入していない）
   */
  public function default_grade($grade_code=null){
    if(empty($this->birth_day)) return "";
    if(empty($grade_code)) $grade_code = $this->default_grade_code();
    if(empty($grade_code)) return "";

    //結果を返す
    if($grade_code>3){
      $grade_index = $grade_code-4;
      if($grade_index > count(config('grade'))) return 'adult';
      $i=0;
      foreach(config('grade') as $index=>$name){
        if($i==$grade_index) return $index;
        $i++;
      }
    }
    return '';
  }
  public function gradeUp(){
    $current_grade = $this->get_tag_value('grade');
    $current_grade_code = $this->grade_to_code($current_grade);
    $current_grade_code++;
    $new_grade = $this->default_grade($current_grade_code);
    UserTag::setTag($this->user_id,'grade',$new_grade,1);
  }
  private function grade_to_code($grade){
    $i = 4;
    foreach(config('grade') as $index=>$name){
      if($grade==$index) return $i;
      $i++;
    }
    return -1;
  }
  private function default_grade_code(){
    if(empty($this->birth_day)) return "";
    $birth = date('Ymd', strtotime($this->birth_day));

    //今日の日付を取得
    $now = date('Ymd');

    //各月日を求める
    $b_y = substr($birth, 0, 4);
    $b_m = substr($birth, 4, 4);
    $n_y = substr($now, 0, 4);
    $n_m = substr($now, 4, 4);
    $m = 0;
    if ($n_m < 400) { //前学期
      $m = 1;
    }
    if($b_m < 402) { //早生まれ
      $n_y++;
    }
    //学年の計算
    $grade_code = $n_y - $b_y - $m;
    return $grade_code;
  }
  public function get_tuition($setting, $is_enable_only=true){
    \Log::warning("------get_tuition start------");
    $lesson = $setting->get_get_tag_value('lesson');

    $tuitions = $this->tuitions->where('lesson', $setting->get_get_tag_value('lesson'))
    ->where('course_type', $setting->get_get_tag_value('course_type'))
    ->where('course_minutes', $setting->course_minutes)
    ->where('lesson_week_count', $this->user->get_enable_calendar_setting_count($lesson))
    ->where('teacher_id', $setting->user->details()->id);
    if($setting->get_get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true){
      $tuitions =  $tuitions->where('subject', $setting->get_get_tag_value('subject'));
    }
    else if($setting->get_get_tag_value('lesson')==4){
      $tuitions =  $tuitions->where('subject', $setting->get_get_tag_value('kids_lesson'));
    }
    $tuitions = $tuitions->sortByDesc('id');
    \Log::warning("------get_tuition end------");
    if(!isset($tuitions)){
      return null;
    }

    foreach($tuitions as $tuition){
      if($is_enable_only==true && $tuition->is_enable()==false) continue;
      \Log::warning("------get_tuition end------");
      return number_format($tuition->tuition);
    }
    //受講料設定なし
    return null;
  }

  public function target_milestone(){
    return $this->hasMany('App\Models\Milestone', 'target_user_id', 'user_id');
  }

  public function target_task(){
    return $this->hasMany('App\Models\Task', 'target_user_id', 'user_id');
  }

  public function create_task(){
    return $this->hasMany('App\Models\Task', 'create_user_id', 'user_id');
  }

  public function get_target_task_count(){
    $query = $this->target_task();
    $status_count['all'] = $query->count();
    $counts = $query->select(DB::raw('count(*) as count,status'))->groupBy('status')->get();
    foreach($counts as $count){
      $status_count[$count['status']] = $count['count'];
    }
    return $status_count;
  }

  public function is_hachiojisakura(){
    //TODO 八王子さくらの生徒の場合True
    if(count($this->trials) > 0) return true;
    //入会後の体験申し込み機能リリース以前の場合
    if(strtotime($this->created_at) < strtotime('2020-09-17 00:00:00')) return true;
    return false;
  }
}
