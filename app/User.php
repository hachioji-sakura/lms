<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\UserTag;
use App\Models\Image;
use App\Models\StudentParent;
use App\Notifications\CustomPasswordReset;
use Hash;
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'image_id', 'email', 'password', 'status', 'access_key'
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
      return UserTag::where('user_id', $this->id)
      ->where('tag_key', '!=', 'student_no')
      ->where('tag_key', '!=', 'teacher_no')
        ->get();

      //return $this->hasMany('App\Models\UserTag');
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
    public function Image(){
      return $this->hasOne('App\Models\Image');
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
    public function details(){
      //Manager | Teacher | Studentのいずれかで認証し情報を取り出す
      $image = Image::where('id', $this->image_id)->first();
      $s3_url = '';
      if(isset($image)){
        $s3_url = $image->s3_url;
      }
      $item = Manager::where('user_id', $this->id)->first();
      if(isset($item)){
        $item['manager_id'] = $item['id'];
        $item['role'] = 'manager';
        $item['icon'] = $s3_url;
        $item['email'] = $this->email;
        return $item;
      }
      $item = Teacher::where('user_id', $this->id)->first();
      if(isset($item)){
        $item['teacher_id'] = $item['id'];
        $item['role'] = 'teacher';
        $item['icon'] = $s3_url;
        $item['email'] = $this->email;
        return $item;
      }
      $item = StudentParent::where('user_id', $this->id)->first();
      if(isset($item)){
        $item['role'] = 'parent';
        $item['student_parent_id'] = $item['id'];
        $item['name'] = $item->name_last.' '.$item->name_first;
        $item['kana'] = $item->kana_last.' '.$item->kana_first;
        $item['icon'] = $s3_url;
        $item['email'] = $this->email;
        return $item;
      }
      $item = Student::where('user_id', $this->id)->first();
      if(isset($item)){
        $item['role'] = 'student';
        $item['student_id'] = $item['id'];
        $item['age'] = floor((date("Ymd") - str_replace("-", "", $item['birth_day']))/10000);
        $item['name'] = $item->name_last.' '.$item->name_first;
        $item['kana'] = $item->kana_last.' '.$item->kana_first;
        $item['icon'] = $s3_url;
        $item['email'] = $this->email;
        return $item;
      }
      return $this;
    }
    public function scopeTag($query, $tagkey, $tagvalue)
    {
      $where_raw = <<<EOT
        id in (select user_id from user_tags where tag_key=? and tag_value=?)
EOT;

      return $query->whereRaw($where_raw,[$tagkey, $tagvalue]);
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
    public function aliases(){
      return $this->hasMany('App\Models\UserTag');
    }
}
