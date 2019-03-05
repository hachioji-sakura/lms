{{--
parents.create_formを使うので、利用しない
  --}}
@section('student_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-graduate mr-1"></i>
      生徒様情報
    </h5>
  </div>
  @component('students.forms.name', ['item' => $student, 'prefix' => '']) @endcomponent
  @component('students.forms.kana', ['item' => $student, 'prefix' => '']) @endcomponent
  <div class="col-12">
    @component('components.select_birthday', ['item' => $student])
    @endcomponent
  </div>
  <div class="col-12">
    @component('components.select_gender', ['item' => $student])
    @endcomponent
  </div>
  @component('students.forms.school', ['item' => $student, 'attributes' => $attributes]) @endcomponent
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
