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
  <div class="col-12">
    @component('components.select_birthday', ['item' => $student])
    @endcomponent
  </div>
  <div class="col-12">
    @component('components.select_gender', ['item' => $student])
    @endcomponent
  </div>
  <div class="col-12 col-lg-9 col-md-9">
    <div class="form-group">
      <label for="school_name" class="w-100">
        学校名
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="school_name" name="school_name" class="form-control" placeholder="例：八王子市立サクラ中学校" required="true">
    </div>
  </div>
  <div class="col-12 col-lg-3 col-md-3">
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
      <label for="lesson_subject" class="w-100">
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
        <input type="checkbox" value="{{ $index }}" name="howto[]" class="flat-red"  onChange="howto_checkbox_change(this)">{{$name}}
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
  <script>
  function howto_checkbox_change(obj){
    //Google検索・Yahoo検索と答えた場合、検索ワードフォームを表示
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
  }
  </script>
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
