<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Common;
use App\Models\Traits\Scopes;

class Order extends Model
{
    //
    use Common;
    use Scopes;

    protected $table = 'common.orders';
    protected $guarded = array('id');
/*
    public static $rules = array(
        'title' => 'required',
        'body' => 'required',
        'type' => 'required'
    );
*/
    protected $fillable = [
      "title",
      "status",
      'type',
      "ordered_user_id",
      "target_user_id",
      "amount",
      "unit_price",
      "item_type",
      "place_id",
      "orderable_id",
      "orderable_type",
      "remarks",
    ];

    public function place(){
      return $this->belongsTo('App\Models\Place',"place_id");
    }

    public function orderable(){
      return $this->morphTo();
    }

    public function target_user(){
      return $this->belongsTo('App\User','target_user_id');
    }

    public function ordered_user(){
      return $this->belongsTo('App\User','ordered_user_id');
    }

    public function getStatusNameAttribute(){
      return config('attribute.order_status')[$this->status];
    }

    public function getTypeNameAttribute(){
      return config('attribute.order_type')[$this->type];
    }

    public function getTargetUserNameAttribute(){
      return $this->target_user->details()->name();
    }

    public function getOrderedUserNameAttribute(){
      return $this->ordered_user->details()->name();
    }

    public function status_update($status){
      $this->status = $status;
      $this->save();
      return $this;
    }

    public function dispose(){
      $this->delete();
      return null;
    }
}
