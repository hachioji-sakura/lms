<div id="{{$domain}}_create">
  @if(isset($_edit))
  <form method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form method="POST" action="/{{$domain}}">
  @endif
  @csrf
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name">
            氏名
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name" name="name" class="form-control" placeholder="例：山田　太郎" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="kana">
            フリガナ
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="kana" name="kana" class="form-control" placeholder="例：ヤマダ　タロウ" required="true" inputtype="zenkakukana">
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="email">
            メールアドレス
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email" query_check="users/email" query_check_error="このメールアドレスは登録済みです">
        </div>
      </div>
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
          <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="" minlength=8 maxlength=16 required="true" equal="password" equal_error="パスワードが一致しません">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="submit" class="btn btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit))
              更新する
            @else
              登録する
            @endif
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
