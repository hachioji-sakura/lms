<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\GeneralAttribute;

class CurriculumController extends MilestoneController
{

    public $domain = "curriculums";
    public function model(){
      return Curriculum::query();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $param = $this->get_param($request);
        $items = $this->model()->paginate($param['_line']);
        $param['items'] = $items;
        return view($this->domain.'.list')->with($param);
    }

    public function create(Request $request){
      $param = $this->get_param($request);
      //GeneralAttibuteを使わないならCurriculumTagsとのリレーションテーブルを持たせる？？
      $subjects = GeneralAttribute::where('attribute_key','charge_subject')->get();
      dd($subjects);
      $param['subjects'] = $subjects;
      $param['_edit'] = false;
      return view($this->domain.'.create')->with($param);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request)
    {
        //

        $form = $this->create_form($request);

        $item = $this->model();
        foreach($form as $key=>$val){
          $item = $item->where($key, $val);
        }
        $item = $item->first();
        if(isset($item)){
          return $this->error_response('すでに登録済みです');
        }

        $res = $this->transaction($request, function() use ($request, $form){
          $item = $this->model()->create($form);
          $item = $this->model()->curriculum_tags()->create();
          return $this->api_response(200, '', '', $item);
        }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );
        return $res;

    }

    public function create_form(Request $request){
      $form['curriculum'] = [
        'name' => $request->get('name')
      ];
      foreach($request->get('subjects') as $subject_name){

      }


      return $form;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _destroy($id)
    {
        //
    }
}
