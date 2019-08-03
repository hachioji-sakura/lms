@extends('layouts.loginbox')
@section('title', __('labels.password_setting'))
@section('title_header', __('labels.password_setting'))
@section('content')
<form method="POST" action="/password/setting" id="reset_form">
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
  <input type="hidden" name="locale" value="ja" >
  <input type="hidden" name="access_key" value="{{$access_key}}" />
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="password">
          {{__('labels.password')}}
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <input type="password" id="password" name="password" class="form-control" placeholder="{{__('messages.error_validate_password')}}" minlength=8 maxlength=16 required="true">
      </div>
    </div>
    <div class="col-12">
      <div class="form-group">
        <label for="password-confirm">
          {{__('labels.password_confirm')}}
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="{{__('messages.error_validate_password')}}" minlength=8 maxlength=16 required="true" equal="password" equal_error="{{__('messages.error_validate_password_not_match')}}">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
        <button type="button" class="btn btn-submit btn-primary btn-block">
          {{__('labels.update_button')}}
        </button>
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
