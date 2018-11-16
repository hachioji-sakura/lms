@extends('layouts.loginbox')
@section('title', '講師登録')
@section('content')
<form id="edit" method="POST" action="/teachers">
    @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label for="field1">
                氏名
                <span class="right badge badge-danger ml-1">必須</span>
              </label>
              <input type="text" id="name" name="name" class="form-control" placeholder="山田 太郎" required="true" inputtype="zenkaku">
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label for="field1">
                氏名（カナ）
                <span class="right badge badge-danger ml-1">必須</span>
              </label>
              <input type="text" id="kana" name="kana" class="form-control" placeholder="ヤマダ　タロウ" required="true" inputtype="zenkakukana">
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label for="email">
                メールアドレス
                <span class="right badge badge-danger ml-1">必須</span>
              </label>
              <input type="text" id="email" name="email" class="form-control" placeholder="hachioji@sakura.com"  required="true" inputtype="email" query_check="auth/email" query_check_error="このメールアドレスは登録済みです">
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label for="password">
                パスワード
                <span class="right badge badge-danger ml-1">必須</span>
              </label>
              <input type="password" id="password" name="password" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true">
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label for="password-confirm">
                パスワード（確認）
                <span class="right badge badge-danger ml-1">必須</span>
              </label>
              <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true">
            </div>
          </div>
          <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">
                  登録する
              </button>
              @if(!empty($error_message))
                <span class="invalid-feedback d-block ml-2 " role="alert">
                    <strong>{{$error_message}}</strong>
                </span>
              @endif
          </div>
        </div>
      </div>
    </div>
</form>
<script>
$(function(){
  @if((env('APP_DEBUG')))
  var data = {
    "name" : "鈴木　一郎",
    "kana" : "すずき　いちろう",
    "email" : "suzuki"+((Math.random()*1000)|0)+"@gmail.com",
    "password-confirm" : "hogehoge",
    "password" : "hogehoge"
  };
  base.pageSettinged("edit", data);
  @else
  base.pageSettinged("edit", null);
  @endif
	$(".btn[type=submit]").on("click", function(){
		console.log("btn.submit");
		if(!front.validateFormValue("edit")) return false;
    $("#edit").submit();
	});
})
</script>

@endsection
