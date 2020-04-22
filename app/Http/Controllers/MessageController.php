<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Message;
use App\Models\Teacher;

class MessageController extends CommentController
{
    //
    /*
    *　生徒をターゲットにしたら親に飛ぶ
    *　先生、親は自分の物にしかアクセスできない
    *　リストから詳細へ
    */
    public $domain = 'messages';
    public function model(){
      return Message::query();
    }

    public function list(Request $request, $id = null){
      $user = $this->login_details($request);
      $role = $user->role;
      if( $role == 'manager'){
        $messages = $this->model()->get();
      }else{
        abort(403);
      }
      $fields = [
        'id' => [
          'label' => '詳細',
        ],
        'create_user' =>[
          'label' => 'from'
        ],
        'target_user' => [
          'label' => 'to'
        ],
        'title' => [
          'label' => 'タイトル',
        ],
      ];
      $params = [
        'items' => $messages,
        'fields' => $fields,
        'domain' => $this->domain,
        'domain_name' => 'メッセージ',
        'user' => $user
      ];
      return view( $this->domain.'.list',$params);
    }


    public function details(Request $request, $id){
      $params = $this->get_param($request,$id);
      $user = $this->login_details($request);
      $item = $this->model()->where('id',$id)->first();
      $parent_message_id = $item->parent_message_id;
      if( $parent_message_id == 0){
        $_id = $id;
      }else{
        $_id = $parent_message_id;
      }
      $items = $this->model()->where('id',$_id)
                            ->orWhere('parent_message_id',$_id)
                            ->orderBy('created_at','asc')
                            ->get();
      $root_message = $this->model()->findRootMessage($_id)->first();
      $message_params = [
        'items' => $items,
        'root_message' => $root_message,
        'domain' => $this->domain,
        'domain_name' => 'メッセージ',
        'id' => $id,
      ];
      return view($this->domain.'.details',$message_params)->with($params);
    }

    public function create(Request $request){
      $param = $this->get_param($request);
      $user = $this->login_details($request);
      $message_type = config('attribute.message_type')['type'];
      if($this->is_teacher($user->role)){
        $charge_users = Student::findChargeStudent($user->id)->get();
      }elseif($this->is_parent($user->role)){
        //TODO　担当はcharge_studentsで管理するように変更していく
        $students = Student::findChild($user->id)->get();
        foreach($students as $student){
          $id = $student->id;
          $query = Teacher::findChargeTeachers($id);
        }
        $charge_users = $query->get();
      }elseif($this->is_manager($user->role)){
        $charge_users = Student::findStatuses('regular',false)->get();
      }else {
        abort(403);
      }
      $select_params = [
        'charge_users' => $charge_users,
        'message_type' => $message_type,
        'user' => $user,
        '_reply' => false,
      ];

      return view($this->domain.'.create',$select_params)->with($param);
    }

    public function create_form(Request $request, $target_user_id = null){
      $user = $this->login_details($request);
      $form = [];
      $form['create_user_id'] = $user->user_id;
      $form['target_user_id'] = $target_user_id;
      $form['body'] = $request->get('body', ENT_QUOTES, 'UTF-8');
      $form['title'] = $request->get('title');
      $form['type'] = $request->get('type');
      $form['parent_message_id'] = $request->get('parent_message_id');
      return $form;
    }

    public function _store(Request $request)
    {
      $target_users = $request->get('target_user_id');
      foreach($target_users as $target_user){
        $form = $this->create_form($request,$target_user);
        $res = $this->save_validate($request);
        if(!$this->is_success_response($res)){
          return $res;
        }
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
          if($request->hasFile('upload_file')){
            if ($request->file('upload_file')->isValid([])) {
              $item->file_upload($request->file('upload_file'));
            }
          }
          return $this->api_response(200, '', '', $item);
        }, '登録しました。', __FILE__, __FUNCTION__, __LINE__ );

        if($this->is_success_response($res)){
          //メールを送信する
          $item = $res['data']->details();
          $template = 'message';
          $type = 'text';
          $param = ['item' => $item ];
          $item->target_user->send_mail($item->title, $param, $type ,$template);
        }
      }
      return $res;
     }

    public function reply(Request $request, $id){
      $user = $this->login_details($request);
      $item = $this->model()->where('id',$id)->first();
      $_reply = true;
      $params = [
        'item' => $item,
        '_reply' => $_reply,
        'domain' => $this->domain,
        'user' => $user,
      ];
      return view($this->domain.'.create')->with($params);
    }

    public function save_validate(Request $request)
    {
      $form = $request->all();
      //保存時にパラメータをチェック
      if(empty($form['title']) || empty($form['body']) ){
        return $this->bad_request('リクエストエラー','/タイトル='.$form['title'].'/内容='.$form['body']);
      }
      return $this->api_response(200, '', '');
    }


    /**
     * 共通パラメータ取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id　（this.domain.model.id)
     * @return json
     */
     public function get_param(Request $request, $id=null){
      $user = $this->login_details($request);
      if(!isset($user)) {
        abort(403);
      }
      $role = $user->role;
      $ret = $this->get_common_param($request);
      if(is_numeric($id) && $id > 0){
        //親は自分の投稿と子供あての投稿のみ見られる
        $item = $this->model()->where('id','=',$id)->first();
        if($this->is_parent($role)){
          $students = $user->get_enable_students();
          $_fail_check = 0;
          foreach($students as $student){
            $student_id = $student->user_id;
            if($item['create_user_id'] == $student_id || $item['target_user_id'] == $student_id){
              $_fail_check++;
            }
          }
          if($_fail_check == 0 && ($item['create_user_id'] !== $user->user_id && $item['target_user_id'] !== $user->user_id)){
            abort(403);
          }
        }elseif($this->is_teacher($role) || $this->is_manager($role)){
          //先生、事務は自分が関係する投稿のみ
          //事務は/messagesですべてのレコードを見る
          if($item['create_user_id'] !== $user->user_id && $item['target_user_id'] !== $user->user_id){
            abort(403);
          }
        }

        $item = $item->details();
        $ret['item'] = $item->details();
      }
      return $ret;
    }
}
