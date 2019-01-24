@extends('layouts.loginbox')
@section('title', '仮登録ページ')
@section('content')
<div id="students_entry">
@if(!empty($result))
  <h4 class="bg-success p-3 text-sm">
    @if($result==='success')
      仮登録完了メールを送信しました。<br>
      送信したメールにて、24時間以内にユーザー登録を進めてください。<br>
    @elseif($result==='already')
      仮登録中の情報が残っています。<br>
      再送信したメールにて、24時間以内にユーザー登録を進めてください。
    @elseif($result==='exist')
      このメールはユーザー登録が完了しています。
    @endif
  </h4>
@else
<form method="POST"  action="/entry">
    @csrf
    <div class="row">
      <div class="col-12">
        <h3 class="text-lg p-2 mb-4 bg-danger text-center" >
          <i class="fa fa-exclamation-triangle mr-1"></i>
          ご契約いただく方の情報をご入力ください
        </h3>
      </div>
      <div class="col-6 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_last">
            氏
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-6 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_first">
            名
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_first" name="name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="email">
            メールアドレス
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email">
        </div>
      </div>
    </div>
    <div class="col-12">
      <h6 class="text-sm p-2 pl-2 mb-4" >
        入力いただいたメールアドレスに、<br>
        本登録用のURLを送信いたします。
      </h6>
    </div>
    <div class="row">
      <div class="col-12 mb-1">
          <button type="submit" class="btn btn-primary btn-block" accesskey="students_create">
            <i class="fa fa-envelope mr-1"></i>仮登録を進める
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-1">
        <a href="/login" role="button" class="btn btn-secondary btn-block float-left mr-1">
          ログイン画面へ戻る
        </a>
      </div>
    </div>

  </form>
</div>
<script>

$(function(){
  base.pageSettinged("students_entry", null);
  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('students_entry')){
      $("form").submit();
    }
  });


});
</script>
@endif
@endsection
