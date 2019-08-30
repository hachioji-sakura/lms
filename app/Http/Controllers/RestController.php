<?php

namespace App\Http\Controllers;

use App\Restdata;
use App\Models\StudentParent;

use Illuminate\Http\Request;
use DB;
class RestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rest.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request)
     {
         $restdata = new Restdata;
         $form = $request->all();
         $form = $request->all();
         unset($form['_token']);
         $restdata->fill($form)->save();
         return redirect('/rest');
     }
     public function test(Request $request)
     {
         $restdata = new Restdata;
         $form = $request->all();
         unset($form['_token']);
         Restdata::create([
           'name' => $form['name'],
           'message' =>  $form['message'],
         ]);
         /*
         Restdata::create($form);
         */
         return "saved";
     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $items = Restdata::where('id',$id);
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
      $target = Restdata::where('id',$id);
      return view('rest.create', ['form' => $target]);

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
        $items = Restdata::where('id',$id);
        $form = $request->all();
        unset($form['_token']);
        $items->fill($form)->save();
        return redirect('/rest');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $items = Restdata::where('id',$id)->delete();
        return redirect('/rest');
    }
}
