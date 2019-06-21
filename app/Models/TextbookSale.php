<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextbookSale extends Model
{
  protected $table = 'lms.textbook_sales';
  protected $guarded = array('id');
  public static $rules = array(
      'textbook_id' => 'required',
      'supplier_id' => 'required',
      'price' => 'required',
  );
  public function textbook(){
    return $this->belongsTo('App\Models\Textbook', 'textbook_id');
  }
  public function supplier(){
    return $this->belongsTo('App\Models\Supplier', 'supplier_id');
  }
}
