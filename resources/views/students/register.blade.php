@extends('layouts.simplepage')
@section('title', 'ユーザー登録')

@section('student_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-graduate mr-1"></i>
      生徒様情報
    </h5>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="name_last">
        氏
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku" @isset($student) value="{{$student->name_last}}" @endisset>
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="name_first">
        名
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="name_first" name="name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku" @isset($student) value="{{$student->name_first}}" @endisset>
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="kana_last">
        氏（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="kana_last" name="kana_last" class="form-control" placeholder="例：ハチオウジ" required="true" inputtype="zenkakukana">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
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
      <input id="birth_day" type="text" class="form-control" name="birth_day"  inputtype="date" placeholder="例：2000/01/01" required="true">
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
            <input class="form-check-input flat-red" type="radio" name="gender" id="gender_2" value="2" required="true" @if(isset($student) && $student->gender===2) checked @endif>
            <label class="form-check-label" for="gender_2">
                女性
            </label>
        </div>
        <div class="form-check ml-2">
            <input class="form-check-input flat-red" type="radio" name="gender" id="gender_1" value="1" required="true" @if(isset($student) && $student->gender===1) checked @endif>
            <label class="form-check-label" for="gender_1">
                男性
            </label>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="school_name" class="w-100">
        学校名
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="school_name" name="school_name" class="form-control" placeholder="例：八王子市立サクラ中学校" required="true">
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="grade" class="w-100">
        学年
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="grade" class="form-control" placeholder="学年" required="true">
        <option value="">(選択してください)</option>
        @foreach($attributes['grade'] as $index => $name)
          <option value="{{$index}}">{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
</div>
@endsection
@section('account_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-key mr-1"></i>
      ログイン情報
    </h5>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="email">
        メールアドレス
      </label>
      <h5>@isset($user) {{$user->email}} @endisset</h5>
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
      <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true" equal="password" equal_error="パスワードが一致しません">
    </div>
  </div>
  <div class="col-12">
    <h6 class="text-sm p-1 pl-2 mt-2 text-danger" >
      ※システムにログインする際、メールアドレスとパスワードが必要となります。
    </h6>
  </div>

</div>
@endsection
@section('parent_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-friends mr-1"></i>
      保護者様情報
    </h5>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_name_last">
        氏
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_name_last" name="parent_name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_name_first">
        名
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_name_first" name="parent_name_first" class="form-control" placeholder="例：桜" required="true" inputtype="zenkaku">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_kana_last">
        氏（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_kana_last" name="parent_kana_last" class="form-control" placeholder="例：ハチオウジ" required="true" inputtype="zenkakukana">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_kana_first">
        名（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_kana_first" name="parent_kana_first" class="form-control" placeholder="例：サクラ" required="true" inputtype="zenkakukana">
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="phone_no">
        連絡先
        <span class="right badge badge-secondary ml-1">任意</span>
        <span class="text-sm">ハイフン(-)不要</span>
      </label>
      <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="例：09011112222"  inputtype="number">
    </div>
  </div>
</div>
@endsection
@section('survey_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-chalkboard-teacher mr-1"></i>
      ご希望のレッスン
    </h5>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="lesson" class="w-100">
        科目
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      @foreach($attributes['lesson_subject'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson_subject[]" class="flat-red"  required="true">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="lesson_week" class="w-100">
        レッスン可能曜日
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      @foreach($attributes['lesson_week'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson_week[]" class="flat-red" required="true">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="lesson_time" class="w-100">
        レッスン可能時間
        <span class="right badge badge-info ml-1">平日</span>
      </label>
      @foreach($attributes['lesson_time'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson_time[]" class="flat-red" >{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="lesson_time_holiday" class="w-100">
        レッスン可能時間
        <span class="right badge badge-warning ml-1">土日・祝日</span>
      </label>
      @foreach($attributes['lesson_time'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson_time_holiday[]" class="flat-red" >{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="lesson" class="w-100">
        希望校舎
      </label>
      @foreach($attributes['lesson_place'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson_place[]" class="flat-red">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
@if(!isset($user->role))
  <div class="col-12">
    <div class="form-group">
      <label for="howto" class="w-100">
        当塾をお知りになった方法は何でしょうか？
      </label>
      @foreach($attributes['howto'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="howto[]" class="flat-red"  >{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div id="howto_word_form" class="col-12 collapse">
    <div class="form-group">
      <label for="howto_word" class="w-100">
        Google検索 / Yahoo検索をお答えの方、検索ワードを教えてください。
      </label>
      <input type="text" id="howto_word" name="howto_word" class="form-control" placeholder="例：八王子 学習塾" >
    </div>
  </div>
@endif
</div>
@endsection
@section('confirm_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
  <div class="col-6 p-3">
    <ruby style="ruby-overhang: none">
      <rb><span id="name_last"></span>&nbsp;<span id="name_first"></span></rb>
      <rt><span id="kana_last"></span>&nbsp;<span id="kana_first"></span></rt>
    </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >性別</div>
  <div class="col-6 p-3"><span id="gender_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >生年月日</div>
  <div class="col-6 p-3"><span id="birth_day"></span></div>
  <div class="col-6 p-3 font-weight-bold" >学校名</div>
  <div class="col-6 p-3"><span id="school_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >学年</div>
  <div class="col-6 p-3"><span id="grade_name"></span></div>
</div>
@if(!isset($user->role))
  <div class="row">
    <div class="col-12 bg-info p-2 pl-4">
      <i class="fa fa-key mr-1"></i>
      ログイン情報
    </div>
    <div class="col-6 p-3 font-weight-bold" >メールアドレス</div>
    <div class="col-6 p-3"><span id="email"></span></div>
    <div class="col-6 p-3 font-weight-bold" >パスワード</div>
    <div class="col-6 p-3" >●●●●●●●●</div>
  </div>
  <div class="row">
    <div class="col-12 bg-info p-2 pl-4">
      <i class="fa fa-user-friends mr-1"></i>
      保護者様情報
    </div>
    <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
    <div class="col-6 p-3">
    <ruby style="ruby-overhang: none">
    <rb><span id="parent_name_last"></span>&nbsp;<span id="parent_name_first"></span></rb>
    <rt><span id="parent_kana_last"></span>&nbsp;<span id="parent_kana_first"></span></rt>
    </ruby>
    </div>
    <div class="col-6 p-3 font-weight-bold" >ご連絡先</div>
    <div class="col-6 p-3"><span id="phone_no"></span></div>
  </div>
@endif
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    ご希望のレッスン
  </div>
  <div class="col-6 p-3 font-weight-bold" >科目</div>
  <div class="col-6 p-3"><span id="lesson_subject_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >レッスン可能曜日</div>
  <div class="col-6 p-3"><span id="lesson_week_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >平日・レッスン可能時間</div>
  <div class="col-6 p-3"><span id="lesson_time_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >土日・祝日・レッスン可能時間</div>
  <div class="col-6 p-3"><span id="lesson_time_holiday_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >希望校舎</div>
  <div class="col-6 p-3"><span id="lesson_place_name"></span></div>
@if(!isset($user->role))
  <div class="col-6 p-3 font-weight-bold" >当塾をお知りになった方法は何でしょうか？</div>
  <div class="col-6 p-3"><span id="howto_name"></span></div>
  <div id="howto_word_confirm" class="col-6 p-3 font-weight-bold" >検索ワードをお答えください</div>
  <div class="col-6 p-3"><span id="howto_word"></span></div>
@endif
</div>
@endsection
@section('content')
<div id="students_register" class="direct-chat-msg">
@if(!empty($result))
    @if($result==='token_error')
    <div class="row">
      <div class="col-12">
        <h4 class="bg-danger p-3 text-sm">
          このページの有効期限が切れています。<br>
          再度、申し混みページより仮登録を行ってください。
        </h4>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <p class="my-2">
          <a href="/students/entry" role="button" class="btn btn-outline-success btn-block btn-sm float-left mr-1">
            入会お申込みはこちら
          </a>
        </p>
    </div>
  </div>
  @elseif($result==='success')
  <div class="row">
    <div class="col-12">
      <h4 class="bg-success p-3 text-sm">
        ユーザー登録が完了しました。<br>
        ログイン後、システムをご利用ください。
      </h4>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <p class="my-2">
        <a href="/login" role="button" class="btn btn-outline-success btn-block btn-sm float-left mr-1">
          ログイン
        </a>
      </p>
    </div>
  </div>
  @endif
@else
<form method="POST"  action="/students/register">
    @csrf
    <div id="register_form" class="carousel slide" data-ride="carousel" data-interval=false>
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('student_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block btn-sm float-left mr-1">
                次へ
              </a>
            </div>
            @if(isset($user->role))
            <div class="col-12 mb-1">
              <a href="/" role="button" class="btn btn-secondary btn-block btn-sm float-left mr-1">
                キャンセル
              </a>
            </div>
            @endif
          </div>
        </div>
      @if(!isset($user->role))
        <input type="hidden" name="access_key" value="{{$access_key}}" />
        <input type="hidden" name="email" value="{{$user->email}}" />
        <input type="hidden" name="student_id" value="{{$student->id}}" />
        <input type="hidden" name="parent_id" value="{{$parent->id}}" />
        <div class="carousel-item">
          @yield('account_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block btn-sm float-left mr-1">
                次へ
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block btn-sm float-left mr-1">
                戻る
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('parent_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block btn-sm float-left mr-1">
                次へ
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block btn-sm float-left mr-1">
                戻る
              </a>
            </div>
          </div>
        </div>
      @endif
        <div class="carousel-item">
          @yield('survey_form')
          <div class="row">
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block btn-sm float-left mr-1">
                  お申込み内容確認
                </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block btn-sm float-left mr-1">
                戻る
              </a>
            </div>
            @if(isset($user->role))
            <div class="col-12 mb-1">
              <a href="/" role="button" class="btn btn-secondary btn-block btn-sm float-left mr-1">
                キャンセル
              </a>
            </div>
            @endif
          </div>
        </div>
        <div class="carousel-item" id="confirm_form">
          @yield('confirm_form')
          <div class="row">
            <div class="col-12 mb-1">
                <button type="submit" class="btn btn-primary btn-block" accesskey="students_create">
                    入力内容を登録する
                </button>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block btn-sm float-left mr-1">
                戻る
              </a>
            </div>
            @if(isset($user->role))
            <div class="col-12 mb-1">
              <a href="/" role="button" class="btn btn-secondary btn-block btn-sm float-left mr-1">
                キャンセル
              </a>
            </div>
            @endif

          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script>

$(function(){
  var form_data = util.getLocalData('register_form');

  base.pageSettinged("register_form", form_data);
  //Google検索・Yahoo検索と答えた場合、検索ワードフォームを表示
  $('input[type="checkbox"][name="howto[]"]').on('ifChanged', function(e){
    var is_google = $('input[type="checkbox"][name="howto[]"][value="google"]').prop("checked");
    var is_yahoo = $('input[type="checkbox"][name="howto[]"][value="yahoo"]').prop("checked");
    if(is_google || is_yahoo){
      $("#howto_word_form").collapse("show");
      $("#howto_word_confirm").collapse("show");
    }
    else {
      $("#howto_word_form").collapse("hide");
      $("#howto_word_confirm").collapse("hide");
    }
  });

  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('register_form .carousel-item.active')){
      $("form").submit();
    }
  });
  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('register_form .carousel-item.active')){
      var form_data = front.getFormValue('register_form');
      util.setLocalData('register_form', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $('#register_form').carousel('next');
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#register_form').carousel('prev');
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    form_data["email"] = $("input[name=email]").val();
    if(form_data["gender"]){
      form_data["gender_name"] = $("label[for='"+$("input[name='gender']:checked").attr("id")+"']").text().trim();
    }
    if(form_data["grade"]){
      form_data["grade_name"] = $('select[name=grade] option:selected').text().trim();
    }
    var _names = ["lesson_subject", "lesson_week", "lesson_time", "lesson_time_holiday", "lesson_place", "howto"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    return form_data;
  }
});
</script>
@endif
@endsection
