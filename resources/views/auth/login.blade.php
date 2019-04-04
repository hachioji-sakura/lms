@extends('layouts.loginbox')
@section('title', 'ログイン')
@section('title_header', 'ログイン')
@section('content')
<form id="login_form" method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group row mb-3">
      <div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text"><i class="fa fa-user-alt"></i></span>
				</div>
        <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required="true" autofocus placeholder="ログインID またはメールアドレス" minlength="3" maxlength="32" inputtype="hankaku">
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
			</div>
    </div>

    <div class="form-group row mb-3">
      <div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text"><i class="fa fa-key"></i></span>
				</div>
        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required="true" placeholder="パスワード" accesskey="enter" minlength="8" maxlength="32" inputtype="hankaku">
        @if ($errors->has('password'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
			</div>
    </div>

    <div class="form-group row mb-3">
      <div class="input-group">
          <div class="form-check">
              <input class="form-check-input icheck flat-green" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">
                  ログイン状態を維持する
              </label>
          </div>
      </div>
    </div>

    <div class="form-group row mb-3">
        <div class="col-12">
            <button type="button" class="btn btn-submit btn-primary btn-block">
                ログイン
            </button>
        </div>
    </div>
</form>
<div class="my-2 row hr-1 bd-gray">
  <h6 class="col-12">
  	<a href="/forget" class="small mr-2"><i class="fa fa-arrow-alt-circle-right mr-1"></i>パスワード忘れた方</a>
  </h6>
</div>
<div class="my-2 row">
  <a href="/entry" role="button" class="btn btn-outline-success btn-block btn-sm float-left mr-1">
    <i class="fa fa-sign-in-alt mr-1"></i>入会・登録はこちら
  </a>
</div>
<div class="my-2 row">
  <h6 class="col-12">
  <a href="/managers/login" class="float-right small mr-2"><i class="fa fa-user-lock mr-1"></i>管理ログインページへ</a>
  </h6>
</div>

<script>
$(function(){
  base.pageSettinged("login_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    console.log("login");
    e.preventDefault();
    if(front.validateFormValue('login_form')){
      $("form").submit();
    }
  });
});
</script>
@endsection
