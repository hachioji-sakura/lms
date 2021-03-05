<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends MilestoneController
{
  public $domain = 'reviews';
  public function model(){
    return Review::query();
  }

  public function edit(Request $request, $id){
    $param = get_param($request,$id);
    $param['_edit'] = true;
    return view('tasks.review')->with($param);
  }

  public function update_form($request){
    $form = [
      'body' =>$request->get('body'),
    ];
    return $form;
  }

  public function destroy(Request $request, $id){

  }
}
