@if(!empty($locale))
<input type="hidden" name="locale" value="{{$locale}}" >
@endif
<div class="form-group row mb-3">
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text"><i class="fa fa-user-alt"></i></span>
    </div>
    <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required="true" autofocus placeholder="{{__('labels.input_login_id')}}" minlength="3" maxlength="512" inputtype="hankaku">
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
    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required="true" placeholder="{{__('labels.input_password')}}" accesskey="enter" minlength="8" maxlength="32" inputtype="hankaku">
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
            {{ __('labels.keep_login') }}
          </label>
      </div>
  </div>
</div>
<script>
$(function(){
  var form_data = util.getLocalData('login_form');
  base.pageSettinged("login_form", form_data);
  //submit
  $("button.btn-submit").on('click', function(e){
    save_login_info();
    e.preventDefault();
    if(front.validateFormValue('login_form')){
      $(this).prop("disabled",true);
      $("form").submit();
    }
  });

  function save_login_info(){
    util.removeLocalData('login_form');
    var remember = $('input[name=remember]').prop('checked');
    if(!remember) return ;
    var form_data = front.getFormValue('login_form');
    delete form_data['_token'];
    util.setLocalData('login_form', form_data);
  }
});
</script>
