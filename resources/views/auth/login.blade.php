@extends('layouts.loginbox')
@section('title', 'ログイン')
@section('content')
<form id="login_form" method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group row mb-3">
      <div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text"><i class="fa fa-envelope"></i></span>
				</div>
        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="メールアドレス" minlength="3" maxlength="32" inputtype="email">
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
        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="パスワード" accesskey="enter" minlength="8" maxlength="32" inputtype="hankaku">
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
              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">
                  ログイン状態を維持する
              </label>
          </div>
      </div>
    </div>

    <div class="form-group row mb-3">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">
                ログイン
            </button>
        </div>
    </div>
</form>
{{-- まだ対応しない
<h6 class="my-2">
	<a href="{{ route('password.request') }}" class="small">パスワード忘れた方</a>
</h6>
<hr class="my-3">
<p class="my-2">
	<button type="button" class="btn btn-outline-success btn-block">新規登録する</button>
</p>
--}}
@endsection
