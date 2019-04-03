@extends('layouts.loginbox')
@section('title', 'パスワード再設定')
@section('title_header', 'パスワード再設定')
@section('content')
<form method="POST" action="/password/setting" id="reset_form">
    @csrf
    <input type="hidden" name="access_key" value="{{$access_key}}" />
    <div class="row">
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
          <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true" equal="password" equal_error="パスワードが一致しません">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block">
              設定する
          </button>
      </div>
    </div>
  </div>
</form>
<script>
$(function(){
  base.pageSettinged("reset_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    console.log("reset_form");
    e.preventDefault();
    if(front.validateFormValue('reset_form')){
      $("form").submit();
    }
  });
});
</script>
@endsection
