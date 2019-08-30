<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use Illuminate\Http\Request;
use DB;
class ManagerController extends TeacherController
{
    public $domain = "managers";
    public $table = "managers";
    
    public $default_image_id = 4;
    public function model(){
      return Manager::query();
    }
    public function login(){
      return view('managers.login');
    }
}
