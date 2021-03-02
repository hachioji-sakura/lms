<?php

namespace App\Models;
//データセット
use App\User;
use App\Models\Manager;
use App\Models\UserTag;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Manager
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
 * @property string|null $entry_date 入会日
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChargeStudent[] $chargeStudents
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
 * @method static \Illuminate\Database\Eloquent\Builder|Manager findChargeStudent($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher findChargeTeachers($student_ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Student findChild($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Student findEmail($word, $or = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher findParent($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Student findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Student hasTag($tag_key, $tag_value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student hasTags($tag_key, $tag_values)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchSubjects($subjects)
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Student searchWord($word)
 * @mixin \Eloquent
 */
class Manager extends Teacher
{
  protected $table = 'common.managers';
  public function scopeFindChargeStudent($query, $id)
  {
    return $query;
  }
  static public function entry($form){
    $ret = [];
    $_nos = UserTag::where('tag_key', 'manager_no')->get();
    $_no = 0;
    foreach($_nos as $__no){
      $__no = $__no['tag_value'];
      $__no = intval(ltrim($__no, '0'));
      if($_no < $__no) $_no = $__no;
    }
    $manager_no = $_no+1;
    $user = null;
    if(isset($form['user_id']) && $form['user_id']>0){
      $user = User::where('id', $form['user_id'])->first();
    }
    if(!isset($user)){
      $user = User::create([
          'name' => $form['name_last'].' '.$form['name_first'],
          'email' => $form['email'],
          'image_id' => 4,
          'status' => 1,
          'access_key' => $form['access_key'],
          'password' => '-',
      ]);
    }
    $manager = Manager::where('user_id', $user->id)->first();
    if(!isset($manager)){
      $manager = Manager::create([
        'name_last' => $form['name_last'],
        'name_first' => $form['name_first'],
        'kana_last' => '',
        'kana_first' => '',
        'user_id' => $user->id,
        'create_user_id' => $user->id,
        'status' => 'trial',
      ]);
    }
    UserTag::setTag($user->id,'manager_no',$manager_no,$user->id);

    return $manager;
  }
  public function is_admin(){
    if($this->id==1) return true;
    if($this->user->has_tag('manager_type', 'admin')) return true;
    return false;
  }
}
