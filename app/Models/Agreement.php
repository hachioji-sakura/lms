<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    //
    protected $connection = 'mysql_common';
    protected $table = 'common.agreements';
    protected $guarded = array('id');
}
