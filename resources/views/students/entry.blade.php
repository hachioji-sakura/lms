@extends('layouts.loginbox')
@section('title', '体験授業申し込み')
@section('content')
<div id="students_entry">
<form method="POST"  action="students/entry">
    @csrf
    <div class="row">
      <div class="col-12">
        <h5 class="bg-info p-1 pl-2 mb-4">
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
          <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：山田" required="true" inputtype="zenkaku">
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
      {{-- カナ・生年月日はファーストステップでは不要
      <div class="col-6 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="kana_last">
            氏（カナ）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="kana_last" name="kana_last" class="form-control" placeholder="例：ヤマダ" required="true" inputtype="zenkakukana">
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
          <input id="birth_day" type="text" class="form-control{{ $errors->has('birth_day') ? ' is-invalid' : '' }}" name="birth_day" value="{{ old('birth_day') }}" inputtype="date" plaeholder="例：2000/01/01" required="true">
          @if ($errors->has('birth_day'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('birth_day') }}</strong>
              </span>
          @endif
        </div>
      </div>
      --}}
      <div class="col-12 col-lg-6 col-md-6">
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
            @foreach($grade as $item)
              <label class="mx-2">
                <input type="radio" name="course" class="flat-red"  value="{{ $item->attribute_value }}" >{{$item->attribute_name}}
              </label>
            @endforeach
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <h5 class="bg-info p-1 pl-2 mb-4">
          保護者様情報
        </h5>
      </div>
    </div>
    <div class="row">
      {{-- 保護者氏名・カナはファーストステップでは不要
      <div class="col-6 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_last">
            氏
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：山田" required="true" inputtype="zenkaku">
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
      <div class="col-6 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="kana_last">
            氏（カナ）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="kana_last" name="kana_last" class="form-control" placeholder="例：ヤマダ" required="true" inputtype="zenkakukana">
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
      --}}
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
          <label for="phone_no">
            連絡先
            <span class="right badge badge-secondary ml-1">ハイフン(-)不要</span>
          </label>
          <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="例：09011112222" required="true" inputtype="number">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <h5 class="bg-info p-1 pl-2 mb-4">
          ご希望のレッスン
        </h5>
      </div>
    </div>
    {{-- 塾の場合はレッスンは不要 / 英会話のとき
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="lesson" class="w-100">
            レッスン
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <label class="mx-2">
            <input type="radio" name="lesson" class="flat-red"  value="one" checked>マンツーマン
          </label>
          <label class="mx-2">
            <input type="radio" name="lesson" class="flat-red" value="group">グループレッスン
          </label>
          <label class="mx-2">
            <input type="radio" name="lesson" class="flat-red" value="both">どちらでも
          </label>
        </div>
      </div>
      --}}
      {{-- 高校生向けの科目を表示すべきかどうか・・--}}
      <div class="col-12">
        <div class="form-group">
          <label for="lesson" class="w-100">
            科目
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
            <label class="mx-2">
              <input type="checkbox" name="subject" class="flat-red"  value="english" >英語
            </label>
            <label class="mx-2">
              <input type="checkbox" name="subject" class="flat-red" value="math">算数・数学
            </label>
            <label class="mx-2">
              <input type="checkbox" name="subject" class="flat-red"  value="japanese" >国語
            </label>
            <label class="mx-2">
              <input type="checkbox" name="subject" class="flat-red" value="science">理科
            </label>
            <label class="mx-2">
              <input type="checkbox" name="subject" class="flat-red" value="society">社会
            </label>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="lesson_week" class="w-100">
            レッスン可能曜日
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_week" class="flat-red"  value="mon" >月
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_week" class="flat-red"  value="tue" >火
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_week" class="flat-red"  value="wed" >水
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_week" class="flat-red"  value="thi" >木
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_week" class="flat-red"  value="fri" >金
            </label>
            <label class="mx-2" style="color:blue;">
              <input type="checkbox" name="lesson_week" class="flat-red"  value="sat" >土
            </label>
            <label class="mx-2" style="color:red;">
              <input type="checkbox" name="lesson_week" class="flat-red"  value="sun" >日
            </label>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="lesson_time" class="w-100">
            レッスン可能時間
            <span class="right badge badge-info ml-1">平日</span>
          </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red"  value="am" >午前中
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="12_13">12:00～14:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="13_14">12:00～14:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red"  value="14_15" >14:00～16:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red"  value="15_16" >14:00～16:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="16_17">16:00～18:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="17_18">16:00～18:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="18_19">18:00～20:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="19_20">18:00～20:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="20_21">20:00～22:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time" class="flat-red" value="21_22">20:00～22:00
            </label>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="lesson_time_holiday" class="w-100">
            レッスン可能時間
            <span class="right badge badge-warning ml-1">土日・祝日</span>
          </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time_holiday" class="flat-red"  value="am" >午前中
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time_holiday" class="flat-red" value="12_14">12:00～14:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time_holiday" class="flat-red"  value="14_16" >14:00～16:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time_holiday" class="flat-red" value="16_18">16:00～18:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time_holiday" class="flat-red" value="18_20">18:00～20:00
            </label>
            <label class="mx-2">
              <input type="checkbox" name="lesson_time_holiday" class="flat-red" value="20_22">20:00～22:00
            </label>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="lesson" class="w-100">
            希望校舎
          </label>
          <label class="mx-2">
            <input type="checkbox" name="place" class="flat-red" value="hinotoyota">日野豊田校
          </label>
          <label class="mx-2">
            <input type="checkbox" name="place" class="flat-red"  value="north" >八王子北口校
          </label>
          <label class="mx-2">
            <input type="checkbox" name="place" class="flat-red" value="south">八王子南口校
          </label>
          <!-- 塾の場合不要/英会話別ページ-->
            <label class="mx-2">
              <input type="checkbox" name="place" class="flat-red"  value="koyasu" >子安校
            </label>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="howto" class="w-100">
            本ページをお知りになった方法は何でしょうか？
          </label>
            <label class="mx-2">
              <input type="checkbox" name="howto" class="flat-red"  value="google" >Google検索
            </label>
            <label class="mx-2">
              <input type="checkbox" name="howto" class="flat-red" value="yahoo">Yahoo検索
            </label>
            <label class="mx-2">
              <input type="checkbox" name="howto" class="flat-red"  value="signboard" >看板
            </label>
            <label class="mx-2">
              <input type="checkbox" name="howto" class="flat-red" value="newspaper">新聞折込チラシ
            </label>
            <label class="mx-2">
              <input type="checkbox" name="howto" class="flat-red" value="flyer">投函チラシ
            </label>
            <label class="mx-2">
              <input type="checkbox" name="howto" class="flat-red" value="dm">ダイレクトメール
            </label>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="howto" class="w-100">
            Google検索 / Yahoo検索をお答えの方、検索ワードを教えてください。
          </label>
          <input type="text" id="email" name="email" class="form-control" placeholder="例：八王子 学習塾" required="true" >
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
});
</script>

@endsection
