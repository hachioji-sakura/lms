<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;

class AgreementStatement extends Model
{
    //
    use Common;
    protected $table = 'common.agreement_statements';
    protected $guarded = array('id');
    protected $fillable = [
      'title',
      'teacher_id',
      'agreement_id',
      'tuition',
      'title',
      'lesson_id',
      'course_type',
      'course_minutes',
      'subject',
      'grade',
      'lesson_week_count',
      'is_exam',
      'remark',
    ];

    public function agreement(){
      return $this->belongsTo('App\Models\Agreement','agreement_id');
    }

    public function teacher(){
      return $this->belongsTo('App\Models\Teacher','teacher_id');
    }

    public function scopeEnable($query){
      return $query->whereHas('agreement',function($query){
        $query->where('status','commit');
      });
    }
    public function user_calendar_member_settings(){
      return $this->belongsToMany('App\Models\UserCalendarMemberSetting','common.user_calendar_member_setting_agreement_statement','agreement_statement_id','user_calendar_member_setting_id')->withTimestamps();
    }

    public function getCourseTypeNameAttribute(){
      return $this->attribute_name('course_type',$this->course_type);
    }

    public function getCourseMinutesNameAttribute(){
      return $this->attribute_name('course_minutes',$this->course_minutes);
    }

    public function getTeacherNameAttribute(){
      return $this->teacher->name();
    }

    public function getLessonNameAttribute(){
      return config('attribute.lesson')[$this->lesson_id];
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

    public function is_already_registered($form){
      $already_data = AgreementStatement::where('student_id' , $form['student_id'])
      ->where('teacher_id' , $form['teacher_id'])
      ->where('lesson' , $form['lesson'])
      ->where('course_type' , $form['course_type'])
      ->where('course_minutes' , $form['course_minutes'])
      ->where('grade' , $form['grade'])
      ->where('lesson_week_count' , $form['lesson_week_count'])
      ->where('subject' , $form['subject'])
      ->where('agreement_id',$form['agreement_id'])->get();
      if(isset($already_data) && count($already_data)>0){
        \Log::warning("tuition : already");
        return null;
      }
    }
}
