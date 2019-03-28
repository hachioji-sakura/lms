<form method="POST" action="/password">
    @csrf
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="password">
            パスワード
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="password" id="password" name="password" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true">
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="password-confirm">
            パスワード（確認）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true" equal="password" equal_error="パスワードが一致しません">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block">
              設定する
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>

    </div>
  </div>
</form>
