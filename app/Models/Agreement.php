<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AgreementStatement;
use Illuminate\Support\Facades\Auth;
use App\Models\Traits\Common;
use App\Models\Traits\WebCache;
use App\Models\Traits\Scopes;

class Agreement extends Model
{
    //
    use WebCache;
    use Scopes;
    use Common;

    protected $table = 'common.agreements';
    protected $guarded = array('id');
    protected $fillable = [
      'title',
      'student_id',
      'type',
      'parent_agreement_id',
      'entry_fee',
      'monthly_fee',
      'commit_date',
      'start_date',
      'end_date',
      'status',
      'consumption_tax_rate',
      'is_tax_include',
      'student_parent_id',
      'create_user_id',
    ];

    protected $attributes = [
      'status' => 'new',
      'create_user_id' =>'1',
    ];

    public function student_parent(){
      return $this->belongsTo('App\Models\StudentParent', 'student_parent_id');
    }

    public function student(){
      return $this->belongsTo('App\Models\Student', 'student_id');
    }

    public function agreement_statements(){
      return $this->hasMany('App\Models\AgreementStatement','agreement_id');
    }

    public function getStatusNameAttribute(){
      return config('attribute.agreement_status')[$this->status];
    }

    public function getStudentParentNameAttribute(){
      return $this->student_parent->details()->name();
    }

    public function getTypeNameAttribute(){
      return config('attribute.agreement_type')[$this->type];
    }


    public function getFormatStartDateAttribute(){
      return $this->dateweek_format($this->start_date);
    }
    
    public function getFormatEndDateAttribute(){
      return $this->dateweek_format($this->end_date);
    }

    public function getStatementSummaryAttribute(){
      $statements = $this->agreement_statements;
      $ret = [];
      foreach($statements as $statement){
        $grade = $statement->grade_name;
        if($statement->is_exam == 1){
          $grade .='受験';
        }
        $ret[]= $statement->lesson_name. ' / 週' . $statement->lesson_week_count  . '回 / '.$grade.' / '. $statement->tuition .'円'.PHP_EOL;
      }
      return $ret;
    }

    public function scopeEnable($query){
      //絞り込みの表示用
      return $query->enableByDate(date('Y-m-d'));
    }

    public function scopeEnableByDate($query,$date = null){
      if(empty($date)){
        $target_date = date('Y-m-d'); 
      }else{
        $target_date = date('Y-m-d',strtotime($date));
      }
      return $query->where('status','commit')
                  ->where('start_date','<=',$date)
                  ->where('end_date','>=',$date);
    }

    public function scopeEnableByType($query,$type){
      return $query->enable()->where('type',$type);
    }

    public function scopeSearchWord($query, $word){
      $search_words = $this->get_search_word_array($word);
      $query = $query->where(function($query)use($search_words){
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $query = $query->orWhere('title','like', $_like);
        }
      });
      return $query;
    }

    public function agreement_ask($create_user_id, $access_key,$ask_type){
      //同じ問い合わせがあったら消去
      Ask::where('target_model', 'agreements')->where('target_model_id', $this->id)
          ->where('status', 'new')->where('type', 'agreement')->delete();

      $ask = Ask::add([
        "type" => $ask_type,
        "end_date" => date("Y-m-d", strtotime("30 day")),
        "body" => "",
        "access_key" => $access_key,
        "target_model" => "agreements",
        "target_model_id" => $this->id,
        "create_user_id" => $create_user_id,
        "target_user_id" => $this->student_parent->user_id,
        "charge_user_id" => 1,
      ]);
      return $ask;
    }

    public function add($request,$status,$parent_agreement_id = null,$type = 'normal'){
      $this->fill($request->get('agreements'));
      $req = $request->get('agreements');
      $this->entry_date = date('Y/m/d H:i:s');
      $student_name = Student::find($req['student_id'])->name();
      $this->title = $student_name . ' : ' . date('Y/m/d');
      $this->status = $status;
      $this->type = $type;
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

    public static function add_from_member_setting($member_id, $date = null){
      if(empty($date)){
        $date = date('Y/m/d');
      }else{
        $date = date('Y/m/d', strtotime($date));
      }
      $member = UserCalendarMemberSetting::find($member_id);
      //基本契約の追加
      
      $setting = $member->setting->details();
      $agreement_form = [
        'title' => $member->user->details()->name() . ' : ' . date('Y/m/d'),
        'type' => 'normal',
        'start_date' => date('Y/m/1',strtotime($date)),
        'end_date' => date('Y/m/t 23:59:59', strtotime($date)),
        'student_id' => $member->user->details()->id,
        'student_parent_id' => $member->user->details()->relations()->first()->student_parent_id,
        'monthly_fee' => $member->user->details()->get_monthly_fee(),
        'entry_fee' => $member->user->details()->get_entry_fee(),
        'status' => 'commit',
      ];
      $new_agreement = new Agreement($agreement_form);
      //契約明細の追加
      $settings = $member->user->monthly_enable_calendar_settings($date);
      $member_ids = [];
      foreach($settings as $st){
        $mb = $st->members->where('user_id',$member->user_id)->first();
        $setting_key = $new_agreement->get_setting_key($st,$mb->user->get_enable_calendar_setting_count($st->lesson(true)));
        $form = [
          'title' => $setting_key,
          'teacher_id' => $st->user->details('teachers')->id,
          'lesson_id' => $st->lesson(true),
          'grade' => $mb->user->details()->get_tag_value('grade'),
          'course_type' => $st->get_tag_value('course_type'),
          'course_minutes' =>  $st['course_minutes'],
          'lesson_week_count' => $mb->user->get_enable_calendar_setting_count($st->lesson(true)),
          'tuition' => $mb->get_tuition_master(),
          'is_exam' => $mb->user->details()->is_juken(),
        ];
        $statement_form[$setting_key] = new AgreementStatement($form);
        if(!isset($member_ids[$setting_key])) $member_ids[$setting_key] = [];
        $member_ids[$setting_key][] = $mb->id;
      }
      
      //契約変更の判定
      $agreement = $member->user->student->prev_agreements->first();
      if(!empty($agreement)){
        $is_update = $agreement->is_same($statement_form);
      }else{
        $is_update = true;
      }

      //消費税率とタイプは前回のものを引き継ぐ
      //新規作成なら税抜きかつconfigの値を読む
      if(!empty($agreement)){
        $new_agreement->is_tax_include = $agreement->is_tax_include;
        $new_agreement->consumption_tax_rate = $agreement->consumption_tax_rate;
      }else{
        $new_agreement->is_tax_include = false;
        $new_agreement->consumption_tax_rate = config('app.consumption_tax_rate');
      }

      //更新があればnew,更新がなければcommitで登録
      if($is_update == true){
        $new_agreement->status = 'new';
      }else{
        $new_agreement->status = 'commit';
        $new_agreement->commit_date = date('Y/m/d',strtotime("first day of this month"));
      }

      if(!empty($agreement)){
        //既存の契約idを取得してparent_idへセット
        $new_agreement->parent_agreement_id = $agreement->id;
      }

      $new_agreement->save();
      $new_agreement->agreement_statements()->saveMany($statement_form);
      foreach($statement_form as $key => $statement){
        $statement->user_calendar_member_settings()->attach($member_ids[$key]);
      }
      return $new_agreement;
    }

    public function get_setting_key($setting,$lesson_week_count){
      return $setting->lesson(true).'_'.$setting['work'].'_'.$setting['course_minutes'].'_'.$lesson_week_count.'_'.$setting->user->details()->id;
    }

    public function is_same($statement_form){
      $counter = 0;
      $is_update = false;
      //元の契約から見て新しい契約に漏れがないか
      foreach ($this->agreement_statements as $statement){
        foreach($statement_form as $key => $value){
          if($statement->statement_key == $key){
            $counter++;
          }
        }
      }
      if($counter == $this->agreement_statements->count()){
        $counter = 0;
        //新しい契約から見て元の契約にも取れがないか
        foreach($statement_form as $key => $value){
          foreach($this->agreement_statements as $statement){
            if($key == $statement->statement_key){
              $counter++;
            }
          }
        }
        if($counter != count($statement_form)){
          $is_update = true;
        }
      }else{
        //最初の判定でずれがあったら更新する
        $is_update = true;
      }

      //金額のチェック
      $sum_tuition = 0;
      foreach($statement_form as $sf){
        $sum_tuition += $sf->tuition;
      }
      if($sum_tuition != $this->agreement_statements->sum('tuition')){
        $is_update = true;
      }

      return $is_update;
    }

    public function is_agreement_confirm_send(){
       $ask_count = Ask::where('target_model','agreements')
                    ->where('target_model_id',$this->id)
                    ->where('type','agreement_confirm')->count();
       if($ask_count > 0){
         return true;
       }else{
         return false;
       }
    }

    public function change($request){
      $this->fill($request->agreements);
      $this->save();
      foreach($this->agreement_statements as $statement){
        $statement->fill($request->agreement_statements[$statement->id]);
        $statement->save();
      }
      return $this;
    }
    
    //メンテナンス用のため通常は使用しない
    //契約は削除しない
    public function dispose(){
      $this->agreement_statements->map(function($item){
        return $item->dispose();
      });
      $this->delete();
    }
}
