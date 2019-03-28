@extends('layouts.loginbox')
@section('title', 'パスワードを忘れた方')
@section('content')
<form method="POST"  action="forget">
    @csrf
    <div class="row mb-3">
      <div class="input-group col-12">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-envelope"></i></span>
        </div>
        <input type="text" name="email" id="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="登録メールアドレス" required autofocus value="{{ $email ?? old('email') }}">
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
      </div>
    </div>
    <div class="row my-3">
      <div class="col-12">
        <p class="small text-muted">
          ご登録されたメールアドレスにパスワード再設定のご案内が送信されます。
        </p>
      </div>
    </div>
    <div class="form-group row mb-3">
        <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block">送信する</button>
        </div>
    </div>
</form>
@endsection
