<?php

namespace App\Models;
use App;
use App\Models\Image;
use App\Models\StudentRelation;
use App\Models\StudentParent;
use App\Models\Student;
use App\Models\UserCalendarSetting;
use App\Models\Ask;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
  protected $connection = 'mysql_common';
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
    if(preg_match('/^[^ -~｡-ﾟ\x00-\x1f\t]+$/u', $this->name_last)){
      if (App::isLocale('en') && $this->user->get_locale()!='en') {
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
   *　リレーション：受講料データ
   */
  public function tuitions(){
    return $this->hasMany('App\Models\Tution', 'student_id');
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
  public function scopeFindEmail($query, $word)
  {
    $where_raw = <<<EOT
      $this->table.user_id in (select id from common.users where email like ?)
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
  public function scopeFieldWhereIn($query, $field, $vals, $is_not=false)
  {
    if(count($vals) > 0){
      if($is_not===true){
        $query = $query->whereNotIn($field, $vals);
      }
      else {
        $query = $query->whereIn($field, $vals);
      }
    }
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
  public function is_juken(){
    //事務システムから渡された受験生かどうか
    if($this->user->has_tag('student_type', 'juken')) return true;
    //中３、高３の場合
    if($this->user->has_tag('grade', 'j3')) return true;
    if($this->user->has_tag('grade', 'h3')) return true;
    $subjects = $this->get_charge_subject();
    foreach($subjects as $key => $subject){
      //受験希望科目がある
      if($subject > 1) return true;
    }
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
  public function is_parent($parent_id){
    $relations = StudentRelation::where('student_id', $this->id)->get();
    foreach($relations as $relation){
      if($relation->student_parent_id == $parent_id) return true;
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
  public function already_recess_data($login_user_id){
    $already_data = Ask::already_data('recess', [
      'status' => 'commit',
      'target_model' => str_replace('common.', '',$this->table),
      'target_model_id' => $this->id,
      'target_user_id' => $login_user_id,
    ]);
    return $already_data;
  }
  public function already_unsubscribe_data($login_user_id){
    $already_data = Ask::already_data('unsubscribe', [
      'status' => 'commit',
      'target_model' => str_replace('common.', '',$this->table),
      'target_model_id' => $this->id,
      'target_user_id' => $login_user_id,
    ]);
    return $already_data;
  }
  public function unsubscribe(){
    if($this->status!='regular') return null;
    $user_calendar_members = [];
    $this->update(['status' => 'unsubscribe']);
    $calendars = UserCalendar::rangeDate($this->unsubscribe_date)
                  ->findUser($this->user_id)
                  ->where('status', 'fix')
                  ->get();

    foreach($calendars as $calendar){
      $is_update = false;
      foreach($calendar->members as $member){
        if($member->user_id != $this->user_id) continue;
        $member->status_update('cancel', '退会のため', 1, false, true);
        $user_calendar_members[$member->id] = $member;
        $is_update = true;
      }
    }
    return ['user_calendar_members' => $user_calendar_members,];
  }
  public function recess(){
    if($this->status!='regular') return null;
    $user_calendar_members = [];
    $this->update(['status' => 'recess']);
    $calendars = UserCalendar::rangeDate($this->recess_start_date, $this->recess_end_date)
                  ->findUser($this->user_id)
                  ->where('status', 'fix')
                  ->get();
    foreach($calendars as $calendar){
      $is_update = false;
      foreach($calendar->members as $member){
        if($member->user_id != $this->user_id) continue;
        $member->status_update('cancel', '休会のため', 1, false, true);
        $user_calendar_members[$member->id] = $member;
        $is_update = true;
      }
    }
    return ['user_calendar_members' => $user_calendar_members,];
  }
  public function recess_cancel(){
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
  /**
   *　プロパティ：標準学年（年齢から算出）
   * TODO:学年自動設定で使う予定(今は自動設定自体を導入していない）
   */
  public function default_grade(){
    $birth = date('Ymd', strtotime($this->birth_day));
    //今日の日付を取得
    $now = date('Ymd');

    //各月日を求める
    $b_y = substr($birth, 0, 4);
    $b_m = substr($birth, 4, 4);
    $n_y = substr($now, 0, 4);
    $n_m = substr($now, 4, 4);
    if ($n_m < 400) { //前学期
      $m = 6;
    } else { //新学期
      $m = 5;
    }

    if($b_m < 402) { //早生まれ
      $n_y++;
    }
    //学年の計算
    $grade = $n_y - $b_y - $m;

    //結果を返す
    if($grade < 1){
      return 'toddler';
    }
    else if($grade>=1 && $grade<=6){
      return 'e'.$grade;
    }
    else if($grade>=7 && $grade<=9){
      return 'j'.($grade-7);
    }
    else if($grade>=10 && $grade<=12){
      return 'h'.($grade-10);
    }
    else if($grade>=13 && $grade<=16){
      return 'university';
    }
    else if($grade>17){
      return 'adult';
    }
    return '';
  }

}
