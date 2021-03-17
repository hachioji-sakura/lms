<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextbookSale
 *
 * @property int $id
 * @property int $supplier_id 販売会社ID
 * @property int $textbook_id 教科書マスタのID
 * @property int $price 販売価格
 * @property int $list_price 定価
 * @property string $url 販売元ページ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Supplier $supplier
 * @property-read \App\Models\Textbook $textbook
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookSale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookSale query()
 * @mixin \Eloquent
 */
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
