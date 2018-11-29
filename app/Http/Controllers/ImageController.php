<?php

namespace App\Http\Controllers;
use App\Models\Image;
use App\Models\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
class ImageController extends UserController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $items = Image::all();
      return $items->toArray();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sample.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
          'image' => [
              // 必須
              'required',
              // アップロードされたファイルであること
              'file',
              // 画像ファイルであること
              'image',
              // MIMEタイプを指定
              'mimes:jpeg,png,gif,bmp,svg',
              // 最小縦横120px 最大縦横400px
              'dimensions:min_width=1,min_height=1,max_width=512,max_height=512',
          ]
      ]);
      $form = $request->all();
      if ($request->file('image')->isValid([])) {
        $res = $this->save_image($request->file('image'), date('Y-m-d'), $form['alias'], env("AWS_S3_ICON_FOLDER"));
        return view('sample.create');
        //return redirect('/images/create')->with('success', "");
      }
      else {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors(['file' => '画像がアップロードされていないか不正なデータです。']);
      }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function icon_change(Request $request, $student_id)
    {
      $user = $this->login_details();
      if($this->is_student($user->role)===true){
        //生徒は、自分（生徒）の内容しか見れない
        $student_id = $user->id;
      }
      $this->validate($request, [
          'image' => [
              // アップロードされたファイルであること
              'file',
              // 画像ファイルであること
              'image',
              // MIMEタイプを指定
              'mimes:jpeg,png,gif,bmp,svg',
              // 最小縦横120px 最大縦横400px
              'dimensions:min_width=1,min_height=1,max_width=512,max_height=512',
          ]
      ]);
      $image_id = null;
      $form = $request->all();
      $_message = "";
      if(empty($image_id) && isset($form["change_icon"])){
        $image_id = $form["change_icon"];
        $_message .= "アイコンを選択した(".$image_id.")";
      }

      if($request->hasFile('image')){
        if ($request->file('image')->isValid([])) {
          $res = $this->save_image($request->file('image'), "9999-12-31", "", env("AWS_S3_ICON_FOLDER"));
          if($this->is_success_responce($res)){
            $image_id = $res["data"]->id;
            $_message .= "画像アップロードしました(".$image_id.")";
          }
          else {
            return back()->with([
              'error_message' => $res["message"],
              'error_message_description' => $res["description"]
            ]);
          }
        }
        else {
          return redirect()
              ->back()
              ->withInput()
              ->withErrors(['file' => '画像がアップロードされていないか不正なデータです。']);
        }
      }
      if(!empty($image_id)){
        $student = Student::find($student_id)->user->details();
        $res = $this->update_user_image($student->user_id, $image_id);
        if($this->is_success_responce($res)){
          return back()->with([
            'success_message' => 'アイコンを変更しました。',
            'success_message_description' => $_message."/user()"
          ]);
        }
        else {
          return back()->with([
            'error_message' => $res["message"],
            'error_message_description' => $res["description"]
          ]);
        }
      }

      return back();
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $items = Image::find($id);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $items = Image::find($id)->delete();
      return redirect('/images');
    }

    private function save_image($request_file, $publiced_at='9999-12-31', $alias='', $save_folder="")
    {
      $user = $this->login_details();
      $image = new Image;

      try {
        DB::beginTransaction();
        $path = Storage::disk('s3')->putFile($save_folder, $request_file, 'public');
        $s3_url = Storage::disk('s3')->url(env('AWS_S3_BUCKET')."/".$path);
        if(empty($alias)){
          $alias = $request_file->getClientOriginalName();
        }
        $image_data =[
          "name" => $request_file->getClientOriginalName(),
          "type" => $request_file->guessClientExtension(),
          "size" => $request_file->getClientSize(),
          "s3_url" => $s3_url,
          "publiced_at" => $publiced_at,
          "create_user_id" => $user->user_id,
          "alias" => $alias
        ];
        $image->fill($image_data)->save();
          DB::commit();
          /*
          $message = "name:".basename($request_file)."\n";
          $message .= "alias:".$request_file->getClientOriginalName()."\n";
          $message .= "getClientSize:".$request_file->getClientSize()."\n";
          $message .= "guessClientExtension:".$request_file->guessClientExtension()."\n";
          $message .= "size:".filesize($request_file)."\n";
          $message .= "type:".filetype($request_file)."\n";
          $message .= "s3_path:".$s3_url."\n";
          $message .= "path:".$path."\n";
          */
          return $this->api_responce(200, "", "", $image);
      }
      catch (\Illuminate\Database\QueryException $e) {
          DB::rollBack();
          return $this->error_responce("Query Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
      catch(\Exception $e){
          echo $e->getMessage();
          DB::rollBack();
          return $this->error_responce("DB Exception", "[".__FILE__."][".__FUNCTION__."[".__LINE__."]"."[".$e->getMessage()."]");
      }
    }

}
