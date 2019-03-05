@section('parent_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-friends mr-1"></i>
    お客様情報
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
      <label for="email">
        メールアドレス
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email">
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

@section('student_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="student_name_last">
        氏
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" name="student_name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku" @isset($student) value="{{$student->name_last}}" @endisset>
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="student_name_first">
        名
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" name="student_name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku" @isset($student) value="{{$student->name_first}}" @endisset>
    </div>
  </div>
{{--
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="kana_last">
        氏（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" name="student_kana_last" class="form-control" placeholder="例：ハチオウジ" required="true" inputtype="zenkakukana">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="kana_first">
        名（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" name="student_kana_first" class="form-control" placeholder="例：タロウ" required="true" inputtype="zenkakukana">
    </div>
  </div>
  <div class="col-12">
    @component('components.select_birthday', ['item' => []])
    @endcomponent
  </div>
--}}
  <div class="col-12 col-lg-6 col-md-6">
    @component('components.select_gender', ['item' => []])
    @endcomponent
  </div>
  @component('students.forms.school', ['item' => $student, 'attributes' => $attributes]) @endcomponent

  <div class="col-12">
    <div class="form-group">
      <label for="email">
        メールアドレス
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email">
    </div>
  </div>

</div>
@endsection

@section('survey_form')
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label for="howto" class="w-100">
        その他、ご要望がございましたらこちらにご入力ください
        <span class="right badge badge-secondary ml-1">任意</span>
      </label>
      <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：宿題・提出物を忘れることが多いため、提出できるように改善していきたい。" ></textarea>
    </div>
  </div>
@if(!isset($user->role))
  <div class="col-12">
    <div class="form-group">
      <label for="howto" class="w-100">
        当塾をお知りになった方法は何でしょうか？
        <span class="right badge badge-secondary ml-1">任意</span>
      </label>
      @foreach($attributes['howto'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="howto[]" class="icheck flat-green"  onChange="howto_checkbox_change(this)">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12 collapse howto_word_form">
    <div class="form-group">
      <label for="howto_word" class="w-100">
        Google検索 / Yahoo検索をお答えの方、検索ワードを教えてください。
        <span class="right badge badge-secondary ml-1">任意</span>
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
      $(".howto_word_form").collapse("show");
      $(".howto_word_confirm").collapse("show");
    }
    else {
      $(".howto_word_form").collapse("hide");
      $(".howto_word_confirm").collapse("hide");
    }
  }
  </script>
@endif
</div>
@endsection

@section('trial_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="lesson" class="w-100">
        ご希望のレッスン
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      @foreach($attributes['lesson'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson[]" class="icheck flat-green" required="true"
        @if(isset($_edit) && $item->user->has_tag('lesson', $index)===true)
       checked
        @endif
        onChange="lesson_checkbox_change(this)">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <script>
  function lesson_checkbox_change(obj){
    var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
    var is_english = $('input[type="checkbox"][name="lesson[]"][value="2"]').prop("checked");
    var is_piano = $('input[type="checkbox"][name="lesson[]"][value="3"]').prop("checked");
    if(is_school){
      $(".subject_form").show();
      $(".subject_confirm").show();
    }
    else {
      $(".subject_form").hide();
      $(".subject_confirm").hide();
    }
    if(is_english){
      $(".english_form").show();
      $(".english_confirm").show();
    }
    else {
      $(".english_form").hide();
      $(".english_confirm").hide();
    }
    if(is_piano){
      $(".piano_form").show();
      $(".piano_confirm").show();
    }
    else {
      $(".piano_form").hide();
      $(".piano_confirm").hide();
    }
    grade_select_change();
  }
  </script>
  <div class="row form-group p-2">
    <div class="col-12 mt-2 col-md-4">
        <label for="start_date" class="w-100">
          第１希望日
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <input type="text" name="trial_date1" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}">
    </div>
    <div class="col-12 mt-2 col-md-8">
      <label for="start_date" class="w-100">
        時間帯
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="trial_start_time1" class="form-control float-left mr-1 w-40" required="true">
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
      <div class="w-10 text-center float-left mx-2">～</div>
      <select name="trial_end_time1" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time1" greater_error="時間帯範囲が間違っています" not_equal="trial_start_time1" not_equal_error="時間帯範囲が間違っています" >
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
    </div>
  </div>
  <div class="row form-group p-2">
    <div class="col-12 mt-2 col-md-4">
        <label for="start_date" class="w-100">
          第２希望日
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <input type="text" name="trial_date2" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}">
    </div>
    <div class="col-12 mt-2 col-md-8">
      <label for="start_date" class="w-100">
        時間帯
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="trial_start_time2" class="form-control float-left mr-1 w-40" required="true">
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
      <div class="w-10 text-center float-left mx-2">～</div>
      <select name="trial_end_time2" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time2" greater_error="時間帯範囲が間違っています" not_equal="trial_start_time2" not_equal_error="時間帯範囲が間違っています" >
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="lesson_place" class="w-100">
        ご希望の校舎
        <span class="right badge badge-secondary ml-1">任意</span>
      </label>
      @foreach($attributes['lesson_place'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson_place[]" class="icheck flat-green">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
</div>
@endsection
@section('subject_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    授業に関するご要望について
  </div>
  <div class="col-12 subject_form">
    <div class="form-group">
      <label for="subject_level" class="w-100">
        ご希望の科目
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <table class="table table-striped">
      <tr class="bg-gray">
        <!-- th class="p-1">学年</th -->
        <th class="p-1 text-sm text-center">分類</th>
        <th class="p-1 text-sm text-center">科目</th>
        <th class="p-1 text-sm text-center">
          希望しない
        </th>
        <th class="p-1 text-sm text-center">
          補習授業
        </th>
        <th class="p-1 text-sm text-center">
          受験対策
        </th>
      </tr>
      @foreach(config('charge_subjects') as $grade => $subject_group)
        @foreach($subject_group as $subject => $subject_data)
          <?php $l1 = $loop->index; ?>
          @isset($subject_data['items'])
            @foreach($subject_data['items'] as $subject => $subject_name)
              <tr class="grade-subject" alt="{{$grade}}">
              @if($l1===0)
              <!-- th class="p-1 text-center bg-gray" rowspan=100>{{$grade}}</th -->
              @endif
              @if($loop->index===0)
              <th class="p-1 text-center bg-gray bd-light bd-r" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
              @endif
              <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
              @foreach($attributes['lesson_subject_level'] as $index => $name)
                <td class="p-1 text-center">
                <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="icheck
                  @if($loop->index===0)
                  flat-grey
                  @else
                  flat-green
                  @endif
                 subject_level"  required="true"
                @if(isset($item) && isset($item->user) && $item->user->has_tag($subject.'_level', $index)===true || (!isset($_edit) && $loop->index == 0))
                 checked
                @endif
                >
              </td>
              @endforeach
            </tr>
            @endforeach
          @else
            <tr class="grade-subject" alt="{{$grade}}">
            @if($loop->index===0)
            <!-- th class="p-1 text-center bg-gray" rowspan={{count($subject_group)}}>{{$grade}}</th -->
            @endif
            <th class="p-1 text-center bg-gray bd-light bd-r">{{$subject_data['name']}}</th>
            <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
            @foreach($attributes['lesson_subject_level'] as $index => $name)
              <td class="text-center">
              <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="icheck
              @if($loop->index===0)
              flat-grey
              @else
              flat-green
              @endif
               subject_level"  required="true"
              @if((isset($_edit) && isset($item) && isset($item->user) && $item->user->has_tag($subject.'_level', $index)===true) || (!isset($_edit) && $loop->index == 0))
               checked
              @endif
              >
            </td>
            @endforeach
            </tr>
          @endisset
        @endforeach
      @endforeach
      </table>
    </div>
  </div>
  <div class="col-12 english_form">
    <div class="form-group">
      <label for="english_teacher" class="w-100">
        英会話講師のご希望はございますか？
        <span class="right badge badge-secondary ml-1">任意</span>
      </label>
      @foreach($attributes['english_teacher'] as $index => $name)
      <label class="mx-2">
        <input type="radio" id="english_teacher_{{$index}}" value="{{ $index }}" name="english_teacher" class="icheck flat-green">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12 piano_form">
    <div class="form-group">
      <label for="piano_level" class="w-100">
        ピアノのご経験について教えてください
        <span class="right badge badge-secondary ml-1">任意</span>
      </label>
      @foreach($attributes['piano_level'] as $index => $name)
      <label class="mx-2">
        <input type="radio" id="piano_level_{{$index}}" value="{{ $index }}" name="piano_level" class="icheck flat-green">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
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
      <rb><span id="student_name_last"></span>&nbsp;<span id="student_name_first"></span></rb>
      <!-- rt><span id="student_kana_last"></span>&nbsp;<span id="student_kana_first"></span></rt -->
    </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >性別</div>
  <div class="col-6 p-3"><span id="gender_name"></span></div>
  {{--
    <div class="col-6 p-3 font-weight-bold" >生年月日</div>
    <div class="col-6 p-3"><span id="birth_day"></span></div>
  --}}
  <div class="col-6 p-3 font-weight-bold" >学年</div>
  <div class="col-6 p-3"><span id="grade_name"></span></div>
  <div class="col-6 p-3 font-weight-bold school_name_confirm" >学校名</div>
  <div class="col-6 p-3 school_name_confirm"><span id="school_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >メールアドレス</div>
  <div class="col-6 p-3"><span id="email"></span></div>
</div>
{{--
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
--}}
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  <div class="col-6 p-3 font-weight-bold" >第１希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time1"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第２希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time2"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご希望の校舎</div>
  <div class="col-6 p-3"><span id="lesson_place_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご希望のレッスン</div>
  <div class="col-6 p-3"><span id="lesson_name"></span></div>
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    授業に関するご要望について
  </div>
  <div class="col-12 p-3 font-weight-bold subject_confirm">
    ご希望の科目
  </div>
  <div class="col-12 subject_confirm">
    <div class="form-group">
      <table class="table table-striped">
      <tr class="bg-gray">
        <th class="p-1 text-sm text-center">分類</th>
        <th class="p-1 text-sm text-center">科目</th>
        <th class="p-1 text-sm text-center">
          補習授業
        </th>
        <th class="p-1 text-sm text-center">
          受験対策
        </th>
      </tr>
      @foreach(config('charge_subjects') as $grade => $subject_group)
        @foreach($subject_group as $subject => $subject_data)
          <?php $l1 = $loop->index; ?>
          @isset($subject_data['items'])
            @foreach($subject_data['items'] as $subject => $subject_name)
              <tr class="grade-subject" alt="{{$grade}}">
              @if($loop->index===0)
              <th class="p-1 text-center bg-gray" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
              @endif
              <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
              @foreach($attributes['lesson_subject_level'] as $index => $name)
                @if($loop->index == 0)
                  @continue
                @endif
                <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
                  -
                </td>
              </td>
              @endforeach
            </tr>
            @endforeach
          @else
            <tr class="grade-subject" alt="{{$grade}}">
            <th class="p-1 text-center bg-gray">{{$subject_data['name']}}</th>
            <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
            @foreach($attributes['lesson_subject_level'] as $index => $name)
              @if($loop->index == 0)
                @continue
              @endif
              <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
                -
              </td>
            @endforeach
            </tr>
          @endisset
        @endforeach
      @endforeach
      </table>
    </div>
  </div>
  <div class="col-6 p-3 font-weight-bold english_confirm" >ご希望の英会話講師</div>
  <div class="col-6 p-3 english_confirm"><span id="english_teacher_name"></span></div>
  <div class="col-6 p-3 font-weight-bold piano_confirm" >ピアノのご経験について</div>
  <div class="col-6 p-3 piano_confirm"><span id="piano_level_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご要望について</div>
  <div class="col-6 p-3"><span id="remark"></span></div>
  <div class="col-6 p-3 font-weight-bold" >当塾をお知りになった方法は何でしょうか？</div>
  <div class="col-6 p-3"><span id="howto_name"></span></div>
  <div class="col-6 p-3 font-weight-bold howto_word_confirm" >検索ワードをお答えください</div>
  <div class="col-6 p-3 howto_word_confirm"><span id="howto_word"></span></div>
</div>
@endsection
