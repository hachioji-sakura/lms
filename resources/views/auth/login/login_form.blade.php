<input type="hidden" name="locale" value="ja" >
<div class="form-group row mb-3">
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text"><i class="fa fa-user-alt"></i></span>
    </div>
    <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" required="true" autofocus placeholder="{{__('labels.input_login_id')}}" minlength="3" maxlength="32" inputtype="hankaku">
  </div>
</div>

<div class="form-group row mb-3">
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text"><i class="fa fa-key"></i></span>
    </div>
    <input id="password" type="password" class="form-control" name="password" required="true" placeholder="{{__('labels.input_password')}}" accesskey="enter" minlength="8" maxlength="32" inputtype="hankaku">
  </div>
</div>
<div class="form-group row mb-3">
  <div class="input-group">
      <div class="form-check">
          <input class="form-check-input icheck flat-green" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <label class="form-check-label" for="remember">
            {{ __('labels.keep_login') }}
          </label>
      </div>
  </div>
</div>
<script>
$(function(){
  base.pageSettinged("login_form", null);
  //submit
  var param = util.convQueryStringToJson();
  if(!util.isEmpty(param["locale"])){
    $("input[name='locale']").val(param["locale"]);
  }

  $("button.btn-submit").on('click', function(e){
    console.log("login");
    e.preventDefault();
    if(front.validateFormValue('login_form')){
      $("form").submit();
    }
  });
});
</script>
