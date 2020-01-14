<?php
namespace App\Http\Controllers;
use App\Models\Image;
use App\Models\Student;
use App\User;
use Illuminate\Http\Request;
use DB;
class ImageController extends UserController
{
    public $domain = 'images';
    public $table = 'images';

    /**
     * このdomainで管理するmodel
     *
     * @return model
     */
    public function model(){
      return Image::query();
    }

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
    public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      if(!isset($user)) {
        abort(403);
      }
      $ret = [
        'domain' => $this->domain,
        'domain_name' => __('labels.'.$this->domain),
        'user' => $user,
        'use_icons' => $this->get_image($request),
        'user_id' => $request->user_id,
        'search_word'=>$request->search_word,
        'search_status'=>$request->status,
        'attributes' => $this->attributes(),
      ];
      return $ret;
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
    public function icon_change_page(Request $request){
      $param = $this->get_param($request);
      return view($this->domain.'.change',
        ['error_message' => ''])
        ->with($param);
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
        $res = $this->save_image($request, $request->file('image'), date('Y-m-d'), $form['alias'], config('aws_s3.icon_folder'));
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
    //CKEditorからuploadする場合
    public function upload_images(Request $request){
      $s3 = $this->s3_upload($request->file('upload'), config('aws_s3.upload_folder'));

      $json = [
        "uploaded" => false,
        "error" => ["message" => "Error message here"],
      ];
      $json = [
        "uploaded" => true,
        "url" => $s3['url'],
      ];

      return $json;
    }
    public function icon_change(Request $request){
      $param = $this->get_param($request);
      $res = $this->_icon_change($request);
      return $this->save_redirect($res, $param, '設定を更新しました。');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _icon_change(Request $request)
    {
      $user = $this->login_details($request);
      $res = $this->bad_request();
      $image_id = null;
      $form = $request->all();
      if(empty($image_id) && isset($form["change_icon"])){
        $image_id = $form["change_icon"];
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
      if($request->hasFile('image')){
        if ($request->file('image')->isValid([])) {
          $res = $this->save_image($request, $request->file('image'), "9999-12-31", "", config('aws_s3.icon_folder'));
          if($this->is_success_response($res)){
            $image_id = $res["data"]->id;
          }
        }
        else {
          return $this->error_response('画像がアップロードされていないか不正なデータです');
        }
      }
      if(!empty($image_id)){
        $res = $this->update_user_image($request->user_id, $image_id);
      }
      return $res;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $items = Image::where('id',$id);
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
      $items = Image::where('id',$id)->delete();
      return redirect('/images');
    }
    private function save_image($request, $request_file, $publiced_at='9999-12-31', $alias='', $save_folder="")
    {
      return $this->transaction($request, function() use ($request, $request_file, $publiced_at, $alias, $save_folder){
        $user = $this->login_details($request);
        $image = new Image;
        $s3 = $this->s3_upload($request_file, $save_folder);
        if(empty($alias)){
          $alias = $request_file->getClientOriginalName();
        }
        $image_data =[
          "name" => $request_file->getClientOriginalName(),
          "type" => $request_file->guessClientExtension(),
          "size" => $request_file->getClientSize(),
          "s3_url" => $s3['url'],
          "publiced_at" => $publiced_at,
          "create_user_id" => $user->user_id,
          "alias" => $alias
        ];
        $image->fill($image_data)->save();
        $message = "name:".basename($request_file)."\n";
        $message .= "alias:".$request_file->getClientOriginalName()."\n";
        $message .= "getClientSize:".$request_file->getClientSize()."\n";
        $message .= "guessClientExtension:".$request_file->guessClientExtension()."\n";
        $message .= "size:".filesize($request_file)."\n";
        $message .= "type:".filetype($request_file)."\n";
        $message .= "s3_path:".$s3['url']."\n";
        $message .= "path:".$s3['path']."\n";
        \Log::info("ファイルアップロード:\n".$message);
        $this->send_slack($message, 'info');
        return $this->api_response(200, '', '', $image);
      }, '画像アップロード', __FILE__, __FUNCTION__, __LINE__ );
    }
}
