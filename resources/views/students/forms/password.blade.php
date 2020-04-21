<div class="col-12">
  <div class="form-group">
    <label for="password">
      パスワード
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="password" id="password" name="password" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true">
  </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="password-confirm">
      パスワード（確認）
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true" equal="password" equal_error="パスワードが一致しません">
  </div>
</div>

<div class="col-12">

  <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
  @if($item->domain == 'students')
    ※生徒がシステムにログインする際、ログインIDとパスワードが必要となります。
  @else
    ※システムにログインする際、メールアドレスとパスワードが必要となります。
  @endif
  </h6>
  </div>
