<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Milestone
{
  protected $table = 'lms.faqs';
  protected $guarded = array('id');

  public static $rules = array(
      'title' => 'required',
      'body' => 'required',
      'type' => 'required'
  );
  public function type_name()
  {
    return $this->attribute_name('faq_type', $this->type);
  }
}
