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
    public function schoolDetail()
    {
        return $this->hasOne('App\Models\SchoolDetail');
    }

    public function address()
    {
        return $this->schoolDetail->address;
    }

    public function phoneNumber()
    {
        return $this->schoolDetail->phone_number;
    }
}
