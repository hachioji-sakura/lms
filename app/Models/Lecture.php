<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
