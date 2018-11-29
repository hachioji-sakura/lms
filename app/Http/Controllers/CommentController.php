<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Student;
use DB;
class CommentController extends MilestoneController
{
    public $domain = 'comments';
    public $table = 'comments';
    public $domain_name = 'コメント';
    public function model(){
      return Comment::query();
    }
}
