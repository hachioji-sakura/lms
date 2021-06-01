<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class School
 *
 * @package App\Models
 */
class School extends Model
{
  public function textbooks(){
    return $this->morphToMany('App\Models\Textbook', 'textbookable')->withTimestamps();
  }

  public function store_school_textbooks($form){
    $this->textbooks()->sync($form['textbooks']);
  }
}
