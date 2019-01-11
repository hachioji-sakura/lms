@extends('layouts.loginbox')
@section('title', 'パスワード再設定')
@section('content')
<form method="POST" action="/password/setting">
    @csrf
    <input type="hidden" name="access_key" value="{{$access_key}}" />
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
          <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true" equal="password" equal‗error="パスワードが一致しません">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
          <button type="submit" class="btn btn-primary btn-block">
              設定する
          </button>
      </div>
    </div>
  </div>
</form>
@endsection
