@extends('layouts.loginbox')
@section('title', '新規ご入会お申込み')
@section('content')
<div id="students_entry">
@if(!empty($result))
  <h4 class="bg-success p-3 text-sm">
    @if($result==='success')
      仮登録完了メールを送信しました。<br>
      送信したメールにて、本登録ページより本登録を進めてください。
    @elseif($result==='already')
      仮登録中の情報が残っています。<br>
      再送信したメールにて、本登録ページより本登録を進めてください。
    @elseif($result==='exist')
      このメールはユーザー登録が完了しています。
    @endif
  </h4>
@else
<form method="POST"  action="/students/entry">
    @csrf
    <div class="col-12">
      <h6 class="text-sm p-1 pl-2 mb-4" >
        入力いただいたメールアドレスに、本登録するURLを送信いたします。
      </h6>
    </div>
    <div class="row">
      <div class="col-12">
        <h5 class="bg-info p-2 pl-3 mb-4">
          生徒様情報
        </h5>
      </div>
    </div>
    <div class="row">
      <div class="col-6 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_last">
            氏
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-6 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_first">
            名
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_first" name="name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-12 col-lg-12 col-md-12">
        <div class="form-group">
          <label for="password-confirm">
            性別
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <div class="input-group">
            <div class="form-check">
                <input class="form-check-input flat-red" type="radio" name="gender" id="gender_2" {{ old('gender') ? 'checked' : '' }} value="2" required="true">
                <label class="form-check-label" for="gender_2">
                    女性
                </label>
            </div>
            <div class="form-check ml-2">
                <input class="form-check-input flat-red" type="radio" name="gender" id="gender_1" {{ old('gender') ? 'checked' : '' }} value="1" required="true">
                <label class="form-check-label" for="gender_1">
                    男性
                </label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <h6 class="text-sm p-1 pl-2 mb-4 text-danger" >
          ※ご兄弟での申し込みについては、本登録完了後にてお願いいたします。
        </h6>
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
    </div>
    <div class="row">
      <div class="col-12 mb-1">
          <button type="submit" class="btn btn-primary btn-block" accesskey="students_create">
              お申込み
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
    </div>
  </form>
</div>
<script>

$(function(){
  base.pageSettinged("students_entry", null);
  //Google検索・Yahoo検索と答えた場合、検索ワードフォームを表示
  $('input[type="checkbox"]').on('ifChanged', function(e){
    var is_google = $('input[type="checkbox"][value="google"]').prop("checked");
    var is_yahoo = $('input[type="checkbox"][value="yahoo"]').prop("checked");
    if(is_google || is_yahoo){
      $("#search_word_question").collapse("show");
    }
    else {
      $("#search_word_question").collapse("hide");
    }
  });
});
</script>
@endif
@endsection
