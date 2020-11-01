<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    //
    protected $table = 'lms.event_types'; //テーブル名を入力(A5MK参照)
    protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)
    public static $rules = array( //必須フィールド
        'role' => 'required',
        'event_name' => 'required'
    );

}
