@extends('layouts.loginbox')
@section('title', __('labels.forget_password'))
@section('title_header',  __('labels.forget_password'))
@section('content')
<form method="POST"  action="forget" id="forget_form">
    @csrf
    <input type="hidden" name="locale" value="ja" >
    <div class="row mb-3">
      <div class="input-group col-12">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-envelope"></i></span>
        </div>
        <input type="text" name="email" id="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{__('labels.register_email')}}" required autofocus value="{{ $email ?? old('email') }}" inputtype="email">
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
          {{__('messages.info_forget_password')}}
        </p>
      </div>
    </div>
    <div class="form-group row mb-3">
        <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block">
            {{__('labels.send_button')}}
          </button>
        </div>
    </div>
</form>
<div class="my-2 row hr-1 bd-gray">
  <h6 class="col-12">
  	<a href="/login" class="small mr-2"><i class="fa fa-arrow-alt-circle-right mr-1"></i>
      {{__('labels.to_login')}}
    </a>
  </h6>
</div>
<script>
$(function(){
  base.pageSettinged("forget_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    console.log("login");
    e.preventDefault();
    if(front.validateFormValue('forget_form')){
      $("form").submit();
    }
  });
});
</script>

@endsection
