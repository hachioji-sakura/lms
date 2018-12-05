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
          <label for="name_last">
            氏
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：山田" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_first">
            名
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_first" name="name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="kana_last">
            氏（カナ）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="kana_last" name="kana_last" class="form-control" placeholder="例：ヤマダ" required="true" inputtype="zenkakukana">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="kana_first">
            名（カナ）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="kana_first" name="kana_first" class="form-control" placeholder="例：タロウ" required="true" inputtype="zenkakukana">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="birth_day">
            生年月日
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input id="birth_day" type="text" class="form-control{{ $errors->has('birth_day') ? ' is-invalid' : '' }}" name="birth_day" value="{{ old('birth_day') }}" inputtype="date" plaeholder="例：2000/01/01" required="true">

          @if ($errors->has('birth_day'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('birth_day') }}</strong>
              </span>
          @endif
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="password-confirm">
            性別
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <div class="input-group">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gender" id="gender_2" {{ old('gender') ? 'checked' : '' }} value="2" required="true">
                <label class="form-check-label" for="gender_2">
                    女性
                </label>
            </div>
            <div class="form-check ml-2">
                <input class="form-check-input" type="radio" name="gender" id="gender_1" {{ old('gender') ? 'checked' : '' }} value="1" required="true">
                <label class="form-check-label" for="gender_1">
                    男性
                </label>
            </div>
          </div>
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
          <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="" minlength=8 maxlength=16 required="true" equal="password" equal‗error="パスワードが一致しません">
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
