<?php

namespace App\Http\Controllers;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends UserController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      $items = Course::all();
      return $items->toArray();
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      return view('courses.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
      $Course = new Course;
      $form = $request->all();
      unset($form['_token']);
      $Course->fill($form)->save();
      return redirect('/courses');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      $items = Course::find($id);
      return $items->toArray();
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $target = Course::find($id);
    return view('courses.create', ['form' => $target]);

  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      $items = Course::find($id);
      $form = $request->all();
      unset($form['_token']);
      $items->fill($form)->save();
      return redirect('/courses');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      $items = Course::find($id)->delete();
      return redirect('/courses');
  }
}
