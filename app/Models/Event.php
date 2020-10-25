<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $table = 'lms.events'; //テーブル名を入力
    protected $guarded = array('id'); //書き換え禁止領域　(今回の場合はid)

    public static $rules = array( //必須フィールド
        'event_type_id' => 'required',
        'title' => 'required',
        'event_from_date' => 'required',
        'event_to_date' => 'required',
        'response_from_date' => 'required',
        'response_to_date' => 'required'
    );







}
