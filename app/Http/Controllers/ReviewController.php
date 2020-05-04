<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends MilestoneController
{
  public $domain = 'reviews';
  public function model(){
    return Review::query();
  }

  public function edit(){

  }

  public function update(){

  }

  public function destroy(){

  }
}
