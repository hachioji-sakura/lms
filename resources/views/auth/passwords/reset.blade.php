@extends('layouts.loginbox')
@section('title', __('labels.forget_password'))
@section('title_header', __('labels.forget_password'))
@section('content')
<form id="login_form" method="POST"  action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="locale" value="ja" >
    <div class="row mb-3">
      <div class="input-group col-12">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-envelope"></i></span>
        </div>
        <input type="text" inputtype="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="登録メールアドレス" required autofocus value="{{ $email ?? old('email') }}">
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-12">
        <p class="small text-muted">
          {{__('messages.info_forget_password')}}
        </p>
      </div>
    </div>
    <div class="form-group row mb-3">
        <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block">__('labels.send_button')</button>
        </div>
    </div>
</form>
<h6 class="my-2">
	<a href="{{ route('password.request') }}" class="small">__('labels.forget_password')</a>
</h6>
<hr class="my-3">
<p class="my-2">
	<button type="button" class="btn btn-outline-success btn-block">__('labels.add_button')</button>
</p>
@endsection
