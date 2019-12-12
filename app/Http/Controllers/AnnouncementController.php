<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends CommentController
{
  public $domain = 'announcements';
  public $table = 'announcements';
  public function model(){
    return Announcement::query();
  }

}
