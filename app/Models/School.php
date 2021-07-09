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
    public function access()
    {
        return $this->schoolDetail->access;
    }
    public function postNumber()
    {
        return $this->schoolDetail->post_number;
    }
    public function faxNumber()
    {
        return $this->schoolDetail->fax_number;
    }

    public function process()
    {
        $text = '';

        if ($this->fullDayGrade()) {
            $text .= '[全日学年制]' . PHP_EOL;
        }
        if ($this->fullDayCredit()) {
            $text .= '[全日単位制]' . PHP_EOL;
        }
        if ($this->partTimeGradeNightOnly()) {
            $text .= '[定時制学年制夜間]' . PHP_EOL;
        }
        if ($this->partTimeCredit()) {
            $text .= '[定時単位制]' . PHP_EOL;
        }
        if ($this->partTimeCreditNightOnly()) {
            $text .= '[定時単位制夜間]' . PHP_EOL;
        }
        if ($this->onlineSchool()) {
            $text .= '[通信制]' . PHP_EOL;
        }

        return $text;
    }

    public function fullDayGrade()
    {
        return $this->schoolDetail->full_day_grade;
    }
    public function fullDayCredit()
    {
        return $this->schoolDetail->full_day_credit;
    }
    public function partTimeGradeNightOnly()
    {
        return $this->schoolDetail->part_time_grade_night_only;
    }
    public function partTimeCredit()
    {
        return $this->schoolDetail->part_time_credit;
    }
    public function partTimeCreditNightOnly()
    {
        return $this->schoolDetail->part_time_credit_night_only;
    }
    public function onlineSchool()
    {
        return $this->schoolDetail->online_school;
    }

    public function departmentIds()
    {
        if ($this->school_type !== 'high_school') {
            return [];
        }

        $school_department = new SchoolDepartment();
        $school_departments = $school_department->where('school_type_id', $this->id)->get();
        return collect($school_departments)->pluck('id')->all();
    }

    public function departmentNames()
    {
        if ($this->school_type !== 'high_school') {
            return '';
        }

        $school_department = new SchoolDepartment();
        $school_departments = $school_department->where('school_type_id', $this->id)->get();
        $result = '';
        foreach ($school_departments as $school_department) {
            $department = Department::where('id', $school_department->department_id)->first();
            if (!empty($department)) {
                $result .= '[' . $department->department . ']' . PHP_EOL;
            }
        }

        return $result;
    }

    public function phoneNumber()
    {
        return $this->schoolDetail->phone_number;
    }

  public function textbooks(){
    return $this->morphToMany('App\Models\Textbook', 'textbookable')->withTimestamps();
  }

  public function store_school_textbooks($form){
    $this->textbooks()->sync($form['textbooks']);
  }
}
