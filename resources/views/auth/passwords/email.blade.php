@extends('layouts.loginbox')
@section('title', 'パスワードを忘れた方')
@section('content')
<form id="login_form" method="POST"  action="{{ route('password.email') }}">
    @csrf
    <div class="row mb-3">
      <div class="input-group col-12">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-envelope"></i></span>
        </div>
        <input type="text" inputtype="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="登録メールアドレス" required autofocus value="{{ $email ?? old('email') }}">
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-12">
        <p class="small text-muted p-2">
          ご登録されたメールアドレスにパスワード再設定のご案内が送信されます。
        </p>
      </div>
    </div>
    <div class="form-group row mb-3">
        <div class="col-12">
          <button type="submit" class="btn btn-primary btn-block">送信する</button>
        </div>
    </div>
</form>
<h6 class="my-2">
	<a href="{{ route('login') }}" class="small">ログインへ戻る</a>
</h6>
<hr class="my-3">
<p class="my-2">
	<a href="{{ route('register') }}" class="btn btn-outline-success btn-block">新規登録する</a>
</p>
@endsection
