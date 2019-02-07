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
      'birth_day' => 'required',
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
   *　リレーション：ユーザー（認証アカウント）
   */
  public function user(){
    return $this->belongsTo('App\User', 'user_id');
  }
  /**
   *　リレーション：担当生徒（担当講師）
   */
  public function chargeStudents(){
    return $this->hasMany('App\Models\ChargeStudent');
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
      $this->table.id in (select student_id from charge_students where teacher_id=?)
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
    $this->table.id in (select student_id from student_relations where student_parent_id=?)
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
    foreach($search_words as $_search_word){
      $_like = '%'.$_search_word.'%';
      $query = $query->orWhere('name_last','like', $_like)
        ->orWhere('name_first','like', $_like)
        ->orWhere('kana_last','like', $_like)
        ->orWhere('kana_first','like', $_like);
    }
    return $this->scopeFindEmail($query, $word);
  }

  /**
   *　メソッド：登録
   * @param  Collection $form
   */
  static public function entry($form){
    $ret = [];
    $student_no = UserTag::where('tag_key', 'student_no')->max('tag_value');
    $student_no = intval(ltrim($student_no, '0'))+1;
    $student_no = sprintf('%06d', $student_no);
    $user = User::create([
      'name' => $form['name_last'].' '.$form['name_first'],
      'password' => '-',
      'email' => $student_no,
      'image_id' => $form['gender'],
      'status' => 0,
    ]);
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
    UserTag::create([
      'user_id' => $user->id,
      'tag_key' => 'student_no',
      'tag_value' => $student_no,
      'create_user_id' => $user->id,
    ]);
    return $student;
  }
  /**
   *　メソッド：情報編集
   * @param  Collection $form
   */
  public function profile_update($form){
    $this->update([
      'name_last' => $form['name_last'],
      'name_first' => $form['name_first'],
      'kana_last' => $form['kana_last'],
      'kana_first' => $form['kana_first'],
      'birth_day' => $form['birth_day'],
      'gender' => $form['gender'],
    ]);
    $tag_names = ['school_name', 'grade'];

    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
	      UserTag::setTag($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
    $tag_names = ['lesson_subject', 'lesson_week', 'lesson_place', 'lesson_time', 'lesson_time_holiday'];
    foreach($tag_names as $tag_name){
      if(!empty($form[$tag_name])){
        UserTag::setTags($this->user_id, $tag_name, $form[$tag_name], $form['create_user_id']);
	    }
    }
  }
}
