<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    //
    protected $connection = 'mysql_common';
    protected $table = 'common.agreements';
    protected $guarded = array('id');
    protected $fillable = [
      'title',
      'trial_id',
      'entry_fee',
      'monthly_fee',
      'entry_date',
      'status',
      'student_parent_id',
      'create_user_id',
    ];

    protected $attributes = [
      'status' => 'new',
      'create_user_id' =>'1',
    ];

    public static  $rules = Array(
        'title' => 'string',
        'entry_fee' => 'integer',
        'monthly_fee' => 'integer',
        'entry_date' => 'datetime',
        'student_parent_id' => 'integer|required',
      );

    public function student_parent(){
      return $this->belongsTo('App\Models\StudentParent', 'student_parent_id');
    }

    public function agreement_statements(){
      return $this->hasMany('App\Models\AgreementStatement','agreement_id');
    }

    public function getStudentParentNameAttribute(){
      return $this->student_parent->details()->name();
    }

    public function agreement_ask($create_user_id, $access_key){
      //保護者にアクセスキーを設定
      $this->student_parent->user->update(['access_key' => $access_key]);
      //同じ問い合わせがあったら消去
      Ask::where('target_model', 'agreements')->where('target_model_id', $this->id)
          ->where('status', 'new')->where('type', 'agreement')->delete();

      $ask = Ask::add([
        "type" => "agreement",
        "end_date" => date("Y-m-d", strtotime("30 day")),
        "body" => "",
        "target_model" => "agreements",
        "target_model_id" => $this->id,
        "create_user_id" => $create_user_id,
        "target_user_id" => $this->student_parent->user_id,
        "charge_user_id" => 1,
      ]);
      return $ask;
    }

}
