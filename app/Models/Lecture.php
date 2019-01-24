<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lecture;
use App\Models\GeneralAttribute;

class Lecture extends Model
{
  protected $table = 'lectures';
  protected $guarded = array('id');
  public static $rules = array(
      'lesson' => 'required',
      'course' => 'required',
      'subject' => 'required',
  );
  static public function dataset(){
    $items = Lecture::query();
    $items = $items->orderBy('lesson', 'asc')
                ->orderBy('course', 'asc')
                ->orderBy('subject', 'asc')->get();

    $ret = [];
    foreach($items as $item){
      $detail = $item->details();
      $item['name'] = $detail['name'];
      $lesson = $detail['lesson']['attribute_name'];
      if(!isset($ret[$lesson])){
        $ret[$lesson] = [];
      }
      $course = $detail['course']['attribute_name'];
      if(!isset($ret[$lesson][$course])){
        $ret[$lesson][$course] = [];
      }
      $subject = $detail['subject']['attribute_name'];
      if(!isset($ret[$lesson][$course][$subject])){
        $ret[$lesson][$course][$subject] = $item['id'];
      }
    }
    return $ret;
  }
  public function details(){
    $item = [];
    $item['id'] = $this->id;
    $item['lesson'] = $this->_lesson();
    $item['course'] = $this->_course();
    $item['subject'] = $this->_subject();
    $item['name'] = $item['lesson']['attribute_name'].':'.$item['course']['attribute_name'].':'.$item['subject']['attribute_name'];
    return $item;
  }
  public function _lesson(){
    $item = GeneralAttribute::lesson($this->lesson)->first();
    return $item;
  }
  public function _subject(){
    $item = GeneralAttribute::subject($this->subject)->first();
    return $item;
  }
  public function _course(){
    $item = GeneralAttribute::course($this->course)->first();
    return $item;
  }
}
