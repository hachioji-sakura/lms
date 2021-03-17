<?php
namespace App\Models\Traits;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Manager;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
trait Test
{
  public function login($role, $id=0){
    switch($role){
      case "student":
        return $this->student_login($id);
      case "manager":
        return $this->manager_login($id);
      case "teacher":
        return $this->teacher_login($id);
      case "student_parent":
        return $this->student_parent_login($id);
    }
  }
  public function student_login($id = 0){
    $t = Student::where('status' , 'regular');
    if($id>0) $t = $t->where('id','>=',$id);
    $t = $t->first();
    Auth::loginUsingId($t->user_id);
    return $t;
  }
  public function teacher_login($id = 0){
    $t = Teacher::where('status' , 'regular');
    if($id>0) $t = $t->where('id','>=',$id);
    $t = $t->first();
    Auth::loginUsingId($t->user_id);
    return $t;
  }
  public function manager_login($id = 0){
    $t = Manager::where('status' , 'regular');
    if($id>0) $t = $t->where($id,'>=',$id);
    $t = $t->first();
    Auth::loginUsingId($t->user_id);
    return $t;
  }
  public function student_parent_login($id = 0){
    $t = StudentParent::where('status' , 'regular');
    if($id>0) $t = $t->where($id,'>=',$id);
    $t = $t->first();
    Auth::loginUsingId($t->user_id);
    return $t;
  }
  public function logout(){
    Auth::logout();
  }
}
