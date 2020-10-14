<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AgreementStatement;
use Illuminate\Support\Facades\Auth;

class Agreement extends Model
{
    //
    protected $table = 'common.agreements';
    protected $guarded = array('id');
    protected $fillable = [
      'title',
      'student_id',
      'parent_agreement_id',
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
/*
    public static  $rules = Array(
        'title' => 'string',
        'entry_fee' => 'integer',
        'monthly_fee' => 'integer',
        'entry_date' => 'datetime',
        'student_parent_id' => 'integer|required',
      );
*/
    public function student_parent(){
      return $this->belongsTo('App\Models\StudentParent', 'student_parent_id');
    }

    public function student(){
      return $this->belongsTo('App\Models\Student', 'student_id');
    }

    public function agreement_statements(){
      return $this->hasMany('App\Models\AgreementStatement','agreement_id');
    }

    public function getStudentParentNameAttribute(){
      return $this->student_parent->details()->name();
    }

    public function scopeSearch($query, $request){
      if( $request->has('search_word')){
        $query = $query->searchWord($request->get('search_word'));
      }
      if( $request->has('agreement_id')){
        $query = $query->where('agreement_id', $request->get('agreement_id'));
      }
      return $query;
    }

    public function scopeEnable($query){
      return $query->where('status','commit');
    }

    public function scopeSearchWord($query, $word){
      $search_words = $this->get_search_word_array($word);
      $query = $query->where(function($query)use($search_words){
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $query = $query->orWhere('remarks','like', $_like)
            ->orWhere('title','like', $_like);
        }
      });
      return $query;
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

    public function add($request,$status,$parent_agreement_id = null){
      $this->fill($request->get('agreements'));
      $req = $request->get('agreements');
      $this->entry_date = date('Y/m/d H:i:s');
      $student_name = Student::find($req['student_id'])->name();
      $this->title = $student_name . ' : ' . date('Y/m/d');
      $this->status = $status;
      $this->parent_agreement_id = $parent_agreement_id;
      $this->create_user_id = Auth::user()->id;
      $this->save();
      foreach($request->get('agreement_statements') as $form){
        $new_agreement_statement = new AgreementStatement($form);
        $statement_form[$form['setting_key']] = $new_agreement_statement;
      }
      $this->agreement_statements()->saveMany($statement_form);
      foreach($statement_form as $key => $statement){
        $ids = $request->get('agreement_statements')[$key]['user_calendar_member_setting_id'];
        $statement->user_calendar_member_settings()->attach($ids);
      }
      return $this;
    }
}
